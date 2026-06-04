<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailPanen;
use App\Models\InstruksiPenyimpanan;
use App\Models\JenisGabah;
use App\Models\KelompokTani;
use App\Models\Panen;
use App\Models\Petani;
use App\Models\SlotLumbung;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PanenController extends Controller
{
    /**
     * Daftar semua data panen yang sudah diinput.
     * Bisa difilter berdasarkan petani, kelompok, atau rentang tanggal.
     */
    public function index(Request $request)
    {
        $query = Panen::with([
            'petani.kelompokTani',
            'detailPanen.jenisGabah',
            'instruksiPenyimpanan',
        ]);

        // Filter berdasarkan petani
        if ($request->filled('id_petani')) {
            $query->where('id_petani', $request->id_petani);
        }

        // Filter berdasarkan kelompok tani
        if ($request->filled('id_kelompok')) {
            $query->whereHas('petani', fn ($q) => $q->where('id_kelompok', $request->id_kelompok));
        }

        // Filter berdasarkan rentang tanggal panen
        if ($request->filled('dari')) {
            $query->where('tanggal_panen', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->where('tanggal_panen', '<=', $request->sampai);
        }

        // Filter berdasarkan status instruksi: ada yang masih pending atau semua selesai
        if ($request->filled('status_instruksi')) {
            if ($request->status_instruksi === 'pending') {
                $query->whereHas('instruksiPenyimpanan', fn ($q) => $q->where('status', 'pending'));
            } elseif ($request->status_instruksi === 'selesai') {
                $query->whereDoesntHave('instruksiPenyimpanan', fn ($q) => $q->where('status', 'pending'));
            }
        }

        $panenList    = $query->orderByDesc('tanggal_panen')->paginate(15)->withQueryString();
        $petaniList   = Petani::orderBy('nama_petani')->get();
        $kelompokList = KelompokTani::orderBy('nama_kelompok')->get();

        // Hitung total panen bulan ini untuk info card
        $totalPanenBulanIni = Panen::whereMonth('tanggal_panen', Carbon::now()->month)
            ->whereYear('tanggal_panen', Carbon::now()->year)
            ->count();

        return view('admin.panen.index', compact(
            'panenList',
            'petaniList',
            'kelompokList',
            'totalPanenBulanIni',
        ));
    }

    /**
     * Form input panen baru.
     *
     * Admin memilih petani dan mengisi tanggal panen.
     * Detail per jenis gabah diisi secara dinamis (bisa lebih dari satu jenis).
     */
    public function create()
    {
        $petaniList     = Petani::with('kelompokTani')->orderBy('nama_petani')->get();
        $jenisGabahList = JenisGabah::orderBy('nama_jenis')->get();

        // Persentase lumbung dari konfigurasi (default 3%)
        $persenLumbung = config('silpd.persen_lumbung', 3);

        return view('admin.panen.create', compact(
            'petaniList',
            'jenisGabahList',
            'persenLumbung',
        ));
    }

    /**
     * Simpan data panen baru.
     *
     * Alur lengkap dalam satu transaksi:
     * 1. Buat record panen (header)
     * 2. Untuk tiap jenis gabah:
     *    a. Buat detail_panen dengan jumlah_panen
     *    b. Hitung jumlah gabah untuk lumbung (jumlah_panen × % lumbung)
     *    c. Cari slot yang sesuai (kapasitas tersedia mencukupi)
     *    d. Buat instruksi_penyimpanan → dikirim ke pengelola
     * 3. Jika ada jenis gabah yang tidak dapat slot, catat sebagai peringatan
     *    (tidak membatalkan seluruh transaksi — panen tetap tersimpan)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_petani'                    => ['required', 'exists:petani,id_petani'],
            'tanggal_panen'                => ['required', 'date', 'before_or_equal:today'],
            'detail'                       => ['required', 'array', 'min:1'],
            'detail.*.id_jenis_gabah'      => ['required', 'exists:jenis_gabah,id_jenis_gabah'],
            'detail.*.jumlah_panen'        => ['required', 'numeric', 'min:1'],
        ], [
            'id_petani.required'                => 'Petani wajib dipilih.',
            'id_petani.exists'                  => 'Petani tidak ditemukan.',
            'tanggal_panen.required'            => 'Tanggal panen wajib diisi.',
            'tanggal_panen.before_or_equal'     => 'Tanggal panen tidak boleh melewati hari ini.',
            'detail.required'                   => 'Minimal satu jenis gabah wajib diisi.',
            'detail.*.id_jenis_gabah.required'  => 'Jenis gabah wajib dipilih.',
            'detail.*.id_jenis_gabah.exists'    => 'Jenis gabah tidak ditemukan.',
            'detail.*.jumlah_panen.required'    => 'Jumlah panen wajib diisi.',
            'detail.*.jumlah_panen.min'         => 'Jumlah panen minimal 1 kg.',
        ]);

        // Cegah duplikasi jenis gabah dalam satu input panen
        $idJenisGabahList = array_column($request->detail, 'id_jenis_gabah');
        if (count($idJenisGabahList) !== count(array_unique($idJenisGabahList))) {
            return back()->withInput()->withErrors([
                'detail' => 'Jenis gabah tidak boleh duplikat dalam satu data panen.',
            ]);
        }

        $persenLumbung = config('silpd.persen_lumbung', 3);
        $peringatanSlot = []; // Kumpulkan peringatan jenis gabah yang tidak dapat slot

        DB::transaction(function () use ($request, $persenLumbung, &$peringatanSlot) {

            // 1. Buat header panen
            $panen = Panen::create([
                'id_petani'     => $request->id_petani,
                'tanggal_panen' => $request->tanggal_panen,
            ]);

            foreach ($request->detail as $item) {
                $jumlahPanen   = (float) $item['jumlah_panen'];
                $jumlahLumbung = round($jumlahPanen * ($persenLumbung / 100), 2);

                // 2a. Buat detail panen
                $detailPanen = DetailPanen::create([
                    'id_panen'       => $panen->id_panen,
                    'id_jenis_gabah' => $item['id_jenis_gabah'],
                    'jumlah_panen'   => $jumlahPanen,
                ]);

                // 2b & 2c. Cari slot yang sesuai untuk jumlah_lumbung
                if ($jumlahLumbung > 0) {
                    $slot = $this->cariSlotYangSesuai($jumlahLumbung);

                    if ($slot) {
                        // 2d. Buat instruksi penyimpanan untuk pengelola
                        InstruksiPenyimpanan::create([
                            'id_detail'         => $detailPanen->id_detail,
                            'id_slot'           => $slot->id_slot,
                            'jumlah'            => $jumlahLumbung,
                            'tanggal_instruksi' => Carbon::today(),
                            'status'            => 'pending',
                        ]);
                    } else {
                        // Tidak ada slot yang cukup — catat sebagai peringatan
                        $namaJenis = JenisGabah::find($item['id_jenis_gabah'])?->nama_jenis ?? 'Tidak diketahui';
                        $peringatanSlot[] = "Gabah {$namaJenis} ({$jumlahLumbung} kg): tidak ada slot dengan kapasitas mencukupi. Instruksi penyimpanan belum dibuat.";
                    }
                }
            }
        });

        // Redirect dengan pesan sukses dan peringatan (jika ada)
        $successMsg = 'Data panen berhasil disimpan. Instruksi penyimpanan telah dikirim ke pengelola.';

        if (! empty($peringatanSlot)) {
            return redirect()
                ->route('admin.panen.index')
                ->with('success', $successMsg)
                ->with('warning_list', $peringatanSlot);
        }

        return redirect()
            ->route('admin.panen.index')
            ->with('success', $successMsg);
    }

    /**
     * Detail satu data panen.
     *
     * Menampilkan:
     * - Header panen (petani, tanggal)
     * - Semua detail per jenis gabah (jumlah panen dan jumlah lumbung 3%)
     * - Status instruksi penyimpanan per jenis gabah
     * - Rekomendasi jika ada jenis gabah yang belum punya instruksi
     */
    public function show(int $id)
    {
        $panen = Panen::with([
            'petani.kelompokTani',
            'detailPanen.jenisGabah',
            'detailPanen.instruksiPenyimpanan.slotLumbung.lumbung',
            'detailPanen.penyimpananGabah.slotLumbung.lumbung',
        ])->findOrFail($id);

        $persenLumbung = config('silpd.persen_lumbung', 3);

        // Hitung ringkasan per detail panen
        $ringkasanDetail = $panen->detailPanen->map(function ($detail) use ($persenLumbung) {
            $jumlahLumbung  = round($detail->jumlah_panen * ($persenLumbung / 100), 2);
            $instruksi      = $detail->instruksiPenyimpanan->first();
            $penyimpanan    = $detail->penyimpananGabah->first();

            return (object)[
                'id_detail'           => $detail->id_detail,
                'jenisGabah'          => $detail->jenisGabah,
                'jumlah_panen'        => $detail->jumlah_panen,
                'jumlah_lumbung'      => $jumlahLumbung,
                'instruksiPenyimpanan' => $detail->instruksiPenyimpanan,
                'penyimpananGabah'    => $detail->penyimpananGabah,
                'status_instruksi'    => $instruksi?->status ?? 'belum_dibuat',
                'ada_instruksi'       => ! is_null($instruksi),
                'sudah_disimpan'      => ! is_null($penyimpanan),
            ];
        });

        $totalPanen   = $panen->detailPanen->sum('jumlah_panen');
        $totalLumbung = round($totalPanen * ($persenLumbung / 100), 2);

        return view('admin.panen.show', compact(
            'panen',
            'ringkasanDetail',
            'totalPanen',
            'totalLumbung',
            'persenLumbung',
        ));
    }

    /**
     * Buat ulang instruksi penyimpanan untuk detail panen yang belum
     * memiliki instruksi (karena saat input pertama tidak ada slot tersedia).
     *
     * Admin memilih slot secara manual dari dropdown.
     */
    public function buatInstruksiManual(Request $request, int $idDetail)
    {
        $detailPanen = DetailPanen::with([
            'panen.petani',
            'jenisGabah',
            'instruksiPenyimpanan',
        ])->findOrFail($idDetail);

        // Cek apakah instruksi sudah ada
        if ($detailPanen->instruksiPenyimpanan->isNotEmpty()) {
            return back()->withErrors([
                'instruksi' => 'Instruksi penyimpanan untuk jenis gabah ini sudah ada.',
            ]);
        }

        $request->validate([
            'id_slot' => ['required', 'exists:slot_lumbung,id_slot'],
        ], [
            'id_slot.required' => 'Slot penyimpanan wajib dipilih.',
            'id_slot.exists'   => 'Slot tidak ditemukan.',
        ]);

        $persenLumbung = config('silpd.persen_lumbung', 3);
        $jumlahLumbung = round($detailPanen->jumlah_panen * ($persenLumbung / 100), 2);

        // Cek kapasitas slot yang dipilih
        $slot = SlotLumbung::findOrFail($request->id_slot);
        if ($slot->kapasitas_tersedia < $jumlahLumbung) {
            return back()->withErrors([
                'id_slot' => "Kapasitas slot {$slot->kode_slot} tidak mencukupi. " .
                    "Tersedia: {$slot->kapasitas_tersedia} kg, dibutuhkan: {$jumlahLumbung} kg.",
            ]);
        }

        InstruksiPenyimpanan::create([
            'id_detail'         => $detailPanen->id_detail,
            'id_slot'           => $slot->id_slot,
            'jumlah'            => $jumlahLumbung,
            'tanggal_instruksi' => Carbon::today(),
            'status'            => 'pending',
        ]);

        return redirect()
            ->route('admin.panen.show', $detailPanen->id_panen)
            ->with('success', "Instruksi penyimpanan untuk {$detailPanen->jenisGabah->nama_jenis} berhasil dibuat.");
    }

    /**
     * Form pilih slot untuk instruksi manual.
     * Dipanggil ketika detail panen belum punya instruksi.
     */
    public function formInstruksiManual(int $idDetail)
    {
        $detailPanen = DetailPanen::with([
            'panen.petani',
            'jenisGabah',
            'instruksiPenyimpanan',
        ])->findOrFail($idDetail);

        if ($detailPanen->instruksiPenyimpanan->isNotEmpty()) {
            return redirect()
                ->route('admin.panen.show', $detailPanen->id_panen)
                ->with('info', 'Instruksi untuk jenis gabah ini sudah ada.');
        }

        $persenLumbung  = config('silpd.persen_lumbung', 3);
        $jumlahLumbung  = round($detailPanen->jumlah_panen * ($persenLumbung / 100), 2);

        // Tampilkan hanya slot yang kapasitasnya mencukupi
        $slotTersedia = SlotLumbung::where('kapasitas_tersedia', '>=', $jumlahLumbung)
            ->with('lumbung')
            ->orderByDesc('kapasitas_tersedia')
            ->get();

        return view('admin.panen.instruksi-manual', compact(
            'detailPanen',
            'jumlahLumbung',
            'slotTersedia',
        ));
    }

    /**
     * Form edit data panen.
     *
     * Hanya bisa diedit jika:
     * - Belum ada gabah yang masuk ke penyimpanan (status penyimpanan belum ada)
     * - Semua instruksi masih pending (belum dikonfirmasi pengelola)
     *
     * Yang boleh diedit:
     * - Tanggal panen
     * - Detail panen (jumlah per jenis gabah)
     * - Instruksi akan di-generate ulang berdasarkan data baru
     */
    public function edit(int $id)
    {
        $panen = Panen::with([
            'petani',
            'detailPanen.jenisGabah',
            'detailPanen.instruksiPenyimpanan',
            'detailPanen.penyimpananGabah',
        ])->findOrFail($id);

        // Cek apakah boleh diedit
        $adaInstruksiSelesai = $panen->detailPanen->flatMap->instruksiPenyimpanan
            ->where('status', 'selesai')
            ->isNotEmpty();

        if ($adaInstruksiSelesai) {
            return redirect()
                ->route('admin.panen.show', $id)
                ->withErrors([
                    'edit' => 'Data panen tidak dapat diedit karena sebagian instruksi penyimpanan sudah dikonfirmasi oleh pengelola.',
                ]);
        }

        $adaPenyimpanan = $panen->detailPanen->flatMap->penyimpananGabah->isNotEmpty();

        if ($adaPenyimpanan) {
            return redirect()
                ->route('admin.panen.show', $id)
                ->withErrors([
                    'edit' => 'Data panen tidak dapat diedit karena gabah sudah masuk ke slot penyimpanan.',
                ]);
        }

        $petaniList     = Petani::with('kelompokTani')->orderBy('nama_petani')->get();
        $jenisGabahList = JenisGabah::orderBy('nama_jenis')->get();
        $persenLumbung  = config('silpd.persen_lumbung', 3);

        return view('admin.panen.edit', compact(
            'panen',
            'petaniList',
            'jenisGabahList',
            'persenLumbung',
        ));
    }

    /**
     * Simpan perubahan data panen.
     *
     * Alur:
     * 1. Validasi input baru
     * 2. Hapus instruksi lama yang masih pending
     * 3. Update header panen & detail panen
     * 4. Generate instruksi penyimpanan baru berdasarkan detail yang di-update
     * 5. Kumpulkan peringatan jika ada detail yang tidak dapat slot
     */
    public function update(Request $request, int $id)
    {
        $panen = Panen::with([
            'detailPanen.instruksiPenyimpanan',
            'detailPanen.penyimpananGabah',
        ])->findOrFail($id);

        // Cek apakah boleh diedit (validasi ulang)
        $adaInstruksiSelesai = $panen->detailPanen->flatMap->instruksiPenyimpanan
            ->where('status', 'selesai')
            ->isNotEmpty();

        if ($adaInstruksiSelesai) {
            return back()->withErrors([
                'edit' => 'Data panen tidak dapat diedit karena sebagian instruksi penyimpanan sudah dikonfirmasi oleh pengelola.',
            ]);
        }

        $adaPenyimpanan = $panen->detailPanen->flatMap->penyimpananGabah->isNotEmpty();

        if ($adaPenyimpanan) {
            return back()->withErrors([
                'edit' => 'Data panen tidak dapat diedit karena gabah sudah masuk ke slot penyimpanan.',
            ]);
        }

        $request->validate([
            'id_petani'                    => ['required', 'exists:petani,id_petani'],
            'tanggal_panen'                => ['required', 'date', 'before_or_equal:today'],
            'detail'                       => ['required', 'array', 'min:1'],
            'detail.*.id_jenis_gabah'      => ['required', 'exists:jenis_gabah,id_jenis_gabah'],
            'detail.*.jumlah_panen'        => ['required', 'numeric', 'min:1'],
        ], [
            'id_petani.required'                => 'Petani wajib dipilih.',
            'id_petani.exists'                  => 'Petani tidak ditemukan.',
            'tanggal_panen.required'            => 'Tanggal panen wajib diisi.',
            'tanggal_panen.before_or_equal'     => 'Tanggal panen tidak boleh melewati hari ini.',
            'detail.required'                   => 'Minimal satu jenis gabah wajib diisi.',
            'detail.*.id_jenis_gabah.required'  => 'Jenis gabah wajib dipilih.',
            'detail.*.id_jenis_gabah.exists'    => 'Jenis gabah tidak ditemukan.',
            'detail.*.jumlah_panen.required'    => 'Jumlah panen wajib diisi.',
            'detail.*.jumlah_panen.min'         => 'Jumlah panen minimal 1 kg.',
        ]);

        // Cegah duplikasi jenis gabah
        $idJenisGabahList = array_column($request->detail, 'id_jenis_gabah');
        if (count($idJenisGabahList) !== count(array_unique($idJenisGabahList))) {
            return back()->withInput()->withErrors([
                'detail' => 'Jenis gabah tidak boleh duplikat dalam satu data panen.',
            ]);
        }

        $persenLumbung = config('silpd.persen_lumbung', 3);
        $peringatanSlot = [];

        DB::transaction(function () use ($request, $panen, $persenLumbung, &$peringatanSlot) {

            // 1. Update header panen
            $panen->update([
                'id_petani'     => $request->id_petani,
                'tanggal_panen' => $request->tanggal_panen,
            ]);

            // 2. Hapus detail lama dan instruksi pending-nya
            foreach ($panen->detailPanen as $detail) {
                // Hapus instruksi yang pending
                $detail->instruksiPenyimpanan()
                    ->where('status', 'pending')
                    ->delete();
                // Hapus detail
                $detail->delete();
            }

            // 3. Buat detail baru
            foreach ($request->detail as $item) {
                $jumlahPanen   = (float) $item['jumlah_panen'];
                $jumlahLumbung = round($jumlahPanen * ($persenLumbung / 100), 2);

                $detailPanen = DetailPanen::create([
                    'id_panen'       => $panen->id_panen,
                    'id_jenis_gabah' => $item['id_jenis_gabah'],
                    'jumlah_panen'   => $jumlahPanen,
                ]);

                // 4. Generate instruksi baru
                if ($jumlahLumbung > 0) {
                    $slot = $this->cariSlotYangSesuai($jumlahLumbung);

                    if ($slot) {
                        InstruksiPenyimpanan::create([
                            'id_detail'         => $detailPanen->id_detail,
                            'id_slot'           => $slot->id_slot,
                            'jumlah'            => $jumlahLumbung,
                            'tanggal_instruksi' => Carbon::today(),
                            'status'            => 'pending',
                        ]);
                    } else {
                        $namaJenis = JenisGabah::find($item['id_jenis_gabah'])?->nama_jenis ?? 'Tidak diketahui';
                        $peringatanSlot[] = "Gabah {$namaJenis} ({$jumlahLumbung} kg): tidak ada slot dengan kapasitas mencukupi.";
                    }
                }
            }
        });

        $successMsg = 'Data panen berhasil diperbarui. Instruksi penyimpanan telah di-generate ulang.';

        if (! empty($peringatanSlot)) {
            return redirect()
                ->route('admin.panen.show', $id)
                ->with('success', $successMsg)
                ->with('warning_list', $peringatanSlot);
        }

        return redirect()
            ->route('admin.panen.show', $id)
            ->with('success', $successMsg);
    }

    /**
     * Batalkan instruksi penyimpanan yang masih pending.
     *
     * Instruksi yang sudah dikonfirmasi pengelola (status: selesai)
     * tidak bisa dibatalkan dari sini.
     */
    public function batalInstruksi(int $idInstruksi)
    {
        $instruksi = InstruksiPenyimpanan::with('detailPanen.panen')
            ->where('status', 'pending')
            ->findOrFail($idInstruksi);

        $idPanen = $instruksi->detailPanen->id_panen;

        $instruksi->delete();

        return redirect()
            ->route('admin.panen.show', $idPanen)
            ->with('success', 'Instruksi penyimpanan berhasil dibatalkan.');
    }

    /**
     * Hapus data panen beserta semua detailnya.
     *
     * Hanya bisa dihapus jika:
     * - Semua instruksi penyimpanan masih pending (belum dikonfirmasi pengelola)
     * - Belum ada gabah yang masuk ke penyimpanan
     */
    public function destroy(int $id)
    {
        $panen = Panen::with([
            'detailPanen.instruksiPenyimpanan',
            'detailPanen.penyimpananGabah',
        ])->findOrFail($id);

        // Cek ada instruksi yang sudah selesai
        $adaInstruksiSelesai = $panen->detailPanen->flatMap->instruksiPenyimpanan
            ->where('status', 'selesai')
            ->isNotEmpty();

        if ($adaInstruksiSelesai) {
            return back()->withErrors([
                'hapus' => 'Data panen tidak dapat dihapus karena sebagian instruksi penyimpanan sudah dikonfirmasi oleh pengelola.',
            ]);
        }

        // Cek ada gabah yang sudah masuk penyimpanan
        $adaPenyimpanan = $panen->detailPanen->flatMap->penyimpananGabah->isNotEmpty();

        if ($adaPenyimpanan) {
            return back()->withErrors([
                'hapus' => 'Data panen tidak dapat dihapus karena gabah sudah masuk ke slot penyimpanan.',
            ]);
        }

        DB::transaction(function () use ($panen) {
            foreach ($panen->detailPanen as $detail) {
                // Hapus instruksi penyimpanan pending
                $detail->instruksiPenyimpanan()->delete();
                // Hapus detail panen
                $detail->delete();
            }
            $panen->delete();
        });

        return redirect()
            ->route('admin.panen.index')
            ->with('success', 'Data panen berhasil dihapus.');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Cari slot yang kapasitasnya mencukupi untuk jumlah gabah yang akan disimpan.
     *
     * Strategi pemilihan slot (urutan prioritas):
     * 1. Slot yang kapasitas_tersedia-nya paling besar (menghindari fragmentasi)
     * 2. Hanya slot yang kapasitas_tersedia >= jumlah yang dibutuhkan
     *
     * Catatan: Logika ini idealnya dipisah ke TentukanSlotService
     * agar bisa diuji secara independen dan mudah diganti strateginya.
     *
     * @param  float       $jumlahDibutuhkan  Jumlah gabah (kg) yang perlu disimpan
     * @return SlotLumbung|null               Slot yang dipilih, atau null jika tidak ada
     */
    private function cariSlotYangSesuai(float $jumlahDibutuhkan): ?SlotLumbung
    {
        return SlotLumbung::where('kapasitas_tersedia', '>=', $jumlahDibutuhkan)
            ->orderByDesc('kapasitas_tersedia')
            ->lockForUpdate() // Cegah race condition jika ada input panen bersamaan
            ->first();
    }
}
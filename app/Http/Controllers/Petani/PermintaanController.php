<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\JenisGabah;
use App\Models\PermintaanPengambilan;
use App\Models\PenyimpananGabah;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PermintaanController extends Controller
{
    /**
     * Daftar semua permintaan pengambilan milik petani ini.
     */
    public function index()
    {
        $idPetani = session('ref_id');

        $permintaanList = PermintaanPengambilan::where('id_petani', $idPetani)
            ->with([
                'penyimpananGabah.detailPanen.jenisGabah',
                'penyimpananGabah.slotLumbung.lumbung',
                'detailPengambilan',
            ])
            ->orderByDesc('tanggal_permintaan')
            ->paginate(15);

        return view('petani.permintaan.index', compact('permintaanList'));
    }

    /**
     * Form pengajuan permintaan pengambilan baru.
     *
     * Hanya menampilkan gabah milik petani yang berstatus 'tersimpan'.
     * Petani tidak bisa mengajukan jika tidak punya stok aktif.
     */
    public function create()
    {
        $idPetani = session('ref_id');

        // Cek apakah ada permintaan yang masih pending atau disetujui
        $permintaanAktif = PermintaanPengambilan::where('id_petani', $idPetani)
            ->whereIn('status', ['pending', 'disetujui'])
            ->exists();

        if ($permintaanAktif) {
            return redirect()
                ->route('petani.permintaan.index')
                ->with('warning', 'Anda masih memiliki permintaan yang sedang diproses. Tunggu hingga selesai sebelum mengajukan yang baru.');
        }

        // Gabah tersimpan milik petani (urutan FIFO — yang tertua ditampilkan lebih dulu)
        $stokTersimpan = PenyimpananGabah::whereHas('detailPanen.panen', function ($q) use ($idPetani) {
                $q->where('id_petani', $idPetani);
            })
            ->where('status', 'tersimpan')
            ->with([
                'detailPanen.jenisGabah',
                'detailPanen.panen',
                'slotLumbung.lumbung',
            ])
            ->orderBy('tanggal_masuk') // FIFO
            ->get();

        if ($stokTersimpan->isEmpty()) {
            return redirect()
                ->route('petani.stok.index')
                ->with('info', 'Anda tidak memiliki gabah yang tersimpan di lumbung.');
        }

        return view('petani.permintaan.create', compact('stokTersimpan'));
    }

    /**
     * Simpan permintaan pengambilan gabah.
     *
     * Alur:
     * 1. Validasi input
     * 2. Validasi stok yang dipilih benar-benar milik petani ini
     * 3. Validasi jumlah tidak melebihi stok tersimpan
     * 4. Buat permintaan + detail pengambilan
     * 5. Status awal = 'pending' (menunggu persetujuan admin)
     */
    public function store(Request $request)
    {
        $idPetani = session('ref_id');

        $request->validate([
            'id_penyimpanan' => ['required', 'integer', 'exists:penyimpanan_gabah,id_penyimpanan'],
            'jumlah'         => ['required', 'numeric', 'min:1'],
            'alasan'         => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'id_penyimpanan.required' => 'Pilih gabah yang ingin diambil.',
            'id_penyimpanan.exists'   => 'Data gabah tidak ditemukan.',
            'jumlah.required'         => 'Jumlah gabah yang ingin diambil wajib diisi.',
            'jumlah.min'              => 'Jumlah minimal 1 kg.',
            'alasan.required'         => 'Alasan pengambilan wajib diisi.',
            'alasan.min'              => 'Alasan terlalu singkat, minimal 10 karakter.',
        ]);

        // Validasi: penyimpanan memang milik petani ini
        $penyimpanan = PenyimpananGabah::whereHas('detailPanen.panen', function ($q) use ($idPetani) {
                $q->where('id_petani', $idPetani);
            })
            ->where('status', 'tersimpan')
            ->findOrFail($request->id_penyimpanan);

        // Validasi jumlah tidak melebihi stok
        if ($request->jumlah > $penyimpanan->jumlah) {
            return back()
                ->withInput()
                ->withErrors([
                    'jumlah' => "Jumlah melebihi stok tersimpan. Stok Anda: {$penyimpanan->jumlah} kg.",
                ]);
        }

        // Cek tidak ada permintaan aktif (double check)
        $sudahAda = PermintaanPengambilan::where('id_petani', $idPetani)
            ->whereIn('status', ['pending', 'disetujui'])
            ->exists();

        if ($sudahAda) {
            return back()->withErrors([
                'permintaan' => 'Anda masih memiliki permintaan aktif yang sedang diproses.',
            ]);
        }

        DB::transaction(function () use ($request, $idPetani, $penyimpanan) {
            // Buat permintaan utama
            $permintaan = PermintaanPengambilan::create([
                'id_petani'          => $idPetani,
                'id_penyimpanan'     => $penyimpanan->id_penyimpanan,
                'tanggal_permintaan' => Carbon::today(),
                'status'             => 'pending',
            ]);

            // Buat detail pengambilan
            $permintaan->detailPengambilan()->create([
                'id_penyimpanan' => $penyimpanan->id_penyimpanan,
                'jumlah'         => $request->jumlah,
                'alasan'         => $request->alasan,
            ]);
        });

        return redirect()
            ->route('petani.permintaan.index')
            ->with('success', 'Permintaan pengambilan berhasil diajukan. Menunggu persetujuan admin desa.');
    }

    /**
     * Detail satu permintaan pengambilan milik petani.
     */
    public function show(int $id)
    {
        $idPetani = session('ref_id');

        $permintaan = PermintaanPengambilan::where('id_petani', $idPetani)
            ->with([
                'penyimpananGabah.detailPanen.jenisGabah',
                'penyimpananGabah.detailPanen.panen',
                'penyimpananGabah.slotLumbung.lumbung',
                'detailPengambilan',
            ])
            ->findOrFail($id);

        return view('petani.permintaan.show', compact('permintaan'));
    }

    /**
     * Petani membatalkan permintaan yang masih berstatus 'pending'.
     * Permintaan yang sudah 'disetujui' tidak bisa dibatalkan dari sisi petani.
     */
    public function batal(int $id)
    {
        $idPetani = session('ref_id');

        $permintaan = PermintaanPengambilan::where('id_petani', $idPetani)
            ->where('status', 'pending')
            ->findOrFail($id);

        $permintaan->update(['status' => 'ditolak']);

        return redirect()
            ->route('petani.permintaan.index')
            ->with('success', 'Permintaan berhasil dibatalkan.');
    }
}
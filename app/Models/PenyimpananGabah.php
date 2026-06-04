<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenyimpananGabah extends Model
{
    protected $table = 'penyimpanan_gabah';
    protected $primaryKey = 'id_penyimpanan';
    public $incrementing = true;
    public $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_detail',
        'id_instruksi',
        'id_slot',
        'jumlah_masuk',
        'jumlah',
        'tanggal_masuk',
        'status',
    ];

    protected $casts = [
        'jumlah_masuk' => 'decimal:2',
        'jumlah' => 'decimal:2',
        'tanggal_masuk' => 'date',
    ];

    /**
     * Hubungan: PenyimpananGabah belongs to InstruksiPenyimpanan
     * Link untuk tracking asal gabah dari instruksi mana
     *
     * @return BelongsTo
     */
    public function instruksiPenyimpanan(): BelongsTo
    {
        return $this->belongsTo(InstruksiPenyimpanan::class, 'id_instruksi', 'id_instruksi');
    }

    /**
     * Hubungan: PenyimpananGabah belongs to DetailPanen
     *
     * @return BelongsTo
     */
    public function detailPanen(): BelongsTo
    {
        return $this->belongsTo(DetailPanen::class, 'id_detail', 'id_detail');
    }

    /**
     * Hubungan: PenyimpananGabah belongs to SlotLumbung
     *
     * @return BelongsTo
     */
    public function slotLumbung(): BelongsTo
    {
        return $this->belongsTo(SlotLumbung::class, 'id_slot', 'id_slot');
    }

    /**
     * Hubungan: PenyimpananGabah memiliki banyak permintaan pengambilan
     *
     * @return HasMany
     */
    public function permintaanPengambilan(): HasMany
    {
        return $this->hasMany(PermintaanPengambilan::class, 'id_penyimpanan', 'id_penyimpanan');
    }

    /**
     * Hubungan: PenyimpananGabah memiliki banyak detail pengambilan
     *
     * @return HasMany
     */
    public function detailPengambilan(): HasMany
    {
        return $this->hasMany(DetailPengambilan::class, 'id_penyimpanan', 'id_penyimpanan');
    }
}

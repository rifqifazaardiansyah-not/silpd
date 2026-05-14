<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailPanen extends Model
{
    protected $table = 'detail_panen';
    protected $primaryKey = 'id_detail';
    public $timestamps = true;

    protected $fillable = [
        'id_panen',
        'id_jenis_gabah',
        'jumlah_panen',
    ];

    protected $casts = [
        'jumlah_panen' => 'decimal:2',
    ];

    /**
     * Hubungan: DetailPanen belongs to Panen
     *
     * @return BelongsTo
     */
    public function panen(): BelongsTo
    {
        return $this->belongsTo(Panen::class, 'id_panen', 'id_panen');
    }

    /**
     * Hubungan: DetailPanen belongs to JenisGabah
     *
     * @return BelongsTo
     */
    public function jenisGabah(): BelongsTo
    {
        return $this->belongsTo(JenisGabah::class, 'id_jenis_gabah', 'id_jenis_gabah');
    }

    /**
     * Hubungan: DetailPanen memiliki banyak penyimpanan gabah
     *
     * @return HasMany
     */
    public function penyimpananGabah(): HasMany
    {
        return $this->hasMany(PenyimpananGabah::class, 'id_detail', 'id_detail');
    }

    /**
     * Hubungan: DetailPanen memiliki banyak instruksi penyimpanan
     *
     * @return HasMany
     */
    public function instruksiPenyimpanan(): HasMany
    {
        return $this->hasMany(InstruksiPenyimpanan::class, 'id_detail', 'id_detail');
    }
}

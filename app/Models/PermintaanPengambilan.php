<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermintaanPengambilan extends Model
{
    protected $table = 'permintaan_pengambilan';
    protected $primaryKey = 'id_permintaan';
    public $timestamps = true;

    protected $fillable = [
        'id_petani',
        'id_penyimpanan',
        'tanggal_permintaan',
        'status',
    ];

    protected $casts = [
        'tanggal_permintaan' => 'date',
    ];

    /**
     * Hubungan: PermintaanPengambilan belongs to Petani
     *
     * @return BelongsTo
     */
    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class, 'id_petani', 'id_petani');
    }

    /**
     * Hubungan: PermintaanPengambilan belongs to PenyimpananGabah
     *
     * @return BelongsTo
     */
    public function penyimpananGabah(): BelongsTo
    {
        return $this->belongsTo(PenyimpananGabah::class, 'id_penyimpanan', 'id_penyimpanan');
    }

    /**
     * Hubungan: PermintaanPengambilan memiliki banyak detail pengambilan
     *
     * @return HasMany
     */
    public function detailPengambilan(): HasMany
    {
        return $this->hasMany(DetailPengambilan::class, 'id_permintaan', 'id_permintaan');
    }
}

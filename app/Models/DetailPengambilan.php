<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPengambilan extends Model
{
    protected $table = 'detail_pengambilan';
    protected $primaryKey = 'id_detail_ambil';
    public $timestamps = true;

    protected $fillable = [
        'id_permintaan',
        'id_penyimpanan',
        'jumlah',
        'alasan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    /**
     * Hubungan: DetailPengambilan belongs to PermintaanPengambilan
     *
     * @return BelongsTo
     */
    public function permintaanPengambilan(): BelongsTo
    {
        return $this->belongsTo(PermintaanPengambilan::class, 'id_permintaan', 'id_permintaan');
    }

    /**
     * Hubungan: DetailPengambilan belongs to PenyimpananGabah
     *
     * @return BelongsTo
     */
    public function penyimpananGabah(): BelongsTo
    {
        return $this->belongsTo(PenyimpananGabah::class, 'id_penyimpanan', 'id_penyimpanan');
    }
}

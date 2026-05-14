<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstruksiPenyimpanan extends Model
{
    protected $table = 'instruksi_penyimpanan';
    protected $primaryKey = 'id_instruksi';
    public $timestamps = true;

    protected $fillable = [
        'id_detail',
        'id_slot',
        'jumlah',
        'tanggal_instruksi',
        'status',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_instruksi' => 'date',
    ];

    /**
     * Hubungan: InstruksiPenyimpanan belongs to DetailPanen
     *
     * @return BelongsTo
     */
    public function detailPanen(): BelongsTo
    {
        return $this->belongsTo(DetailPanen::class, 'id_detail', 'id_detail');
    }

    /**
     * Hubungan: InstruksiPenyimpanan belongs to SlotLumbung
     *
     * @return BelongsTo
     */
    public function slotLumbung(): BelongsTo
    {
        return $this->belongsTo(SlotLumbung::class, 'id_slot', 'id_slot');
    }
}

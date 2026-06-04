<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InstruksiPenyimpanan extends Model
{
    protected $table = 'instruksi_penyimpanan';
    protected $primaryKey = 'id_instruksi';
    public $incrementing = true;
    public $keyType = 'int';
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

    /**
     * Hubungan: InstruksiPenyimpanan has one PenyimpananGabah
     * Satu instruksi hanya menghasilkan satu penyimpanan (one-to-one)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function penyimpananGabah()
    {
        return $this->hasOne(PenyimpananGabah::class, 'id_instruksi', 'id_instruksi');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlotLumbung extends Model
{
    protected $table = 'slot_lumbung';
    protected $primaryKey = 'id_slot';
    public $incrementing = true;
    public $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_lumbung',
        'kode_slot',
        'kapasitas',
        'kapasitas_tersedia',
    ];

    protected $casts = [
        'kapasitas' => 'decimal:2',
        'kapasitas_tersedia' => 'decimal:2',
    ];

    /**
     * Hubungan: SlotLumbung belongs to Lumbung
     *
     * @return BelongsTo
     */
    public function lumbung(): BelongsTo
    {
        return $this->belongsTo(Lumbung::class, 'id_lumbung', 'id_lumbung');
    }

    /**
     * Hubungan: SlotLumbung memiliki banyak penyimpanan gabah
     *
     * @return HasMany
     */
    public function penyimpananGabah(): HasMany
    {
        return $this->hasMany(PenyimpananGabah::class, 'id_slot', 'id_slot');
    }

    /**
     * Hubungan: SlotLumbung memiliki banyak instruksi penyimpanan
     *
     * @return HasMany
     */
    public function instruksiPenyimpanan(): HasMany
    {
        return $this->hasMany(InstruksiPenyimpanan::class, 'id_slot', 'id_slot');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Panen extends Model
{
    protected $table = 'panen';
    protected $primaryKey = 'id_panen';
    public $incrementing = true;
    public $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_petani',
        'tanggal_panen',
    ];

    protected $casts = [
        'tanggal_panen' => 'date',
    ];

    /**
     * Hubungan: Panen belongs to Petani
     *
     * @return BelongsTo
     */
    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class, 'id_petani', 'id_petani');
    }

    /**
     * Hubungan: Panen memiliki banyak detail panen
     *
     * @return HasMany
     */
    public function detailPanen(): HasMany
    {
        return $this->hasMany(DetailPanen::class, 'id_panen', 'id_panen');
    }

    /**
     * Hubungan: Panen memiliki banyak instruksi penyimpanan (melalui detailPanen)
     *
     * @return HasManyThrough
     */
    public function instruksiPenyimpanan(): HasManyThrough
    {
        return $this->hasManyThrough(
            InstruksiPenyimpanan::class,
            DetailPanen::class,
            'id_panen',      // Foreign key on DetailPanen
            'id_detail',     // Foreign key on InstruksiPenyimpanan
            'id_panen',      // Local key on Panen
            'id_detail'      // Local key on DetailPanen
        );
    }
}

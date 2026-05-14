<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Panen extends Model
{
    protected $table = 'panen';
    protected $primaryKey = 'id_panen';
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
}

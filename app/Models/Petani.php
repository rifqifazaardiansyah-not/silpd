<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Petani extends Model
{
    protected $table = 'petani';
    protected $primaryKey = 'id_petani';
    public $timestamps = true;

    protected $fillable = [
        'id_kelompok',
        'nama_petani',
        'luas_lahan',
    ];

    protected $casts = [
        'luas_lahan' => 'decimal:2',
    ];

    /**
     * Hubungan: Petani belongs to KelompokTani
     *
     * @return BelongsTo
     */
    public function kelompokTani(): BelongsTo
    {
        return $this->belongsTo(KelompokTani::class, 'id_kelompok', 'id_kelompok');
    }

    /**
     * Hubungan: Petani memiliki banyak panen
     *
     * @return HasMany
     */
    public function panen(): HasMany
    {
        return $this->hasMany(Panen::class, 'id_petani', 'id_petani');
    }

    /**
     * Hubungan: Petani memiliki banyak permintaan pengambilan
     *
     * @return HasMany
     */
    public function permintaanPengambilan(): HasMany
    {
        return $this->hasMany(PermintaanPengambilan::class, 'id_petani', 'id_petani');
    }

    /**
     * Hubungan: Petani memiliki data login
     *
     * @return HasMany
     */
    public function login(): HasMany
    {
        return $this->hasMany(Login::class, 'id_petani', 'id_petani');
    }
}

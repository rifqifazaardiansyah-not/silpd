<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pengelola extends Model
{
    protected $table = 'pengelola';
    protected $primaryKey = 'id_pengelola';
    public $incrementing = true;
    public $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nama_pengelola',
        'no_hp',
    ];

    /**
     * Hubungan: Pengelola memiliki data login
     *
     * @return HasOne
     */
    public function login(): HasOne
    {
        return $this->hasOne(Login::class, 'id_pengelola', 'id_pengelola');
    }

    /**
     * Hubungan: Pengelola belongs to many Lumbung (many-to-many)
     * Relasi dikelola melalui tabel pivot lumbung_pengelola
     * Dengan atribut peran (pemilik_akun atau anggota)
     *
     * @return BelongsToMany
     */
    public function lumbung(): BelongsToMany
    {
        return $this->belongsToMany(
            Lumbung::class,
            'lumbung_pengelola',        // nama tabel pivot
            'id_pengelola',             // FK di pivot ke pengelola
            'id_lumbung',               // FK di pivot ke lumbung
            'id_pengelola',             // PK di pengelola
            'id_lumbung'                // PK di lumbung
        )->withPivot('peran', 'created_at', 'updated_at');
    }
}

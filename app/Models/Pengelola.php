<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengelola extends Model
{
    protected $table = 'pengelola';
    protected $primaryKey = 'id_pengelola';
    public $timestamps = true;

    protected $fillable = [
        'nama_pengelola',
        'no_hp',
    ];

    /**
     * Hubungan: Pengelola memiliki data login
     *
     * @return HasMany
     */
    public function login(): HasMany
    {
        return $this->hasMany(Login::class, 'id_pengelola', 'id_pengelola');
    }
}

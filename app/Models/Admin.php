<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    public $timestamps = true;

    protected $fillable = [
        'nama_admin',
        'jabatan',
    ];

    /**
     * Hubungan: Admin memiliki data login
     *
     * @return HasMany
     */
    public function login(): HasMany
    {
        return $this->hasMany(Login::class, 'id_admin', 'id_admin');
    }
}

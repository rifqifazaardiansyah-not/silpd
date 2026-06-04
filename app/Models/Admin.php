<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Admin extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    public $incrementing = true;
    public $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nama_admin',
        'jabatan',
    ];

    /**
     * Hubungan: Admin memiliki data login
     *
     * @return HasOne
     */
    public function login(): HasOne
    {
        return $this->hasOne(Login::class, 'id_admin', 'id_admin');
    }
}

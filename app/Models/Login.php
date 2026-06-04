<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Login extends Model
{
    protected $table = 'login';
    protected $primaryKey = 'id_login';
    public $incrementing = true;
    public $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_petani',
        'id_pengelola',
        'id_admin',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Hubungan: Login belongs to Petani (nullable)
     *
     * @return BelongsTo
     */
    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class, 'id_petani', 'id_petani');
    }

    /**
     * Hubungan: Login belongs to Pengelola (nullable)
     *
     * @return BelongsTo
     */
    public function pengelola(): BelongsTo
    {
        return $this->belongsTo(Pengelola::class, 'id_pengelola', 'id_pengelola');
    }

    /**
     * Hubungan: Login belongs to Admin (nullable)
     *
     * @return BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}

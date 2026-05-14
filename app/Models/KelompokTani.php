<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelompokTani extends Model
{
    protected $table = 'kelompok_tani';
    protected $primaryKey = 'id_kelompok';
    public $timestamps = true;

    protected $fillable = [
        'nama_kelompok',
    ];

    /**
     * Hubungan: Satu kelompok tani memiliki banyak petani
     *
     * @return HasMany
     */
    public function petani(): HasMany
    {
        return $this->hasMany(Petani::class, 'id_kelompok', 'id_kelompok');
    }
}

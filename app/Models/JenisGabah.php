<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisGabah extends Model
{
    protected $table = 'jenis_gabah';
    protected $primaryKey = 'id_jenis_gabah';
    public $incrementing = true;
    public $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nama_jenis',
    ];

    /**
     * Hubungan: Jenis gabah memiliki banyak detail panen
     *
     * @return HasMany
     */
    public function detailPanen(): HasMany
    {
        return $this->hasMany(DetailPanen::class, 'id_jenis_gabah', 'id_jenis_gabah');
    }
}

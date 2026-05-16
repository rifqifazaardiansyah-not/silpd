<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lumbung extends Model
{
    protected $table = 'lumbung';
    protected $primaryKey = 'id_lumbung';
    public $timestamps = true;

    protected $fillable = [
        'nama_lumbung',
    ];

    /**
     * Hubungan: Lumbung memiliki banyak slot
     *
     * @return HasMany
     */
    public function slotLumbung(): HasMany
    {
        return $this->hasMany(SlotLumbung::class, 'id_lumbung', 'id_lumbung');
    }

    /**
     * Hubungan: Lumbung belongs to many Pengelola (many-to-many)
     * Relasi dikelola melalui tabel pivot lumbung_pengelola
     *
     * @return BelongsToMany
     */
    public function pengelola(): BelongsToMany
    {
        return $this->belongsToMany(
            Pengelola::class,
            'lumbung_pengelola',        // nama tabel pivot
            'id_lumbung',               // FK di pivot ke lumbung
            'id_pengelola',             // FK di pivot ke pengelola
            'id_lumbung',               // PK di lumbung
            'id_pengelola'              // PK di pengelola
        )->withPivot('peran', 'created_at', 'updated_at');
    }
}

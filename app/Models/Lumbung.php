<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}

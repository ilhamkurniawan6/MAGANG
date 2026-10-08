<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeluangBisnisMinat extends Model
{
    use HasFactory;

    protected $table = 'peluang_bisnis_minat';

    protected $fillable = [
        'peluang_bisnis_id',
        'umkm_id',
        'catatan',
    ];

    public function peluangBisnis(): BelongsTo
    {
        return $this->belongsTo(PeluangBisnis::class, 'peluang_bisnis_id');
    }

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }
}

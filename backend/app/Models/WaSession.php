<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaSession extends Model
{
    use HasFactory;

    protected $table = 'wa_session';

    protected $fillable = [
        'nomor_wa',
        'umkm_id',
        'step',
        'foto_path',
        'data_ekstraksi',
    ];

    protected $casts = [
        'data_ekstraksi' => 'array',
    ];

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeluangBisnis extends Model
{
    use HasFactory;

    protected $table = 'peluang_bisnis';

    protected $fillable = [
        'created_by',
        'kategori_produk_id',
        'judul',
        'deskripsi',
        'kapasitas',
        'target_volume',
        'status',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }

    public function peminat(): HasMany
    {
        return $this->hasMany(PeluangBisnisMinat::class, 'peluang_bisnis_id');
    }
}

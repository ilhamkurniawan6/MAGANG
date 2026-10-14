<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\StorageService;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'umkm_id',
        'kategori_produk_id',
        'nama_produk',
        'harga',
        'deskripsi',
        'foto_produk',
        'status',
    ];

    protected $casts = [
        'harga' => 'float',
    ];

    protected $appends = ['foto_url'];

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto_produk) {
            return null;
        }

        if (filter_var($this->foto_produk, FILTER_VALIDATE_URL)) {
            return $this->foto_produk;
        }

        return app(StorageService::class)->getUrl($this->foto_produk);
    }
}

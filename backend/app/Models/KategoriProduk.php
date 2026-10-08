<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriProduk extends Model
{
    use HasFactory;

    protected $table = 'kategori_produk';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'kategori_produk_id');
    }
}

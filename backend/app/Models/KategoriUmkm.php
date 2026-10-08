<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriUmkm extends Model
{
    use HasFactory;

    protected $table = 'kategori_umkm';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function umkm(): HasMany
    {
        return $this->hasMany(Umkm::class, 'kategori_id');
    }
}

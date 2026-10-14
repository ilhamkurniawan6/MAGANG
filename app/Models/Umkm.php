<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\StorageService;

class Umkm extends Model
{
    use HasFactory;

    protected $table = 'umkm';

    protected $fillable = [
        'user_id',
        'kategori_id',
        'nama_usaha',
        'deskripsi',
        'alamat',
        'kontak_wa',
        'email_usaha',
        'logo',
        'status_verifikasi',
        'praktik_ramah_lingkungan',
        'praktik_terverifikasi',
        'skor_lestari',
        'skor_lestari_level',
        'skor_lestari_updated_at',
    ];

    protected $casts = [
        'praktik_ramah_lingkungan' => 'array',
        'praktik_terverifikasi' => 'boolean',
        'skor_lestari' => 'integer',
        'skor_lestari_updated_at' => 'datetime',
    ];

    protected $appends = ['logo_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriUmkm::class, 'kategori_id');
    }

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'umkm_id');
    }

    public function catatanPembinaan(): HasMany
    {
        return $this->hasMany(CatatanPembinaan::class, 'umkm_id');
    }

    public function programPeserta(): HasMany
    {
        return $this->hasMany(ProgramPeserta::class, 'umkm_id');
    }

    public function peluangBisnisMinat(): HasMany
    {
        return $this->hasMany(PeluangBisnisMinat::class, 'umkm_id');
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        return app(StorageService::class)->getUrl($this->logo);
    }
}

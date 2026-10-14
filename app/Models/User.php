<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'nama',
        'email',
        'password',
        'no_wa',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['name'];

    public function getNameAttribute(): ?string
    {
        return $this->nama;
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function umkm(): HasOne
    {
        return $this->hasOne(Umkm::class, 'user_id');
    }

    public function programCreated(): HasMany
    {
        return $this->hasMany(Program::class, 'created_by');
    }

    public function peluangBisnisCreated(): HasMany
    {
        return $this->hasMany(PeluangBisnis::class, 'created_by');
    }

    public function catatanPembinaanAdmin(): HasMany
    {
        return $this->hasMany(CatatanPembinaan::class, 'admin_id');
    }

    public function hasRole(string $roleName): bool
    {
        return strtolower($this->role?->nama_role ?? '') === strtolower($roleName);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'nama',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute(): string
    {
        return $this->nama ?? $this->username;
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_user');
    }

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'id_user');
    }

    public function koreksiStoks()
    {
        return $this->hasMany(KoreksiStok::class, 'id_user');
    }
}

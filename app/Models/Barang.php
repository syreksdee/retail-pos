<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'nama_barang',
        'harga_beli',
        'harga_jual',
        'stok',
        'satuan',
    ];

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_barang');
    }

    public function detailBarangMasuks()
    {
        return $this->hasMany(DetailBarangMasuk::class, 'id_barang');
    }

    public function koreksiStoks()
    {
        return $this->hasMany(KoreksiStok::class, 'id_barang');
    }
}

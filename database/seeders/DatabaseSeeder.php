<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\BarangMasuk;
use App\Models\DetailBarangMasuk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'nama' => 'Administrator Utama',
            'role' => 'admin',
        ]);

        $kasir = User::create([
            'username' => 'kasir',
            'password' => Hash::make('kasir123'),
            'nama' => 'Budi Santoso (Kasir)',
            'role' => 'kasir',
        ]);

        $gudang = User::create([
            'username' => 'gudang',
            'password' => Hash::make('gudang123'),
            'nama' => 'Joko Widodo (Gudang)',
            'role' => 'gudang',
        ]);

        // 2. Barangs
        $barangsData = [
            [
                'barcode' => '8886008101053',
                'nama_barang' => 'Indomie Goreng Spesial',
                'harga_beli' => 2800,
                'harga_jual' => 3500,
                'stok' => 150,
                'satuan' => 'pcs',
            ],
            [
                'barcode' => '8886008101060',
                'nama_barang' => 'Aqua Air Mineral 600ml',
                'harga_beli' => 2500,
                'harga_jual' => 4000,
                'stok' => 80,
                'satuan' => 'botol',
            ],
            [
                'barcode' => '8992753211111',
                'nama_barang' => 'Teh Pucuk Harum 350ml',
                'harga_beli' => 3000,
                'harga_jual' => 4500,
                'stok' => 60,
                'satuan' => 'botol',
            ],
            [
                'barcode' => '8998866102345',
                'nama_barang' => 'Minyak Goreng Bimoli 1L',
                'harga_beli' => 17000,
                'harga_jual' => 20000,
                'stok' => 25,
                'satuan' => 'pouch',
            ],
            [
                'barcode' => '8991001200012',
                'nama_barang' => 'Beras Ramos Super 5kg',
                'harga_beli' => 68000,
                'harga_jual' => 76000,
                'stok' => 15,
                'satuan' => 'karung',
            ],
            [
                'barcode' => '8991001300021',
                'nama_barang' => 'Gula Pasir Gulaku 1kg',
                'harga_beli' => 15000,
                'harga_jual' => 18000,
                'stok' => 30,
                'satuan' => 'kg',
            ],
            [
                'barcode' => '8992696404415',
                'nama_barang' => 'Kopi Kapal Api Special 165g',
                'harga_beli' => 12500,
                'harga_jual' => 15000,
                'stok' => 40,
                'satuan' => 'pack',
            ],
            [
                'barcode' => '8999999014321',
                'nama_barang' => 'Sabun Mandi Lifebuoy Total 10 85g',
                'harga_beli' => 3500,
                'harga_jual' => 5000,
                'stok' => 8, // stok rendah <= 10 for alert demonstration
                'satuan' => 'pcs',
            ],
            [
                'barcode' => '8999999522101',
                'nama_barang' => 'Pasta Gigi Pepsodent 120g',
                'harga_beli' => 8500,
                'harga_jual' => 11000,
                'stok' => 5, // stok rendah <= 10 for alert demonstration
                'satuan' => 'pcs',
            ],
            [
                'barcode' => '8996001301222',
                'nama_barang' => 'Susu Ultra Milk Coklat 250ml',
                'harga_beli' => 5500,
                'harga_jual' => 7000,
                'stok' => 45,
                'satuan' => 'kotak',
            ],
        ];

        $createdBarangs = [];
        foreach ($barangsData as $b) {
            $createdBarangs[] = Barang::create($b);
        }

        // 3. Sample Barang Masuk
        $bm = BarangMasuk::create([
            'tanggal' => now()->subDays(2),
            'id_user' => $gudang->id,
            'keterangan' => 'Pengiriman supplier mingguan PT Sumber Makmur',
        ]);

        DetailBarangMasuk::create([
            'id_barang_masuk' => $bm->id,
            'id_barang' => $createdBarangs[0]->id,
            'qty' => 50,
        ]);
        DetailBarangMasuk::create([
            'id_barang_masuk' => $bm->id,
            'id_barang' => $createdBarangs[1]->id,
            'qty' => 40,
        ]);

        // 4. Sample Transaksi Hari Ini
        $t1 = Transaksi::create([
            'kode_transaksi' => 'TRX-' . now()->format('Ymd') . '-0001',
            'tanggal' => now()->subHours(2),
            'id_user' => $kasir->id,
            'total' => 24500,
            'bayar' => 30000,
            'kembalian' => 5500,
        ]);

        DetailTransaksi::create([
            'id_transaksi' => $t1->id,
            'id_barang' => $createdBarangs[0]->id, // Indomie
            'qty' => 3,
            'harga' => 3500,
            'subtotal' => 10500,
        ]);

        DetailTransaksi::create([
            'id_transaksi' => $t1->id,
            'id_barang' => $createdBarangs[1]->id, // Aqua
            'qty' => 1,
            'harga' => 4000,
            'subtotal' => 4000,
        ]);

        DetailTransaksi::create([
            'id_transaksi' => $t1->id,
            'id_barang' => $createdBarangs[6]->id, // Kopi
            'qty' => 1,
            'harga' => 15000,
            'subtotal' => 15000,
        ]);

        // Second sample transaction
        $t2 = Transaksi::create([
            'kode_transaksi' => 'TRX-' . now()->format('Ymd') . '-0002',
            'tanggal' => now()->subHour(),
            'id_user' => $kasir->id,
            'total' => 40000,
            'bayar' => 50000,
            'kembalian' => 10000,
        ]);

        DetailTransaksi::create([
            'id_transaksi' => $t2->id,
            'id_barang' => $createdBarangs[3]->id, // Bimoli
            'qty' => 2,
            'harga' => 20000,
            'subtotal' => 40000,
        ]);
    }
}

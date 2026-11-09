<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Kasir Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', [KasirController::class, 'index'])->name('index');
    Route::get('/cari-barang', [KasirController::class, 'cariBarang'])->name('cari-barang');
    Route::get('/search-barang', [KasirController::class, 'searchBarang'])->name('search-barang');
    Route::post('/transaksi', [KasirController::class, 'store'])->name('transaksi.store');
    Route::get('/struk/{transaksi}', [KasirController::class, 'struk'])->name('struk');
});

/*
|--------------------------------------------------------------------------
| Gudang Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:gudang'])->prefix('gudang')->name('gudang.')->group(function () {
    // Dashboard
    Route::get('/', [GudangController::class, 'index'])->name('index');

    // CRUD Barang
    Route::get('/barang', [GudangController::class, 'barang'])->name('barang');
    Route::get('/barang/create', [GudangController::class, 'createBarang'])->name('barang.create');
    Route::post('/barang', [GudangController::class, 'storeBarang'])->name('barang.store');
    Route::get('/barang/{barang}/edit', [GudangController::class, 'editBarang'])->name('barang.edit');
    Route::put('/barang/{barang}', [GudangController::class, 'updateBarang'])->name('barang.update');
    Route::delete('/barang/{barang}', [GudangController::class, 'destroyBarang'])->name('barang.destroy');

    // Barang Masuk
    Route::get('/barang-masuk', [GudangController::class, 'barangMasuk'])->name('barang-masuk');
    Route::get('/barang-masuk/create', [GudangController::class, 'createBarangMasuk'])->name('barang-masuk.create');
    Route::post('/barang-masuk', [GudangController::class, 'storeBarangMasuk'])->name('barang-masuk.store');
    Route::get('/barang-masuk/{barangMasuk}', [GudangController::class, 'showBarangMasuk'])->name('barang-masuk.show');

    // Koreksi Stok
    Route::get('/koreksi-stok', [GudangController::class, 'koreksiStok'])->name('koreksi-stok');
    Route::get('/koreksi-stok/create', [GudangController::class, 'createKoreksiStok'])->name('koreksi-stok.create');
    Route::post('/koreksi-stok', [GudangController::class, 'storeKoreksiStok'])->name('koreksi-stok.store');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Laporan Penjualan
    Route::get('/laporan-penjualan', [AdminController::class, 'laporanPenjualan'])->name('laporan-penjualan');
    Route::get('/detail-transaksi/{transaksi}', [AdminController::class, 'detailTransaksi'])->name('detail-transaksi');

    // Laporan Keuntungan
    Route::get('/laporan-keuntungan', [AdminController::class, 'laporanKeuntungan'])->name('laporan-keuntungan');

    // Stok Barang
    Route::get('/stok-barang', [AdminController::class, 'stokBarang'])->name('stok-barang');

    // Riwayat
    Route::get('/riwayat-barang-masuk', [AdminController::class, 'riwayatBarangMasuk'])->name('riwayat-barang-masuk');
    Route::get('/riwayat-koreksi', [AdminController::class, 'riwayatKoreksi'])->name('riwayat-koreksi');
});

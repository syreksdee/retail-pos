<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\BarangMasuk;
use App\Models\KoreksiStok;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RetailPosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Retail POS System');
    }

    public function test_kasir_login_and_redirect()
    {
        $response = $this->post('/login', [
            'username' => 'kasir',
            'password' => 'kasir123',
        ]);

        $response->assertRedirect('/kasir');
        $this->assertAuthenticated();
    }

    public function test_gudang_login_and_redirect()
    {
        $response = $this->post('/login', [
            'username' => 'gudang',
            'password' => 'gudang123',
        ]);

        $response->assertRedirect('/gudang');
        $this->assertAuthenticated();
    }

    public function test_admin_login_and_redirect()
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticated();
    }

    public function test_role_middleware_blocks_unauthorized_access()
    {
        $kasir = User::where('role', 'kasir')->first();

        // Kasir trying to access Gudang routes should get 403
        $response = $this->actingAs($kasir)->get('/gudang');
        $response->assertStatus(403);

        // Kasir trying to access Admin routes should get 403
        $response = $this->actingAs($kasir)->get('/admin');
        $response->assertStatus(403);

        // Kasir accessing Kasir route should succeed
        $response = $this->actingAs($kasir)->get('/kasir');
        $response->assertStatus(200);
    }

    public function test_kasir_can_search_product_by_barcode()
    {
        $kasir = User::where('role', 'kasir')->first();
        $barang = Barang::where('barcode', '8886008101053')->first();

        $response = $this->actingAs($kasir)->getJson('/kasir/cari-barang?q=8886008101053');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'barang' => [
                    'id' => $barang->id,
                    'barcode' => '8886008101053',
                ]
            ]);
    }

    public function test_kasir_transaction_deducts_stock()
    {
        $kasir = User::where('role', 'kasir')->first();
        $barang = Barang::first();
        $initialStock = $barang->stok;

        $response = $this->actingAs($kasir)->postJson('/kasir/transaksi', [
            'bayar' => 50000,
            'items' => [
                [
                    'id_barang' => $barang->id,
                    'qty' => 2,
                    'harga' => $barang->harga_jual,
                ]
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Stock must be reduced by 2
        $this->assertEquals($initialStock - 2, $barang->fresh()->stok);
    }

    public function test_gudang_can_add_barang_masuk_and_increase_stock()
    {
        $gudang = User::where('role', 'gudang')->first();
        $barang = Barang::first();
        $initialStock = $barang->stok;

        $response = $this->actingAs($gudang)->post('/gudang/barang-masuk', [
            'keterangan' => 'Restock test',
            'items' => [
                [
                    'id_barang' => $barang->id,
                    'qty' => 20,
                ]
            ]
        ]);

        $response->assertRedirect(route('gudang.barang-masuk'));
        $this->assertEquals($initialStock + 20, $barang->fresh()->stok);
    }

    public function test_gudang_can_koreksi_stok()
    {
        $gudang = User::where('role', 'gudang')->first();
        $barang = Barang::first();

        $response = $this->actingAs($gudang)->post('/gudang/koreksi-stok', [
            'id_barang' => $barang->id,
            'stok_baru' => 77,
            'alasan' => 'Penyesuaian stok opname fisik',
        ]);

        $response->assertRedirect(route('gudang.koreksi-stok'));
        $this->assertEquals(77, $barang->fresh()->stok);
    }

    public function test_admin_reports_and_profit_calculation()
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/admin/laporan-keuntungan');
        $response->assertStatus(200);
    }
}

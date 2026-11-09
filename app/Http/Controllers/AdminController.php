<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\BarangMasuk;
use App\Models\KoreksiStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Dashboard stats
        $totalPenjualanHariIni = Transaksi::whereDate('tanggal', today())->sum('total');
        $totalTransaksiHariIni = Transaksi::whereDate('tanggal', today())->count();
        $totalBarangTerjualHariIni = DetailTransaksi::whereHas('transaksi', function ($q) {
            $q->whereDate('tanggal', today());
        })->sum('qty');

        // Total keuntungan hari ini
        $keuntunganHariIni = DB::table('detail_transaksis')
            ->join('transaksis', 'detail_transaksis.id_transaksi', '=', 'transaksis.id')
            ->join('barangs', 'detail_transaksis.id_barang', '=', 'barangs.id')
            ->whereDate('transaksis.tanggal', today())
            ->selectRaw('SUM((detail_transaksis.harga - barangs.harga_beli) * detail_transaksis.qty) as total_keuntungan')
            ->value('total_keuntungan') ?? 0;

        // Monthly stats (last 7 days for chart)
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $penjualan = Transaksi::whereDate('tanggal', $date)->sum('total');
            $last7Days->push([
                'tanggal' => $date->format('d/m'),
                'penjualan' => $penjualan,
            ]);
        }

        // Top 5 produk terlaris
        $topProduk = DB::table('detail_transaksis')
            ->join('barangs', 'detail_transaksis.id_barang', '=', 'barangs.id')
            ->selectRaw('barangs.nama_barang, SUM(detail_transaksis.qty) as total_qty')
            ->groupBy('barangs.id', 'barangs.nama_barang')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Stok rendah
        $stokRendah = Barang::where('stok', '<=', 10)->orderBy('stok')->get();

        return view('admin.index', compact(
            'totalPenjualanHariIni', 'totalTransaksiHariIni',
            'totalBarangTerjualHariIni', 'keuntunganHariIni',
            'last7Days', 'topProduk', 'stokRendah'
        ));
    }

    public function laporanPenjualan(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', now()->format('Y-m-d'));

        $transaksis = Transaksi::with(['user', 'detailTransaksis.barang'])
            ->whereDate('tanggal', '>=', $tanggalMulai)
            ->whereDate('tanggal', '<=', $tanggalAkhir)
            ->orderByDesc('tanggal')
            ->get();

        $totalPenjualan = $transaksis->sum('total');
        $totalTransaksi = $transaksis->count();

        return view('admin.laporan-penjualan', compact(
            'transaksis', 'totalPenjualan', 'totalTransaksi',
            'tanggalMulai', 'tanggalAkhir'
        ));
    }

    public function detailTransaksi(Transaksi $transaksi)
    {
        $transaksi->load(['user', 'detailTransaksis.barang']);
        return view('admin.detail-transaksi', compact('transaksi'));
    }

    public function laporanKeuntungan(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', now()->format('Y-m-d'));

        // Per-product profit
        $laporanPerBarang = DB::table('detail_transaksis')
            ->join('transaksis', 'detail_transaksis.id_transaksi', '=', 'transaksis.id')
            ->join('barangs', 'detail_transaksis.id_barang', '=', 'barangs.id')
            ->whereDate('transaksis.tanggal', '>=', $tanggalMulai)
            ->whereDate('transaksis.tanggal', '<=', $tanggalAkhir)
            ->selectRaw('
                barangs.nama_barang,
                SUM(detail_transaksis.qty) as total_qty,
                SUM(detail_transaksis.subtotal) as total_penjualan,
                SUM(barangs.harga_beli * detail_transaksis.qty) as total_modal,
                SUM((detail_transaksis.harga - barangs.harga_beli) * detail_transaksis.qty) as total_keuntungan
            ')
            ->groupBy('barangs.id', 'barangs.nama_barang')
            ->orderByDesc('total_keuntungan')
            ->get();

        $totalPenjualan = $laporanPerBarang->sum('total_penjualan');
        $totalModal = $laporanPerBarang->sum('total_modal');
        $totalKeuntungan = $laporanPerBarang->sum('total_keuntungan');

        return view('admin.laporan-keuntungan', compact(
            'laporanPerBarang', 'totalPenjualan', 'totalModal', 'totalKeuntungan',
            'tanggalMulai', 'tanggalAkhir'
        ));
    }

    public function stokBarang()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('admin.stok-barang', compact('barangs'));
    }

    public function riwayatBarangMasuk()
    {
        $barangMasuks = BarangMasuk::with(['user', 'detailBarangMasuks.barang'])
            ->orderByDesc('tanggal')->get();
        return view('admin.riwayat-barang-masuk', compact('barangMasuks'));
    }

    public function riwayatKoreksi()
    {
        $koreksis = KoreksiStok::with(['barang', 'user'])
            ->orderByDesc('tanggal')->get();
        return view('admin.riwayat-koreksi', compact('koreksis'));
    }
}

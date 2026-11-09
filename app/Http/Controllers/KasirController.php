<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        return view('kasir.index');
    }

    // API-like endpoint for barcode scanning
    public function cariBarang(Request $request)
    {
        $query = $request->get('q');
        $barang = Barang::where('barcode', $query)
            ->orWhere('nama_barang', 'LIKE', "%{$query}%")
            ->first();

        if (!$barang) {
            return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan']);
        }

        return response()->json([
            'success' => true,
            'barang' => [
                'id' => $barang->id,
                'barcode' => $barang->barcode,
                'nama_barang' => $barang->nama_barang,
                'harga_jual' => $barang->harga_jual,
                'stok' => $barang->stok,
            ]
        ]);
    }

    // Search multiple results
    public function searchBarang(Request $request)
    {
        $query = $request->get('q');
        $barangs = Barang::where('barcode', 'LIKE', "%{$query}%")
            ->orWhere('nama_barang', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'barcode', 'nama_barang', 'harga_jual', 'stok']);

        return response()->json($barangs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barangs,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
        ]);

        $transaksi = null;

        DB::transaction(function () use ($request, &$transaksi) {
            $total = 0;
            $items = [];

            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['id_barang']);

                if ($barang->stok < $item['qty']) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi! Sisa: {$barang->stok}");
                }

                $subtotal = $item['harga'] * $item['qty'];
                $total += $subtotal;
                $items[] = [
                    'id_barang' => $item['id_barang'],
                    'qty' => $item['qty'],
                    'harga' => $item['harga'],
                    'subtotal' => $subtotal,
                ];
            }

            $kembalian = $request->bayar - $total;
            if ($kembalian < 0) {
                throw new \Exception("Pembayaran kurang! Total: Rp " . number_format($total, 0, ',', '.'));
            }

            // Generate kode transaksi: TRX-YYYYMMDD-XXXX
            $today = now()->format('Ymd');
            $count = Transaksi::whereDate('tanggal', today())->count() + 1;
            $kodeTransaksi = "TRX-{$today}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'tanggal' => now(),
                'id_user' => auth()->id(),
                'total' => $total,
                'bayar' => $request->bayar,
                'kembalian' => $kembalian,
            ]);

            foreach ($items as $item) {
                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id,
                    ...$item,
                ]);

                // Kurangi stok
                Barang::find($item['id_barang'])->decrement('stok', $item['qty']);
            }
        });

        return response()->json([
            'success' => true,
            'transaksi' => $transaksi->load('detailTransaksis.barang'),
            'message' => 'Transaksi berhasil!',
        ]);
    }

    public function struk(Transaksi $transaksi)
    {
        $transaksi->load(['detailTransaksis.barang', 'user']);
        return view('kasir.struk', compact('transaksi'));
    }
}

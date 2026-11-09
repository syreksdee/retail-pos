<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\DetailBarangMasuk;
use App\Models\KoreksiStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GudangController extends Controller
{
    // Dashboard
    public function index()
    {
        $totalBarang = Barang::count();
        $stokRendah = Barang::where('stok', '<=', 10)->get();
        $barangMasukHariIni = BarangMasuk::whereDate('tanggal', today())->count();
        return view('gudang.index', compact('totalBarang', 'stokRendah', 'barangMasukHariIni'));
    }

    // CRUD Barang
    public function barang()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('gudang.barang.index', compact('barangs'));
    }

    public function createBarang()
    {
        return view('gudang.barang.create');
    }

    public function storeBarang(Request $request)
    {
        $request->validate([
            'barcode' => 'nullable|string|unique:barangs,barcode',
            'nama_barang' => 'required|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
        ]);

        Barang::create($request->all());
        return redirect()->route('gudang.barang')->with('success', 'Barang berhasil ditambahkan!');
    }

    public function editBarang(Barang $barang)
    {
        return view('gudang.barang.edit', compact('barang'));
    }

    public function updateBarang(Request $request, Barang $barang)
    {
        $request->validate([
            'barcode' => 'nullable|string|unique:barangs,barcode,' . $barang->id,
            'nama_barang' => 'required|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
        ]);

        $barang->update($request->only(['barcode', 'nama_barang', 'harga_beli', 'harga_jual', 'satuan']));
        return redirect()->route('gudang.barang')->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroyBarang(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('gudang.barang')->with('success', 'Barang berhasil dihapus!');
    }

    // Barang Masuk
    public function barangMasuk()
    {
        $barangMasuks = BarangMasuk::with(['user', 'detailBarangMasuks.barang'])
            ->orderByDesc('tanggal')->get();
        return view('gudang.barang-masuk.index', compact('barangMasuks'));
    }

    public function createBarangMasuk()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('gudang.barang-masuk.create', compact('barangs'));
    }

    public function storeBarangMasuk(Request $request)
    {
        $request->validate([
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barangs,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $barangMasuk = BarangMasuk::create([
                'tanggal' => now(),
                'id_user' => auth()->id(),
                'keterangan' => $request->keterangan,
            ]);

            foreach ($request->items as $item) {
                DetailBarangMasuk::create([
                    'id_barang_masuk' => $barangMasuk->id,
                    'id_barang' => $item['id_barang'],
                    'qty' => $item['qty'],
                ]);

                // Update stok
                $barang = Barang::find($item['id_barang']);
                $barang->increment('stok', $item['qty']);
            }
        });

        return redirect()->route('gudang.barang-masuk')->with('success', 'Barang masuk berhasil dicatat!');
    }

    public function showBarangMasuk(BarangMasuk $barangMasuk)
    {
        $barangMasuk->load(['user', 'detailBarangMasuks.barang']);
        return view('gudang.barang-masuk.show', compact('barangMasuk'));
    }

    // Koreksi Stok
    public function koreksiStok()
    {
        $koreksis = KoreksiStok::with(['barang', 'user'])->orderByDesc('tanggal')->get();
        return view('gudang.koreksi-stok.index', compact('koreksis'));
    }

    public function createKoreksiStok()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('gudang.koreksi-stok.create', compact('barangs'));
    }

    public function storeKoreksiStok(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barangs,id',
            'stok_baru' => 'required|integer|min:0',
            'alasan' => 'required|string',
        ]);

        $barang = Barang::findOrFail($request->id_barang);
        $stokSebelum = $barang->stok;
        $perubahan = $request->stok_baru - $stokSebelum;

        DB::transaction(function () use ($request, $barang, $stokSebelum, $perubahan) {
            KoreksiStok::create([
                'id_barang' => $request->id_barang,
                'id_user' => auth()->id(),
                'stok_sebelum' => $stokSebelum,
                'perubahan' => $perubahan,
                'stok_sesudah' => $request->stok_baru,
                'alasan' => $request->alasan,
                'tanggal' => now(),
            ]);

            $barang->update(['stok' => $request->stok_baru]);
        });

        return redirect()->route('gudang.koreksi-stok')->with('success', 'Koreksi stok berhasil!');
    }
}

@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📄 Detail Transaksi</h1>
        <a href="{{ route('admin.laporan-penjualan') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            ← Kembali
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Transaction Header --}}
    <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Kode Transaksi</p>
                <p class="font-mono font-bold text-blue-700 text-lg">{{ $transaksi->kode_transaksi }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Tanggal</p>
                <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Kasir</p>
                <p class="font-semibold text-gray-800">{{ $transaksi->user->name ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-600">Daftar Barang</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4 text-gray-600 font-medium">No</th>
                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Nama Barang</th>
                        <th class="text-right py-3 px-4 text-gray-600 font-medium">Harga</th>
                        <th class="text-center py-3 px-4 text-gray-600 font-medium">Qty</th>
                        <th class="text-right py-3 px-4 text-gray-600 font-medium">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi->detailTransaksis as $i => $detail)
                        <tr class="border-b border-gray-100">
                            <td class="py-3 px-4 text-gray-500">{{ $i + 1 }}</td>
                            <td class="py-3 px-4 font-medium">{{ $detail->barang->nama_barang ?? '-' }}</td>
                            <td class="py-3 px-4 text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">{{ $detail->qty }}</td>
                            <td class="py-3 px-4 text-right font-semibold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Total Summary --}}
    <div class="bg-white rounded-xl shadow-md p-4 lg:p-6">
        <div class="space-y-3">
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600">Total</span>
                <span class="font-semibold text-lg">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
            </div>
            <div class="border-t border-gray-200 pt-3 flex justify-between items-center text-sm">
                <span class="text-gray-600">Bayar</span>
                <span class="font-semibold text-lg text-green-600">Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
            </div>
            <div class="border-t border-gray-200 pt-3 flex justify-between items-center text-sm">
                <span class="text-gray-600">Kembalian</span>
                <span class="font-semibold text-lg text-blue-600">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-6 text-center">
        <a href="{{ route('admin.laporan-penjualan') }}"
            class="inline-block bg-gray-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-gray-700 transition">
            ← Kembali ke Laporan Penjualan
        </a>
    </div>
</div>
@endsection

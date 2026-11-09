@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📊 Dashboard Admin</h1>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total Penjualan Hari Ini --}}
        <div class="bg-blue-600 text-white rounded-xl p-4 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-blue-200 text-sm font-medium">Penjualan Hari Ini</span>
                <span class="text-2xl">💰</span>
            </div>
            <p class="text-xl lg:text-2xl font-bold">Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}</p>
        </div>

        {{-- Total Transaksi Hari Ini --}}
        <div class="bg-green-600 text-white rounded-xl p-4 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-green-200 text-sm font-medium">Transaksi Hari Ini</span>
                <span class="text-2xl">🧾</span>
            </div>
            <p class="text-xl lg:text-2xl font-bold">{{ number_format($totalTransaksiHariIni) }}</p>
        </div>

        {{-- Barang Terjual Hari Ini --}}
        <div class="bg-purple-600 text-white rounded-xl p-4 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-purple-200 text-sm font-medium">Barang Terjual</span>
                <span class="text-2xl">📦</span>
            </div>
            <p class="text-xl lg:text-2xl font-bold">{{ number_format($totalBarangTerjualHariIni) }}</p>
        </div>

        {{-- Keuntungan Hari Ini --}}
        <div class="bg-emerald-600 text-white rounded-xl p-4 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-emerald-200 text-sm font-medium">Keuntungan Hari Ini</span>
                <span class="text-2xl">📈</span>
            </div>
            <p class="text-xl lg:text-2xl font-bold">Rp {{ number_format($keuntunganHariIni, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Penjualan 7 Hari Terakhir Chart --}}
    <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">📉 Penjualan 7 Hari Terakhir</h2>
        @php
            $maxPenjualan = $last7Days->max('penjualan');
            $maxPenjualan = $maxPenjualan > 0 ? $maxPenjualan : 1;
        @endphp
        <div class="flex items-end justify-between gap-2" style="height: 200px;">
            @foreach($last7Days as $day)
                @php
                    $heightPercent = ($day['penjualan'] / $maxPenjualan) * 100;
                    $heightPercent = max($heightPercent, 2);
                @endphp
                <div class="flex-1 flex flex-col items-center justify-end h-full">
                    <div class="text-xs text-gray-500 mb-1 truncate w-full text-center">
                        Rp {{ number_format($day['penjualan'] / 1000, 0, ',', '.') }}k
                    </div>
                    <div
                        class="w-full bg-blue-500 rounded-t-md transition-all duration-300 hover:bg-blue-600"
                        style="height: {{ $heightPercent }}%; min-height: 4px;"
                    ></div>
                    <div class="text-xs text-gray-600 mt-2 font-medium">{{ $day['tanggal'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Top 5 Produk Terlaris --}}
        <div class="bg-white rounded-xl shadow-md p-4 lg:p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🏆 Top 5 Produk Terlaris</h2>
            @if($topProduk->isEmpty())
                <p class="text-gray-500 text-sm text-center py-4">Belum ada data penjualan.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 px-2 text-gray-600 font-medium">No</th>
                                <th class="text-left py-2 px-2 text-gray-600 font-medium">Nama Barang</th>
                                <th class="text-right py-2 px-2 text-gray-600 font-medium">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProduk as $i => $produk)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 px-2">
                                        @if($i === 0) 🥇
                                        @elseif($i === 1) 🥈
                                        @elseif($i === 2) 🥉
                                        @else {{ $i + 1 }}
                                        @endif
                                    </td>
                                    <td class="py-2 px-2 font-medium">{{ $produk->nama_barang }}</td>
                                    <td class="py-2 px-2 text-right font-semibold text-blue-600">{{ number_format($produk->total_qty) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Stok Rendah Alert --}}
        <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 {{ $stokRendah->isNotEmpty() ? 'border-2 border-red-400' : '' }}">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                ⚠️ Peringatan Stok Rendah
                @if($stokRendah->isNotEmpty())
                    <span class="text-sm bg-red-500 text-white px-2 py-0.5 rounded-full ml-2">{{ $stokRendah->count() }}</span>
                @endif
            </h2>
            @if($stokRendah->isEmpty())
                <div class="text-center py-4">
                    <p class="text-green-600 font-medium">✅ Semua stok aman!</p>
                    <p class="text-gray-500 text-sm mt-1">Tidak ada barang dengan stok ≤ 10</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 px-2 text-gray-600 font-medium">Barang</th>
                                <th class="text-right py-2 px-2 text-gray-600 font-medium">Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stokRendah as $barang)
                                <tr class="border-b border-gray-100 {{ $barang->stok <= 3 ? 'bg-red-50' : 'bg-yellow-50' }}">
                                    <td class="py-2 px-2 font-medium">{{ $barang->nama_barang }}</td>
                                    <td class="py-2 px-2 text-right">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold {{ $barang->stok <= 3 ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800' }}">
                                            {{ $barang->stok }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="bg-white rounded-xl shadow-md p-4 lg:p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">🔗 Menu Cepat</h2>
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
            <a href="{{ route('admin.laporan-penjualan') }}" class="flex items-center gap-2 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <span class="text-xl">🧾</span>
                <span class="text-sm font-medium text-blue-700">Laporan Penjualan</span>
            </a>
            <a href="{{ route('admin.laporan-keuntungan') }}" class="flex items-center gap-2 p-3 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition">
                <span class="text-xl">💹</span>
                <span class="text-sm font-medium text-emerald-700">Laporan Keuntungan</span>
            </a>
            <a href="{{ route('admin.stok-barang') }}" class="flex items-center gap-2 p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <span class="text-xl">📦</span>
                <span class="text-sm font-medium text-purple-700">Stok Barang</span>
            </a>
            <a href="{{ route('admin.riwayat-barang-masuk') }}" class="flex items-center gap-2 p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                <span class="text-xl">📥</span>
                <span class="text-sm font-medium text-orange-700">Riwayat Barang Masuk</span>
            </a>
            <a href="{{ route('admin.riwayat-koreksi') }}" class="flex items-center gap-2 p-3 bg-red-50 rounded-lg hover:bg-red-100 transition">
                <span class="text-xl">🔄</span>
                <span class="text-sm font-medium text-red-700">Riwayat Koreksi Stok</span>
            </a>
        </div>
    </div>
</div>
@endsection

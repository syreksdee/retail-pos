@extends('layouts.app')

@section('title', 'Laporan Keuntungan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">💹 Laporan Keuntungan</h1>
        <a href="{{ route('admin.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            ← Dashboard
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Date Filter --}}
    <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-600 mb-3">🔍 Filter Tanggal</h2>
        <form method="GET" action="{{ route('admin.laporan-keuntungan') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Tampilkan
                </button>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-600 text-white rounded-xl p-4 shadow-lg">
            <p class="text-blue-200 text-sm font-medium">Total Penjualan</p>
            <p class="text-xl font-bold mt-1">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-orange-600 text-white rounded-xl p-4 shadow-lg">
            <p class="text-orange-200 text-sm font-medium">Total Modal</p>
            <p class="text-xl font-bold mt-1">Rp {{ number_format($totalModal, 0, ',', '.') }}</p>
        </div>
        <div class="bg-emerald-600 text-white rounded-xl p-4 shadow-lg ring-4 ring-emerald-300">
            <p class="text-emerald-200 text-sm font-medium">Total Keuntungan</p>
            <p class="text-2xl font-bold mt-1">Rp {{ number_format($totalKeuntungan, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Profit Per Product Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-600">Keuntungan Per Produk</h2>
        </div>
        @if($laporanPerBarang->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <p class="text-lg mb-1">📋</p>
                <p class="text-sm">Tidak ada data pada periode ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium">No</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium">Nama Barang</th>
                            <th class="text-right py-3 px-4 text-gray-600 font-medium hidden sm:table-cell">Qty Terjual</th>
                            <th class="text-right py-3 px-4 text-gray-600 font-medium hidden md:table-cell">Total Penjualan</th>
                            <th class="text-right py-3 px-4 text-gray-600 font-medium hidden md:table-cell">Total Modal</th>
                            <th class="text-right py-3 px-4 text-gray-600 font-medium">Keuntungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporanPerBarang as $i => $item)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="py-3 px-4 font-medium">
                                    {{ $item->nama_barang }}
                                    <div class="text-xs text-gray-400 sm:hidden mt-1">
                                        Qty: {{ number_format($item->total_qty) }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right hidden sm:table-cell">{{ number_format($item->total_qty) }}</td>
                                <td class="py-3 px-4 text-right hidden md:table-cell">Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right hidden md:table-cell">Rp {{ number_format($item->total_modal, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-bold {{ $item->total_keuntungan >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($item->total_keuntungan, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr class="font-bold">
                            <td colspan="2" class="py-3 px-4">Total</td>
                            <td class="py-3 px-4 text-right hidden sm:table-cell">{{ number_format($laporanPerBarang->sum('total_qty')) }}</td>
                            <td class="py-3 px-4 text-right hidden md:table-cell">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right hidden md:table-cell">Rp {{ number_format($totalModal, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-green-700">Rp {{ number_format($totalKeuntungan, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

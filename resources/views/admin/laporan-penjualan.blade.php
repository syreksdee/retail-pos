@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🧾 Laporan Penjualan</h1>
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
        <form method="GET" action="{{ route('admin.laporan-penjualan') }}" class="flex flex-col sm:flex-row gap-3">
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
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-600 text-white rounded-xl p-4 shadow-lg">
            <p class="text-blue-200 text-sm font-medium">Total Penjualan</p>
            <p class="text-xl font-bold mt-1">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-green-600 text-white rounded-xl p-4 shadow-lg">
            <p class="text-green-200 text-sm font-medium">Total Transaksi</p>
            <p class="text-xl font-bold mt-1">{{ number_format($totalTransaksi) }}</p>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-600">Daftar Transaksi</h2>
        </div>
        @if($transaksis->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <p class="text-lg mb-1">📋</p>
                <p class="text-sm">Tidak ada transaksi pada periode ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium">No</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium">Kode Transaksi</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium hidden sm:table-cell">Tanggal</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium hidden md:table-cell">Kasir</th>
                            <th class="text-right py-3 px-4 text-gray-600 font-medium">Total</th>
                            <th class="text-center py-3 px-4 text-gray-600 font-medium">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksis as $i => $trx)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-mono font-medium text-blue-700">{{ $trx->kode_transaksi }}</span>
                                    <div class="text-xs text-gray-400 sm:hidden mt-1">
                                        {{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-600 hidden sm:table-cell">
                                    {{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-gray-600 hidden md:table-cell">
                                    {{ $trx->user->name ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-right font-semibold">
                                    Rp {{ number_format($trx->total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('admin.detail-transaksi', $trx->id) }}"
                                        class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-lg text-xs font-medium hover:bg-blue-200 transition">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

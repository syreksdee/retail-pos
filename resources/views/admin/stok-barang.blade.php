@extends('layouts.app')

@section('title', 'Stok Barang')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📦 Stok Barang</h1>
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

    {{-- Total Count --}}
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600">Total jenis barang:</p>
            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold">{{ $barangs->count() }} item</span>
        </div>
    </div>

    {{-- Stock Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        @if($barangs->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <p class="text-lg mb-1">📦</p>
                <p class="text-sm">Belum ada data barang.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium">No</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium hidden sm:table-cell">Barcode</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium">Nama Barang</th>
                            <th class="text-right py-3 px-4 text-gray-600 font-medium hidden md:table-cell">Harga Beli</th>
                            <th class="text-right py-3 px-4 text-gray-600 font-medium hidden sm:table-cell">Harga Jual</th>
                            <th class="text-center py-3 px-4 text-gray-600 font-medium">Stok</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium hidden lg:table-cell">Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barangs as $i => $barang)
                            @php
                                $isLow = $barang->stok <= 10;
                                $isCritical = $barang->stok <= 3;
                                $rowClass = $isCritical ? 'bg-red-50' : ($isLow ? 'bg-yellow-50' : '');
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50 {{ $rowClass }}">
                                <td class="py-3 px-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="py-3 px-4 font-mono text-xs text-gray-600 hidden sm:table-cell">{{ $barang->barcode ?? '-' }}</td>
                                <td class="py-3 px-4 font-medium">
                                    {{ $barang->nama_barang }}
                                    @if($isLow)
                                        <span class="inline-block ml-1 text-xs {{ $isCritical ? 'text-red-600' : 'text-yellow-600' }}">⚠️</span>
                                    @endif
                                    <div class="text-xs text-gray-400 sm:hidden mt-1">
                                        {{ $barang->barcode ?? '-' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right hidden md:table-cell">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right hidden sm:table-cell">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-bold
                                        {{ $isCritical ? 'bg-red-200 text-red-800' : ($isLow ? 'bg-yellow-200 text-yellow-800' : 'bg-green-100 text-green-700') }}">
                                        {{ $barang->stok }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-600 hidden lg:table-cell">{{ $barang->satuan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Legend --}}
    <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-500">
        <div class="flex items-center gap-1">
            <span class="inline-block w-3 h-3 rounded bg-red-200"></span>
            Stok kritis (≤ 3)
        </div>
        <div class="flex items-center gap-1">
            <span class="inline-block w-3 h-3 rounded bg-yellow-200"></span>
            Stok rendah (≤ 10)
        </div>
        <div class="flex items-center gap-1">
            <span class="inline-block w-3 h-3 rounded bg-green-100"></span>
            Stok aman (> 10)
        </div>
    </div>
</div>
@endsection

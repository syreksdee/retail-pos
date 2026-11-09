@extends('layouts.app')

@section('title', 'Riwayat Koreksi Stok')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🔄 Riwayat Koreksi Stok</h1>
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

    {{-- Corrections Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        @if($koreksis->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <p class="text-lg mb-1">🔄</p>
                <p class="text-sm">Belum ada riwayat koreksi stok.</p>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium">No</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium">Tanggal</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium">Barang</th>
                            <th class="text-center py-3 px-4 text-gray-600 font-medium">Stok Sebelum</th>
                            <th class="text-center py-3 px-4 text-gray-600 font-medium">Perubahan</th>
                            <th class="text-center py-3 px-4 text-gray-600 font-medium">Stok Sesudah</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium">Alasan</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-medium">Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($koreksis as $i => $koreksi)
                            @php
                                $perubahan = $koreksi->stok_sesudah - $koreksi->stok_sebelum;
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="py-3 px-4 text-gray-600">
                                    {{ \Carbon\Carbon::parse($koreksi->tanggal)->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4 font-medium">{{ $koreksi->barang->nama_barang ?? '-' }}</td>
                                <td class="py-3 px-4 text-center">{{ $koreksi->stok_sebelum }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold
                                        {{ $perubahan >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $perubahan >= 0 ? '+' : '' }}{{ $perubahan }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center font-semibold">{{ $koreksi->stok_sesudah }}</td>
                                <td class="py-3 px-4 text-gray-600 text-xs">{{ $koreksi->alasan ?? '-' }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ $koreksi->user->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200">
                @foreach($koreksis as $i => $koreksi)
                    @php
                        $perubahan = $koreksi->stok_sesudah - $koreksi->stok_sebelum;
                    @endphp
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $koreksi->barang->nama_barang ?? '-' }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($koreksi->tanggal)->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $perubahan >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $perubahan >= 0 ? '+' : '' }}{{ $perubahan }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-500 mb-1">
                            <span>Sebelum: <strong>{{ $koreksi->stok_sebelum }}</strong></span>
                            <span>→</span>
                            <span>Sesudah: <strong>{{ $koreksi->stok_sesudah }}</strong></span>
                        </div>
                        @if($koreksi->alasan)
                            <p class="text-xs text-gray-400 mt-1">📝 {{ $koreksi->alasan }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">Oleh: {{ $koreksi->user->name ?? '-' }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

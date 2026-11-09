@extends('layouts.app')

@section('title', 'Riwayat Barang Masuk')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📥 Riwayat Barang Masuk</h1>
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

    {{-- Incoming Goods Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        @if($barangMasuks->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <p class="text-lg mb-1">📥</p>
                <p class="text-sm">Belum ada riwayat barang masuk.</p>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($barangMasuks as $i => $bm)
                    <div class="p-4" x-data="{ open: false }">
                        {{-- Header Row --}}
                        <div class="flex items-start justify-between cursor-pointer" onclick="this.parentElement.querySelector('.detail-content').classList.toggle('hidden')">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-bold text-gray-700">{{ $i + 1 }}.</span>
                                    <span class="text-sm font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($bm->tanggal)->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-medium">
                                        {{ $bm->detailBarangMasuks->count() }} item
                                    </span>
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    Dicatat oleh: <span class="font-medium">{{ $bm->user->name ?? '-' }}</span>
                                </div>
                                @if($bm->keterangan)
                                    <div class="mt-1 text-xs text-gray-400">
                                        📝 {{ $bm->keterangan }}
                                    </div>
                                @endif
                            </div>
                            <button class="text-blue-500 hover:text-blue-700 text-xs font-medium p-1">
                                ▼ Detail
                            </button>
                        </div>

                        {{-- Detail Content (hidden by default) --}}
                        <div class="detail-content hidden mt-3 bg-gray-50 rounded-lg p-3">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="text-gray-500">
                                        <th class="text-left py-1 px-2">Barang</th>
                                        <th class="text-right py-1 px-2">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bm->detailBarangMasuks as $detail)
                                        <tr class="border-t border-gray-200">
                                            <td class="py-1.5 px-2 font-medium">{{ $detail->barang->nama_barang ?? '-' }}</td>
                                            <td class="py-1.5 px-2 text-right font-semibold text-green-600">+{{ $detail->qty }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

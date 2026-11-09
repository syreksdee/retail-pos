@extends('layouts.app')

@section('title', 'Koreksi Stok Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="flex items-center mb-6">
        <a href="{{ route('gudang.koreksi-stok') }}" class="mr-3 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Koreksi Stok Baru</h2>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('gudang.koreksi-stok.store') }}" method="POST">
            @csrf

            <!-- Pilih Barang -->
            <div class="mb-5">
                <label for="id_barang" class="block text-sm font-medium text-gray-700 mb-1">Pilih Barang <span class="text-red-500">*</span></label>
                <select name="id_barang" id="id_barang" required onchange="showCurrentStok()"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors @error('id_barang') border-red-500 @enderror">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->id }}" data-stok="{{ $barang->stok }}" data-satuan="{{ $barang->satuan }}"
                                {{ old('id_barang') == $barang->id ? 'selected' : '' }}>
                            {{ $barang->nama_barang }}
                        </option>
                    @endforeach
                </select>
                @error('id_barang')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Stock Display -->
            <div id="current-stok-container" class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-blue-700">
                        Stok saat ini: <strong id="current-stok-value">0</strong> <span id="current-satuan-value"></span>
                    </p>
                </div>
            </div>

            <!-- Stok Baru -->
            <div class="mb-5">
                <label for="stok_baru" class="block text-sm font-medium text-gray-700 mb-1">Stok Baru <span class="text-red-500">*</span></label>
                <input type="number" name="stok_baru" id="stok_baru" value="{{ old('stok_baru') }}" required min="0"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors @error('stok_baru') border-red-500 @enderror"
                       placeholder="Masukkan jumlah stok yang benar" oninput="showDifference()">
                @error('stok_baru')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Difference Preview -->
            <div id="difference-container" class="mb-5 p-4 rounded-lg border hidden">
                <p class="text-sm font-medium" id="difference-text"></p>
            </div>

            <!-- Alasan -->
            <div class="mb-6">
                <label for="alasan" class="block text-sm font-medium text-gray-700 mb-1">Alasan <span class="text-red-500">*</span></label>
                <textarea name="alasan" id="alasan" rows="3" required
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors @error('alasan') border-red-500 @enderror"
                          placeholder="Jelaskan alasan koreksi stok (misal: hasil stock opname, barang rusak, dll)">{{ old('alasan') }}</textarea>
                @error('alasan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('gudang.koreksi-stok') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    Simpan Koreksi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentStok = 0;

    function showCurrentStok() {
        const select = document.getElementById('id_barang');
        const container = document.getElementById('current-stok-container');
        const stokValue = document.getElementById('current-stok-value');
        const satuanValue = document.getElementById('current-satuan-value');
        const selected = select.options[select.selectedIndex];

        if (select.value) {
            currentStok = parseInt(selected.dataset.stok);
            stokValue.textContent = currentStok;
            satuanValue.textContent = selected.dataset.satuan;
            container.classList.remove('hidden');
        } else {
            currentStok = 0;
            container.classList.add('hidden');
        }

        showDifference();
    }

    function showDifference() {
        const stokBaru = document.getElementById('stok_baru').value;
        const container = document.getElementById('difference-container');
        const text = document.getElementById('difference-text');
        const select = document.getElementById('id_barang');

        if (stokBaru !== '' && select.value) {
            const diff = parseInt(stokBaru) - currentStok;
            container.classList.remove('hidden');

            if (diff > 0) {
                container.className = 'mb-5 p-4 rounded-lg border bg-green-50 border-green-200';
                text.className = 'text-sm font-medium text-green-700';
                text.textContent = `Perubahan: +${diff} (penambahan stok)`;
            } else if (diff < 0) {
                container.className = 'mb-5 p-4 rounded-lg border bg-red-50 border-red-200';
                text.className = 'text-sm font-medium text-red-700';
                text.textContent = `Perubahan: ${diff} (pengurangan stok)`;
            } else {
                container.className = 'mb-5 p-4 rounded-lg border bg-gray-50 border-gray-200';
                text.className = 'text-sm font-medium text-gray-700';
                text.textContent = 'Tidak ada perubahan stok.';
            }
        } else {
            container.classList.add('hidden');
        }
    }

    // Initialize on page load if old value exists
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('id_barang');
        if (select.value) {
            showCurrentStok();
        }
    });
</script>
@endpush
@endsection

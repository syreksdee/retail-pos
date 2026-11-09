@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="flex items-center mb-6">
        <a href="{{ route('gudang.barang') }}" class="mr-3 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Edit Barang</h2>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('gudang.barang.update', $barang) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Barcode -->
            <div class="mb-5">
                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $barang->barcode) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors @error('barcode') border-red-500 @enderror"
                       placeholder="Scan atau masukkan barcode (opsional)">
                @error('barcode')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Barang -->
            <div class="mb-5">
                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors @error('nama_barang') border-red-500 @enderror"
                       placeholder="Masukkan nama barang">
                @error('nama_barang')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga Beli & Harga Jual -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label for="harga_beli" class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli', $barang->harga_beli) }}" required min="0" step="1"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors @error('harga_beli') border-red-500 @enderror"
                           placeholder="0">
                    @error('harga_beli')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="harga_jual" class="block text-sm font-medium text-gray-700 mb-1">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual', $barang->harga_jual) }}" required min="0" step="1"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors @error('harga_jual') border-red-500 @enderror"
                           placeholder="0">
                    @error('harga_jual')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Satuan (No stok field - managed via barang masuk / koreksi stok) -->
            <div class="mb-5">
                <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                <select name="satuan" id="satuan" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors @error('satuan') border-red-500 @enderror">
                    <option value="">-- Pilih Satuan --</option>
                    @foreach(['pcs', 'botol', 'kg', 'pack', 'dus', 'sachet', 'liter'] as $s)
                        <option value="{{ $s }}" {{ old('satuan', $barang->satuan) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                @error('satuan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Stock Info (read-only) -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-700">
                    <strong>Stok saat ini:</strong> {{ $barang->stok }} {{ $barang->satuan }}
                </p>
                <p class="text-xs text-blue-500 mt-1">Stok dikelola melalui Barang Masuk atau Koreksi Stok.</p>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('gudang.barang') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    Perbarui Barang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

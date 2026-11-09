@extends('layouts.app')

@section('title', 'Point of Sale (Kasir)')

@section('content')
<div class="h-full flex flex-col -m-4 sm:-m-6">
    <!-- Top Action / Shortcut Bar -->
    <div class="bg-slate-900 text-slate-300 px-6 py-2.5 flex items-center justify-between text-xs border-b border-slate-700">
        <div class="flex items-center space-x-4">
            <span class="font-bold text-white flex items-center">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                KASIR ONLINE
            </span>
            <span class="hidden md:inline text-slate-400">|</span>
            <span class="hidden md:inline"><kbd class="px-1.5 py-0.5 bg-slate-800 rounded border border-slate-600 font-mono text-white">F1</kbd> Scan / Cari</span>
            <span class="hidden md:inline"><kbd class="px-1.5 py-0.5 bg-slate-800 rounded border border-slate-600 font-mono text-white">F8</kbd> Nominal Bayar</span>
            <span class="hidden md:inline"><kbd class="px-1.5 py-0.5 bg-slate-800 rounded border border-slate-600 font-mono text-white">F12</kbd> Proses Bayar</span>
            <span class="hidden md:inline"><kbd class="px-1.5 py-0.5 bg-slate-800 rounded border border-slate-600 font-mono text-white">Esc</kbd> Reset Keranjang</span>
        </div>
        <div class="font-medium text-slate-300">
            Kasir: <span class="text-white font-bold">{{ auth()->user()->nama ?? auth()->user()->username }}</span>
        </div>
    </div>

    <!-- Main POS Grid -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-0 overflow-hidden bg-slate-100 min-h-[calc(100vh-140px)]">
        
        <!-- LEFT PANEL: Product Scanner & Cart (8 cols) -->
        <div class="lg:col-span-8 flex flex-col p-4 sm:p-6 border-r border-slate-200">
            
            <!-- Scan / Search Bar -->
            <div class="relative mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        id="barcodeInput" 
                        class="w-full pl-12 pr-28 py-3.5 text-base sm:text-lg bg-white border-2 border-blue-500 rounded-xl shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-100 font-medium placeholder-slate-400" 
                        placeholder="Scan barcode atau ketik nama barang lalu tekan Enter..."
                        autocomplete="off"
                        autofocus
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center space-x-1">
                        <button 
                            type="button" 
                            id="btnSearch"
                            onclick="handleScanOrSearch()"
                            class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                            Cari
                        </button>
                    </div>
                </div>

                <!-- Autocomplete Dropdown Search Results -->
                <div id="searchResults" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 max-h-72 overflow-y-auto divide-y divide-slate-100">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- Cart Table Card -->
            <div class="flex-1 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-bold text-slate-700 text-sm tracking-wide uppercase flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                        Daftar Belanja
                    </h3>
                    <span id="cartCountBadge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        0 item
                    </span>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 sticky top-0 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                            <tr>
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Nama Produk</th>
                                <th class="py-3 px-4 text-right w-28">Harga</th>
                                <th class="py-3 px-4 text-center w-36">Jumlah</th>
                                <th class="py-3 px-4 text-right w-32">Subtotal</th>
                                <th class="py-3 px-4 text-center w-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody" class="divide-y divide-slate-100 text-sm">
                            <tr id="emptyCartRow">
                                <td colspan="6" class="text-center py-16 text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <p class="font-medium text-slate-500">Keranjang masih kosong</p>
                                        <p class="text-xs text-slate-400 mt-1">Scan barcode atau cari barang di atas untuk memulai transaksi</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Payment & Summary (4 cols) -->
        <div class="lg:col-span-4 bg-white p-5 sm:p-6 flex flex-col justify-between border-t lg:border-t-0 border-slate-200 shadow-sm">
            
            <div class="space-y-5">
                <!-- Large Total Display -->
                <div class="bg-gradient-to-br from-slate-900 to-blue-950 text-white rounded-2xl p-5 shadow-lg border border-slate-800">
                    <div class="text-xs uppercase tracking-wider text-slate-400 font-semibold mb-1">Total Pembayaran</div>
                    <div id="grandTotalDisplay" class="text-3xl sm:text-4xl font-extrabold tracking-tight text-blue-400 font-mono">
                        Rp 0
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Nominal Bayar (F8)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-bold text-slate-400 text-base">
                                Rp
                            </span>
                            <input 
                                type="number" 
                                id="nominalBayar" 
                                class="w-full pl-12 pr-4 py-3 text-xl font-bold font-mono text-slate-900 bg-slate-50 border-2 border-slate-300 rounded-xl focus:bg-white focus:border-blue-600 focus:outline-none transition"
                                placeholder="0" 
                                min="0"
                            >
                        </div>
                    </div>

                    <!-- Quick Cash Buttons -->
                    <div>
                        <div class="text-xs font-semibold text-slate-500 mb-2">Uang Cepat:</div>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="setExactAmount()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold transition border border-slate-200">
                                Uang Pas
                            </button>
                            <button type="button" onclick="setNominal(50000)" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold transition border border-slate-200 font-mono">
                                Rp 50.000
                            </button>
                            <button type="button" onclick="setNominal(100000)" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold transition border border-slate-200 font-mono">
                                Rp 100.000
                            </button>
                            <button type="button" onclick="setNominal(200000)" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold transition border border-slate-200 font-mono">
                                Rp 200.000
                            </button>
                        </div>
                    </div>

                    <!-- Kembalian Display Box -->
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500 font-medium">Kembalian:</span>
                            <span id="kembalianDisplay" class="text-xl font-extrabold text-emerald-600 font-mono">
                                Rp 0
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 space-y-3">
                <button 
                    type="button" 
                    id="btnProsesBayar"
                    onclick="prosesTransaksi()"
                    class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl transition-all duration-150 flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>BAYAR SEKARANG (F12)</span>
                </button>

                <button 
                    type="button" 
                    onclick="resetCart()"
                    class="w-full py-2.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 font-semibold text-sm rounded-xl transition border border-slate-200">
                    Batal Transaksi (Esc)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Struk / Transaksi Selesai -->
<div id="successModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl text-center transform transition-all animate-bounce-short">
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800">Transaksi Berhasil!</h3>
        <p id="modalKodeTransaksi" class="text-sm font-mono text-slate-500 mt-1">TRX-XXXX</p>

        <div class="my-5 p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-2 text-sm text-left">
            <div class="flex justify-between">
                <span class="text-slate-500">Total Belanja:</span>
                <span id="modalTotal" class="font-bold font-mono text-slate-800">Rp 0</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Nominal Bayar:</span>
                <span id="modalBayar" class="font-bold font-mono text-slate-800">Rp 0</span>
            </div>
            <div class="flex justify-between border-t border-slate-200 pt-2 text-base">
                <span class="font-semibold text-emerald-700">Kembalian:</span>
                <span id="modalKembalian" class="font-extrabold font-mono text-emerald-600">Rp 0</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button 
                type="button" 
                id="btnCetakStruk" 
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition flex items-center justify-center">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Struk
            </button>
            <button 
                type="button" 
                onclick="closeModalAndNewTrans()" 
                class="w-full py-3 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-md transition">
                Transaksi Baru
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // State
    let cart = [];
    let currentTransactionId = null;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const barcodeInput = document.getElementById('barcodeInput');
    const nominalBayarInput = document.getElementById('nominalBayar');
    const searchResults = document.getElementById('searchResults');

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    // Scan / Search Handler
    async function handleScanOrSearch() {
        const query = barcodeInput.value.trim();
        if (!query) return;

        try {
            const res = await fetch(`{{ route('kasir.cari-barang') }}?q=${encodeURIComponent(query)}`);
            const data = await res.json();

            if (data.success && data.barang) {
                addToCart(data.barang);
                barcodeInput.value = '';
                searchResults.classList.add('hidden');
            } else {
                // If direct match failed, try autocomplete search list
                searchMultiple(query);
            }
        } catch (err) {
            console.error(err);
            alert('Gagal mengambil data produk.');
        }
    }

    // Auto-search dropdown on typing
    let debounceTimer;
    barcodeInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }
        debounceTimer = setTimeout(() => searchMultiple(query), 300);
    });

    async function searchMultiple(query) {
        try {
            const res = await fetch(`{{ route('kasir.search-barang') }}?q=${encodeURIComponent(query)}`);
            const items = await res.json();

            if (!items || items.length === 0) {
                searchResults.innerHTML = `<div class="p-4 text-center text-sm text-slate-400">Barang "${query}" tidak ditemukan</div>`;
                searchResults.classList.remove('hidden');
                return;
            }

            searchResults.innerHTML = items.map(b => `
                <div onclick="selectProduct(${JSON.stringify(b).replace(/"/g, '&quot;')})" 
                     class="p-3 hover:bg-blue-50 cursor-pointer flex items-center justify-between transition">
                    <div>
                        <div class="font-semibold text-slate-800 text-sm">${b.nama_barang}</div>
                        <div class="text-xs text-slate-400">Barcode: ${b.barcode || '-'} | Stok: ${b.stok}</div>
                    </div>
                    <div class="font-bold text-blue-600 font-mono text-sm">
                        ${formatRupiah(b.harga_jual)}
                    </div>
                </div>
            `).join('');

            searchResults.classList.remove('hidden');
        } catch (err) {
            console.error(err);
        }
    }

    function selectProduct(barang) {
        addToCart(barang);
        barcodeInput.value = '';
        searchResults.classList.add('hidden');
        barcodeInput.focus();
    }

    // Add to Cart
    function addToCart(barang) {
        const existing = cart.find(item => item.id === barang.id);

        if (existing) {
            if (existing.qty + 1 > barang.stok) {
                alert(`Stok tidak mencukupi! Sisa stok untuk ${barang.nama_barang}: ${barang.stok}`);
                return;
            }
            existing.qty += 1;
        } else {
            if (barang.stok <= 0) {
                alert(`Stok produk ${barang.nama_barang} habis!`);
                return;
            }
            cart.push({
                id: barang.id,
                barcode: barang.barcode,
                nama_barang: barang.nama_barang,
                harga: Number(barang.harga_jual),
                stok: barang.stok,
                qty: 1
            });
        }

        renderCart();
    }

    // Change Qty
    function updateQty(id, delta) {
        const item = cart.find(i => i.id === id);
        if (!item) return;

        const newQty = item.qty + delta;
        if (newQty <= 0) {
            removeFromCart(id);
            return;
        }

        if (newQty > item.stok) {
            alert(`Stok tidak mencukupi! Stok ${item.nama_barang} hanya tersisa ${item.stok}`);
            return;
        }

        item.qty = newQty;
        renderCart();
    }

    function setQtyDirect(id, value) {
        const item = cart.find(i => i.id === id);
        if (!item) return;

        let qty = parseInt(value, 10);
        if (isNaN(qty) || qty <= 0) qty = 1;

        if (qty > item.stok) {
            alert(`Stok tidak mencukupi! Stok hanya ${item.stok}`);
            qty = item.stok;
        }

        item.qty = qty;
        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        renderCart();
    }

    function resetCart() {
        if (cart.length > 0 && !confirm('Yakin ingin membatalkan transaksi ini?')) return;
        cart = [];
        nominalBayarInput.value = '';
        renderCart();
        barcodeInput.focus();
    }

    function calculateTotal() {
        return cart.reduce((acc, item) => acc + (item.harga * item.qty), 0);
    }

    // Render Table
    function renderCart() {
        const tbody = document.getElementById('cartTableBody');
        const badge = document.getElementById('cartCountBadge');
        const grandDisplay = document.getElementById('grandTotalDisplay');

        if (cart.length === 0) {
            tbody.innerHTML = `
                <tr id="emptyCartRow">
                    <td colspan="6" class="text-center py-16 text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <p class="font-medium text-slate-500">Keranjang masih kosong</p>
                            <p class="text-xs text-slate-400 mt-1">Scan barcode atau cari barang di atas untuk memulai transaksi</p>
                        </div>
                    </td>
                </tr>
            `;
            badge.innerText = '0 item';
            grandDisplay.innerText = 'Rp 0';
            updateKembalian();
            return;
        }

        let totalItems = 0;
        tbody.innerHTML = cart.map((item, idx) => {
            const subtotal = item.harga * item.qty;
            totalItems += item.qty;

            return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition">
                    <td class="py-3 px-4 text-center font-mono text-slate-400 text-xs">${idx + 1}</td>
                    <td class="py-3 px-4">
                        <div class="font-bold text-slate-800">${item.nama_barang}</div>
                        <div class="text-xs text-slate-400 font-mono">${item.barcode || '-'}</div>
                    </td>
                    <td class="py-3 px-4 text-right font-mono text-slate-600">${formatRupiah(item.harga)}</td>
                    <td class="py-3 px-4 text-center">
                        <div class="inline-flex items-center border border-slate-200 rounded-lg overflow-hidden bg-white shadow-sm">
                            <button type="button" onclick="updateQty(${item.id}, -1)" class="px-2.5 py-1 bg-slate-50 hover:bg-slate-200 text-slate-700 font-bold transition">-</button>
                            <input 
                                type="number" 
                                value="${item.qty}" 
                                min="1" 
                                max="${item.stok}"
                                onchange="setQtyDirect(${item.id}, this.value)"
                                class="w-12 text-center text-sm font-semibold font-mono border-0 focus:ring-0 p-1">
                            <button type="button" onclick="updateQty(${item.id}, 1)" class="px-2.5 py-1 bg-slate-50 hover:bg-slate-200 text-slate-700 font-bold transition">+</button>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">${formatRupiah(subtotal)}</td>
                    <td class="py-3 px-4 text-center">
                        <button type="button" onclick="removeFromCart(${item.id})" class="text-rose-500 hover:text-rose-700 p-1 rounded-lg hover:bg-rose-50 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        badge.innerText = `${totalItems} item`;
        const total = calculateTotal();
        grandDisplay.innerText = formatRupiah(total);
        updateKembalian();
    }

    // Payment & Kembalian
    nominalBayarInput.addEventListener('input', updateKembalian);

    function updateKembalian() {
        const total = calculateTotal();
        const bayar = parseFloat(nominalBayarInput.value) || 0;
        const kembalian = bayar - total;
        const kembalianEl = document.getElementById('kembalianDisplay');

        if (kembalian >= 0) {
            kembalianEl.innerText = formatRupiah(kembalian);
            kembalianEl.className = "text-xl font-extrabold text-emerald-600 font-mono";
        } else {
            kembalianEl.innerText = 'Kurang ' + formatRupiah(Math.abs(kembalian));
            kembalianEl.className = "text-xl font-extrabold text-rose-500 font-mono";
        }
    }

    function setExactAmount() {
        nominalBayarInput.value = calculateTotal();
        updateKembalian();
    }

    function setNominal(val) {
        nominalBayarInput.value = val;
        updateKembalian();
    }

    // Submit Transaction
    async function prosesTransaksi() {
        if (cart.length === 0) {
            alert('Keranjang belanja masih kosong!');
            barcodeInput.focus();
            return;
        }

        const total = calculateTotal();
        const bayar = parseFloat(nominalBayarInput.value) || 0;

        if (bayar < total) {
            alert('Uang pembayaran masih kurang!');
            nominalBayarInput.focus();
            return;
        }

        const btn = document.getElementById('btnProsesBayar');
        btn.disabled = true;
        btn.innerHTML = `<span class="animate-spin inline-block mr-2">⏳</span> Memproses...`;

        const payload = {
            bayar: bayar,
            items: cart.map(i => ({
                id_barang: i.id,
                qty: i.qty,
                harga: i.harga
            }))
        };

        try {
            const response = await fetch(`{{ route('kasir.transaksi.store') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const resData = await response.json();

            if (resData.success && resData.transaksi) {
                currentTransactionId = resData.transaksi.id;

                // Show modal
                document.getElementById('modalKodeTransaksi').innerText = resData.transaksi.kode_transaksi;
                document.getElementById('modalTotal').innerText = formatRupiah(resData.transaksi.total);
                document.getElementById('modalBayar').innerText = formatRupiah(resData.transaksi.bayar);
                document.getElementById('modalKembalian').innerText = formatRupiah(resData.transaksi.kembalian);

                document.getElementById('btnCetakStruk').onclick = () => {
                    window.open(`{{ url('/kasir/struk') }}/${currentTransactionId}`, '_blank', 'width=400,height=600');
                };

                document.getElementById('successModal').classList.remove('hidden');
            } else {
                alert(resData.message || 'Terjadi kesalahan saat memproses transaksi.');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan atau server.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>BAYAR SEKARANG (F12)</span>
            `;
        }
    }

    function closeModalAndNewTrans() {
        document.getElementById('successModal').classList.add('hidden');
        cart = [];
        nominalBayarInput.value = '';
        renderCart();
        barcodeInput.focus();
    }

    // Keyboard Shortcuts
    document.addEventListener('keydown', function(e) {
        // F1: Scan / Search
        if (e.key === 'F1') {
            e.preventDefault();
            barcodeInput.focus();
            barcodeInput.select();
        }
        // F8: Focus Bayar
        if (e.key === 'F8') {
            e.preventDefault();
            nominalBayarInput.focus();
            nominalBayarInput.select();
        }
        // F12: Proses Bayar
        if (e.key === 'F12') {
            e.preventDefault();
            prosesTransaksi();
        }
        // Escape: Cancel
        if (e.key === 'Escape') {
            if (!document.getElementById('successModal').classList.contains('hidden')) {
                closeModalAndNewTrans();
            } else {
                resetCart();
            }
        }
        // Enter on Barcode Input
        if (e.key === 'Enter' && document.activeElement === barcodeInput) {
            e.preventDefault();
            handleScanOrSearch();
        }
    });

    // Close autocomplete on click outside
    document.addEventListener('click', function(e) {
        if (!barcodeInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
</script>
@endpush

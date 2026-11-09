<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $transaksi->kode_transaksi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            background: #f3f4f6;
            color: #000;
        }

        .struk {
            max-width: 300px;
            margin: 20px auto;
            background: #fff;
            padding: 15px;
            border: 1px dashed #ccc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .store-name {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .store-info {
            font-size: 10px;
            margin-top: 4px;
            line-height: 1.4;
        }

        .separator {
            border: none;
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            line-height: 1.6;
        }

        .item-name {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding-left: 10px;
            margin-bottom: 6px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            line-height: 1.8;
        }

        .total-row.grand {
            font-weight: bold;
            font-size: 14px;
        }

        .footer {
            font-size: 10px;
            margin-top: 4px;
            line-height: 1.4;
        }

        .no-print {
            text-align: center;
            margin: 20px auto;
            max-width: 300px;
        }

        .no-print button {
            padding: 8px 24px;
            font-size: 14px;
            cursor: pointer;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 4px;
            margin: 0 4px;
        }

        .no-print button:hover {
            background: #1d4ed8;
        }

        .no-print button.secondary {
            background: #6b7280;
        }

        .no-print button.secondary:hover {
            background: #4b5563;
        }

        @media print {
            body {
                background: #fff;
            }

            .struk {
                margin: 0;
                border: none;
                padding: 0;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="struk">
        {{-- Header --}}
        <div class="text-center">
            <div class="store-name">RETAIL POS</div>
            <div class="store-info">
                Jl. Contoh Alamat No. 123<br>
                Kota Manado, Sulawesi Utara<br>
                Telp: (0431) 123-4567
            </div>
        </div>

        <hr class="separator">

        {{-- Transaction Info --}}
        <div class="info-row">
            <span>Kode</span>
            <span>{{ $transaksi->kode_transaksi }}</span>
        </div>
        <div class="info-row">
            <span>Tanggal</span>
            <span>{{ $transaksi->tanggal->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span>Kasir</span>
            <span>{{ $transaksi->user->name ?? '-' }}</span>
        </div>

        <hr class="separator">

        {{-- Items --}}
        @foreach ($transaksi->detailTransaksis as $detail)
            <div class="item-name">{{ $detail->barang->nama_barang }}</div>
            <div class="item-detail">
                <span>{{ $detail->qty }} x Rp {{ number_format($detail->harga, 0, ',', '.') }}</span>
                <span>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
            </div>
        @endforeach

        <hr class="separator">

        {{-- Totals --}}
        <div class="total-row grand">
            <span>TOTAL</span>
            <span>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Bayar</span>
            <span>Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Kembalian</span>
            <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
        </div>

        <hr class="separator">

        {{-- Footer --}}
        <div class="text-center footer">
            Terima kasih atas kunjungan Anda!<br>
            Barang yang sudah dibeli tidak dapat<br>
            ditukar atau dikembalikan.
        </div>
    </div>

    {{-- Print buttons (hidden when printing) --}}
    <div class="no-print">
        <button onclick="window.print()">🖨️ Cetak Struk</button>
        <button class="secondary" onclick="window.close()">Tutup</button>
    </div>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>

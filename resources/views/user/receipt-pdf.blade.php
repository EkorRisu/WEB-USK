<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Digital - Azur Coffee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            background-color: #f59e0b;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header p {
            margin: 0;
            font-size: 16px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            width: 50%;
            padding: 8px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
        }
        .value {
            font-size: 14px;
            color: #000;
        }
        .items-section {
            border-top: 2px solid #e5e7eb;
            padding-top: 20px;
            margin-bottom: 20px;
        }
        .items-section h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .item {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .item-info {
            display: table-cell;
            width: 70%;
        }
        .item-price {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-weight: bold;
        }
        .item-name {
            font-weight: bold;
            margin-bottom: 4px;
        }
        .item-details {
            font-size: 12px;
            color: #666;
        }
        .topping {
            font-size: 11px;
            color: #2563eb;
            margin-left: 16px;
        }
        .summary {
            border-top: 2px solid #e5e7eb;
            padding-top: 15px;
            margin-bottom: 20px;
        }
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .summary-label {
            display: table-cell;
            width: 70%;
        }
        .summary-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-weight: bold;
        }
        .total-row {
            font-size: 16px;
            font-weight: bold;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
            margin-top: 8px;
        }
        .total-row .summary-value {
            color: #059669;
        }
        .payment-info {
            border-top: 2px solid #e5e7eb;
            padding-top: 15px;
            margin-bottom: 20px;
        }
        .payment-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .payment-label {
            display: table-cell;
            width: 40%;
            color: #666;
        }
        .payment-value {
            display: table-cell;
            width: 60%;
            text-align: right;
            font-weight: bold;
        }
        .footer {
            background-color: #f9fafb;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            margin-top: 20px;
        }
        .footer p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        .status-badge {
            background-color: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Azur Coffee</h1>
        <p>Struk Digital Pembayaran</p>
        <div class="status-badge">PEMBAYARAN BERHASIL</div>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell">
                <div class="label">No. Transaksi</div>
                <div class="value">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="info-cell">
                <div class="label">Tanggal</div>
                <div class="value">{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-cell">
                <div class="label">Customer</div>
                <div class="value">{{ $transaction->customer_name ?? $transaction->user->name }}</div>
            </div>
            
        </div>
    </div>

    <div class="items-section">
        <h3>Detail Pesanan</h3>
        @php $subtotal = 0; @endphp
        @foreach($transaction->items as $item)
            @php
                $itemTotal = $item->harga * $item->jumlah;
                $toppingTotal = 0;
                if($item->toppings && is_array($item->toppings)) {
                    $selectedToppings = \App\Models\Topping::whereIn('id', $item->toppings)->get();
                    $toppingTotal = $selectedToppings->sum('price') * $item->jumlah;
                }
                $lineTotal = $itemTotal + $toppingTotal;
                $subtotal += $lineTotal;
            @endphp
            
            <div class="item">
                <div class="item-info">
                    <div class="item-name">{{ $item->nama_barang }}</div>
                    <div class="item-details">{{ $item->jumlah }}x @ Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
                    
                    @if($item->toppings && is_array($item->toppings) && count($item->toppings) > 0)
                        @foreach($selectedToppings as $topping)
                            <div class="topping">+ {{ $topping->name }} (+Rp {{ number_format($topping->price, 0, ',', '.') }})</div>
                        @endforeach
                    @endif
                </div>
                <div class="item-price">Rp {{ number_format($lineTotal, 0, ',', '.') }}</div>
            </div>
        @endforeach
    </div>

    <div class="summary">
        <div class="summary-row">
            <div class="summary-label">Subtotal</div>
            <div class="summary-value">Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row total-row">
            <div class="summary-label">Total Pembayaran</div>
            <div class="summary-value">Rp {{ number_format($transaction->total, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="payment-info">
        <div class="payment-row">
            <div class="payment-label">Metode Pembayaran</div>
            <div class="payment-value">{{ $transaction->metode_pembayaran }}</div>
        </div>
        
        {{-- Cash payment details --}}
        @if($transaction->metode_pembayaran === 'Bayar Tunai' && isset($transaction->cash_amount))
            <div class="payment-row">
                <div class="payment-label">Uang Tunai</div>
                <div class="payment-value">Rp {{ number_format($transaction->cash_amount, 0, ',', '.') }}</div>
            </div>
            @if(isset($transaction->change_amount) && $transaction->change_amount > 0)
                <div class="payment-row">
                    <div class="payment-label">Kembalian</div>
                    <div class="payment-value"><strong>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</strong></div>
                </div>
            @endif
        @endif
        
        @if(isset($transaction->bank_tujuan))
            <div class="payment-row">
                <div class="payment-label">Bank</div>
                <div class="payment-value">{{ $transaction->bank_tujuan }}</div>
            </div>
        @endif
        @if(isset($transaction->nama_pengirim))
            <div class="payment-row">
                <div class="payment-label">Pengirim</div>
                <div class="payment-value">{{ $transaction->nama_pengirim }}</div>
            </div>
        @endif
    </div>

    <div class="footer">
        <p><strong>Terima kasih telah berbelanja di Azur Coffee</strong></p>
        <p>Simpan struk ini sebagai bukti pembayaran yang sah</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
{{-- <!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Struk Pembelian</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 15px;
            background-color: #f9fafb;
            color: #1f2937;
            padding: 30px;
            line-height: 1.6;
        }
    
        .header {
            text-align: center;
            font-weight: 600;
            font-size: 22px;
            color: #111827;
            margin-bottom: 25px;
        }
    
        .header small {
            display: block;
            font-size: 13px;
            color: #6b7280;
            margin-top: 5px;
        }
    
        .info {
            margin-bottom: 25px;
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
    
        .info p {
            margin: 6px 0;
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 0 1px #e5e7eb;
        }
    
        th {
            background-color: #2563eb;
            color: white;
            padding: 10px;
            font-weight: 600;
            text-align: left;
        }
    
        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            background-color: #ffffff;
        }
    
        tr:last-child td {
            border-bottom: none;
        }
    
        .total-row td {
            font-weight: bold;
            background-color: #fef3c7;
            border-top: 2px solid #fbbf24;
            color: #111827;
        }
    
        .footer {
            margin-top: 40px;
            text-align: center;
            font-style: italic;
            font-size: 14px;
            color: #6b7280;
        }
    </style>

</head>

<body>

    <div class="header">
        ADR STORE<br>
        <small>Struk Pembelian #{{ $transaksi->id }}</small>
    </div>

    <div class="info">
        <p><strong>Nama:</strong> {{ $transaksi->user->name }}</p>
        <p><strong>Alamat:</strong> {{ $transaksi->alamat }}</p>
        <p><strong>Telepon:</strong> {{ $transaksi->telepon }}</p>
        <p><strong>Status:</strong> {{ ucfirst($transaksi->status) }}</p>
        <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaksi->metode_pembayaran) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi->items as $item)
                <tr>
                    <td>{{ $item->produk->nama }}</td>
                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" align="right">Total</td>
                <td>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Terima kasih telah berbelanja di ADR STORE
    </div>

</body>

</html> --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }

        .header { background: #394766; color: white; padding: 18px 24px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; font-weight: bold; }
        .header p  { font-size: 11px; opacity: 0.8; margin-top: 3px; }

        .period { font-size: 11px; color: #666; margin: 0 24px 14px; }

        table { width: calc(100% - 48px); margin: 0 24px; border-collapse: collapse; }
        thead tr { background: #394766; color: white; }
        thead th { padding: 9px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody tr:nth-child(even) { background: #f5f4ef; }
        tbody td { padding: 8px 10px; border-bottom: 1px solid #e5e5e5; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: bold; }
        .badge-paid       { background: #C1F2D0; color: #166534; }
        .badge-pending    { background: #FEF9C3; color: #854D0E; }
        .badge-cancelled  { background: #F7CDCD; color: #991B1B; }

        .badge-cash   { background: #BFDCDE; color: #1e4d50; }
        .badge-qris   { background: #DBC5E8; color: #4a1d7d; }
        .badge-ewallet{ background: #FDE68A; color: #78350F; }

        .footer { margin: 20px 24px 0; font-size: 10px; color: #999; border-top: 1px solid #e5e5e5; padding-top: 10px; }
        .total-row { font-weight: bold; background: #E4DFB5 !important; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Riwayat Transaksi</h1>
        <p>WARIS — Warung Informasi Sales</p>
    </div>

    @if($from || $to)
    <p class="period">
        Periode:
        {{ $from ? \Carbon\Carbon::parse($from)->format('d M Y') : 'Awal' }}
        —
        {{ $to ? \Carbon\Carbon::parse($to)->format('d M Y') : 'Sekarang' }}
    </p>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:20%">Invoice</th>
                <th style="width:18%">Tanggal</th>
                <th style="width:16%">Total</th>
                <th style="width:14%">Metode</th>
                <th style="width:12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $i => $order)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $order->invoice_no }}</td>
                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                <td>
                    @php $metode = optional($order->payment)->method ?? '-'; @endphp
                    <span class="badge badge-{{ $metode }}">{{ ucfirst($metode) }}</span>
                </td>
                <td>
                    <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align:right; padding-right:12px;">Total Keseluruhan</td>
                <td>Rp {{ number_format($orders->sum('total'), 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }} &nbsp;|&nbsp; Total transaksi: {{ $orders->count() }}
    </div>

</body>
</html>
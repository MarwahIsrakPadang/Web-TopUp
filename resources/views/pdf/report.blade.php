<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 20px; margin-bottom: 5px; }
        .subtitle { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; font-weight: 600; }
        .text-right { text-align: right; }
        .mb-10 { margin-bottom: 10px; }
        .summary-box { display: inline-block; margin-right: 20px; margin-bottom: 20px; }
        .summary-value { font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan</h1>
    <p class="subtitle">
        Periode: {{ $filters['start_date'] ?? 'Awal' }} — {{ $filters['end_date'] ?? 'Sekarang' }}
    </p>

    <div class="mb-10">
        <div class="summary-box">
            <div>Total Pesanan</div>
            <div class="summary-value">{{ number_format($summary['total_orders'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div>Total Pendapatan</div>
            <div class="summary-value">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div>Total Biaya</div>
            <div class="summary-value">Rp {{ number_format($summary['total_fee'], 0, ',', '.') }}</div>
        </div>
    </div>

    <h3>Penjualan Harian</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th class="text-right">Pesanan</th>
                <th class="text-right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dailySales as $day)
            <tr>
                <td>{{ $day->date }}</td>
                <td class="text-right">{{ number_format($day->total_orders, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($day->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="3">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>Top 10 Game</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Game</th>
                <th class="text-right">Pesanan</th>
                <th class="text-right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topGames as $i => $game)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $game->game_name }}</td>
                <td class="text-right">{{ number_format($game->total_orders, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($game->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p style="text-align:right;color:#999;margin-top:30px;">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>

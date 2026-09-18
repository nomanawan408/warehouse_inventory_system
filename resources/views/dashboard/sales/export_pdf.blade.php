<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report - {{ ucfirst($filter) }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h2 { margin-bottom: 0; }
        .meta { color: #666; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 5px 6px; text-align: left; }
        th { background: #2c3e50; color: #fff; }
        tr:nth-child(even) td { background: #f2f2f2; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h2>Sales Report ({{ ucfirst($filter) }})</h2>
    <p class="meta">Generated: {{ $generatedAt }} &middot; Rows: {{ $rows->count() }}</p>
    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Customer</th>
                <th class="right">Total</th>
                <th class="right">Discount</th>
                <th class="right">Net Total</th>
                <th class="right">Paid</th>
                <th class="right">Pending</th>
                <th>Last Update</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                <td># {{ str_pad($row->id, 3, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $row->customer_name ?? 'Deleted Customer' }}</td>
                <td class="right">{{ $row->total_amount }}</td>
                <td class="right">{{ $row->discount }}</td>
                <td class="right">{{ $row->net_total }}</td>
                <td class="right">{{ $row->amount_paid }}</td>
                <td class="right">{{ $row->pending_amount }}</td>
                <td>{{ $row->updated_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Audit Logs</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 16px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1e293b; color: #fff; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge-created { color: #16a34a; }
        .badge-deleted { color: #dc2626; }
        .badge-updated { color: #2563eb; }
        .footer { margin-top: 16px; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>Audit Logs — {{ config('app.name') }}</h1>
    <p style="font-size:9px;color:#6b7280;margin-bottom:12px;">Generated {{ now()->format('M d, Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Action</th>
                <th>Model</th>
                <th>ID</th>
                <th>IP</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->user?->name ?? 'System' }}</td>
                    <td class="badge-{{ $log->action }}">{{ $log->action }}</td>
                    <td>{{ class_basename($log->model_type) }}</td>
                    <td>{{ $log->model_id }}</td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Total: {{ $logs->count() }} records</div>
</body>
</html>

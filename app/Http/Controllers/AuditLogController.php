<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->get('search');
        $action  = $request->get('action');
        $from    = $request->get('from');
        $to      = $request->get('to');

        $logs = AuditLog::with('user')
            ->when($search, fn ($q) => $q->where('action', 'ilike', "%{$search}%")
                ->orWhere('model_type', 'ilike', "%{$search}%"))
            ->when($action, fn ($q) => $q->where('action', $action))
            ->when($from,   fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to,     fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit-logs.index', compact('logs', 'search', 'action'));
    }

    public function export(Request $request)
    {
        $logs = AuditLog::with('user')->latest()->limit(1000)->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit-logs.csv"',
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'User', 'Action', 'Model Type', 'Model ID', 'IP', 'Created At']);
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    $log->action,
                    $log->model_type,
                    $log->model_id,
                    $log->ip_address,
                    $log->created_at->toDateTimeString(),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $logs = AuditLog::with('user')->latest()->limit(200)->get();
        $pdf  = Pdf::loadView('admin.audit-logs.pdf', compact('logs'))->setPaper('a4', 'landscape');

        return $pdf->download('audit-logs.pdf');
    }
}

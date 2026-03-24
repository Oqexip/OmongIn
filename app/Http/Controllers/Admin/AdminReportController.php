<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'open');

        $reports = Report::when($status !== 'all', fn($q) => $q->status($status))
            ->with('reportable', 'resolvedBy')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends(['status' => $status]);

        return view('admin.reports.index', compact('reports', 'status'));
    }

    public function show(Report $report)
    {
        $report->load('reportable', 'resolvedBy');

        return view('admin.reports.show', compact('report'));
    }

    public function resolve(Request $request, Report $report)
    {
        $data = $request->validate([
            'action' => ['required', 'in:reviewed,dismissed'],
            'delete_content' => ['nullable' , 'boolean'],
        ]);

        $report->update([
            'status'      => $data['action'],
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        // Optionally delete the reported content
        if (!empty($data['delete_content']) && $report->reportable) {
            $report->reportable->delete();
        }

        $label = $data['action'] === 'reviewed' ? 'ditinjau' : 'ditolak';
        return redirect()->route('admin.reports.index')->with('ok', "Laporan #{$report->id} ditandai {$label}.");
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return back()->with('ok', 'Laporan dihapus.');
    }
}

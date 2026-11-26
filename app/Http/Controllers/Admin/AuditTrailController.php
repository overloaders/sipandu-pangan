<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Activitylog\Models\Activity;

class AuditTrailController extends Controller
{
    /**
     * Display a listing of activities.
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by event type
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by model type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter by log name
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'LIKE', "%{$request->search}%");
        }

        $activities = $query->latest()->paginate($request->input('per_page', 25));

        // Get unique users and models for filters
        $users = \App\Models\User::orderBy('name')->get();
        $subjectTypes = Activity::select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->pluck('subject_type');

        return view('admin.audit-trail.index', compact('activities', 'users', 'subjectTypes'));
    }

    /**
     * Display the specified activity.
     */
    public function show(Activity $activity)
    {
        $activity->load(['causer', 'subject']);
        return view('admin.audit-trail.show', compact('activity'));
    }

    /**
     * Get activities for a specific model.
     */
    public function modelHistory(Request $request, string $subjectType, int $subjectId)
    {
        $activities = Activity::where('subject_type', $subjectType)
            ->where('subject_id', $subjectId)
            ->with('causer')
            ->latest()
            ->paginate(25);

        return view('admin.audit-trail.model-history', compact('activities', 'subjectType', 'subjectId'));
    }

    public function modelHistoryPdf(string $subjectType, int $subjectId)
    {
        $activities = Activity::where('subject_type', $subjectType)
            ->where('subject_id', $subjectId)
            ->with('causer')
            ->latest()
            ->get();

        $subjectName = $this->subjectLabel($subjectType, $subjectId);
        $pdf = Pdf::loadView('reports.audit-trail-model-history', compact('activities', 'subjectType', 'subjectId', 'subjectName'))
            ->setPaper('a4', 'portrait')
            ->output();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="audit-trail-' . class_basename($subjectType) . '-' . $subjectId . '.pdf"',
        ]);
    }

    /**
     * Label ringkas untuk subjek (nama/kode) agar PDF mudah dikenali.
     */
    private function subjectLabel(string $subjectType, int $subjectId): string
    {
        $model = class_exists($subjectType) ? $subjectType::find($subjectId) : null;
        if ($model) {
            foreach (['nama_lengkap', 'nama_item', 'nama_kandang', 'nama_kolam', 'nama_lahan', 'judul', 'nama_program', 'nama_pelatihan', 'nama_menu', 'nama_blok', 'kode_transaksi_keu', 'kode_komplians', 'nomor_induk', 'kode_item', 'kode_kamar', 'kode_laporan', 'kode_distribusi', 'kode_opname', 'kode_batch', 'kode_rencana', 'kode_rencana_gizi', 'kode_pelatihan', 'kode_program'] as $field) {
                if (($model->{$field} ?? null)) {
                    return (string) $model->{$field};
                }
            }
            return '#' . $model->getKey();
        }
        return '#' . $subjectId;
    }

    /**
     * Get activity statistics.
     */
    public function statistics()
    {
        $stats = [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', today())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Activity::whereMonth('created_at', now()->month)->count(),
            'by_event' => Activity::selectRaw('event, COUNT(*) as count')
                ->groupBy('event')
                ->pluck('count', 'event'),
            'by_model' => Activity::selectRaw('subject_type, COUNT(*) as count')
                ->groupBy('subject_type')
                ->pluck('count', 'subject_type'),
            'by_user' => Activity::selectRaw('causer_id, COUNT(*) as count')
                ->groupBy('causer_id')
                ->with('causer:id,name')
                ->get()
                ->pluck('count', 'causer.name'),
            'recent' => Activity::with('causer')
                ->latest()
                ->limit(10)
                ->get(),
        ];

        return view('admin.audit-trail.statistics', compact('stats'));
    }

    /**
     * Clean up old activities.
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 365);
        
        $deleted = Activity::where('created_at', '<', now()->subDays($days))->delete();

        return redirect()->route('admin.audit-trail.index')
            ->with('success', "Berhasil menghapus {$deleted} activity log yang lebih tua dari {$days} hari");
    }

    /**
     * Export activities to CSV.
     */
    public function export(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $activities = $query->latest()->get();

        $filename = 'activity_log_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($activities) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID',
                'User',
                'Event',
                'Subject Type',
                'Subject ID',
                'Description',
                'IP Address',
                'Created At'
            ]);

            // Data
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->id,
                    $activity->causer?->name ?? 'System',
                    $activity->event,
                    $activity->subject_type,
                    $activity->subject_id,
                    $activity->description,
                    $activity->ip_address,
                    $activity->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

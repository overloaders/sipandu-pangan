<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SlowQueryController extends Controller
{
    /**
     * Tampilkan ringkasan slow queries dari storage/logs/slow-queries-*.log.
     */
    public function index(Request $request)
    {
        $days = (int) $request->input('days', 14);
        $days = min(max($days, 1), 90);
        $type = $request->input('type'); // slow | high | null (semua)

        $entries = $this->readEntries($days);
        $entries = array_values(array_filter($entries, function ($e) use ($type) {
            if ($type === 'slow') return $e['message'] === 'Slow query';
            if ($type === 'high') return $e['message'] === 'High query count for request';
            return true;
        }));

        $total = count($entries);
        $today = now()->format('Y-m-d');
        $totalToday = count(array_filter($entries, fn($e) => $e['date'] === $today));

        $msValues = array_map(fn($e) => (float) ($e['payload']['time_ms'] ?? 0), $entries);
        $slowestMs = $msValues ? max($msValues) : 0;
        $avgMs = $msValues ? round(array_sum($msValues) / count($msValues), 1) : 0;

        // Query terlambat teratas (hanya entri Slow query)
        $slowEntries = array_values(array_filter($entries, fn($e) => $e['message'] === 'Slow query'));
        usort($slowEntries, fn($a, $b) => ($b['payload']['time_ms'] ?? 0) <=> ($a['payload']['time_ms'] ?? 0));
        $topSlow = array_slice($slowEntries, 0, 15);

        // Kelompokkan per SQL (normalisasi whitespace untuk menggabung varian)
        $bySql = collect($slowEntries)
            ->groupBy(fn($e) => $this->normalizeSql($e['payload']['sql'] ?? ''))
            ->map(function (Collection $group) {
                $ms = $group->pluck('payload.time_ms')->map(fn($v) => (float) $v);
                return [
                    'sql' => $group->first()['payload']['sql'] ?? '',
                    'count' => $group->count(),
                    'max_ms' => $ms->max(),
                    'avg_ms' => round($ms->avg(), 1),
                ];
            })
            ->sortByDesc('max_ms')
            ->take(15)
            ->values();

        // Kelompokkan per URL request
        $byUrl = collect($entries)
            ->groupBy(fn($e) => $e['payload']['request']['url'] ?? '(tanpa konteks)')
            ->map(fn(Collection $group) => ['url' => $group->first()['payload']['request']['url'] ?? '(tanpa konteks)', 'count' => $group->count()])
            ->sortByDesc('count')
            ->take(10)
            ->values();

        // Per hari (14 hari terakhir)
        $byDay = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $byDay[$d] = count(array_filter($entries, fn($e) => $e['date'] === $d));
        }

        // Entri terbaru
        $recent = array_slice($entries, 0, 50);

        $logEnabled = (bool) config('database.slow_query.enabled');
        $threshold = (int) config('database.slow_query.threshold_ms');
        $maxPerRequest = (int) config('database.slow_query.max_queries_per_request');

        return view('admin.slow-queries', compact(
            'entries', 'total', 'totalToday', 'slowestMs', 'avgMs',
            'topSlow', 'bySql', 'byUrl', 'byDay', 'recent',
            'logEnabled', 'threshold', 'maxPerRequest', 'days', 'type'
        ));
    }

    /**
     * Baca & parse semua file slow-queries-*.log (terbaru dulu).
     */
    private function readEntries(int $days): array
    {
        $entries = [];
        $files = glob(storage_path('logs/slow-queries-*.log'));
        if (!$files) return $entries;

        rsort($files); // nama file berformat Y-m-d, sortir menurun = terbaru dulu

        $cutoff = now()->subDays($days)->format('Y-m-d');

        foreach ($files as $file) {
            $fileDate = basename($file, '.log');
            // slow-queries-2026-08-17.log → ambil tanggal dari nama
            if (preg_match('/slow-queries-(\d{4}-\d{2}-\d{2})\.log$/', $file, $fm)) {
                if ($fm[1] < $cutoff) continue;
            }

            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (!$lines) continue;

            foreach ($lines as $line) {
                // [2026-08-17 10:05:39] production.WARNING: Slow query   {"time_ms":...}
                if (!preg_match('/^\[(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2})\]\s+\S+\.(\w+):\s+(.*)$/', $line, $m)) {
                    continue;
                }
                $rest = $m[4];
                $pos = strpos($rest, '{');
                $message = trim($pos !== false ? substr($rest, 0, $pos) : $rest);
                $payload = [];
                if ($pos !== false) {
                    $decoded = json_decode(substr($rest, $pos), true);
                    if (is_array($decoded)) $payload = $decoded;
                }
                $entries[] = [
                    'date' => $m[1],
                    'time' => $m[2],
                    'level' => $m[3],
                    'message' => $message,
                    'payload' => $payload,
                ];
            }
        }

        return $entries;
    }

    /**
     * Normalisasi SQL: runtuhkan whitespace berlebih agar query sejenis
     * (beda nilai literal) bisa dikelompokkan.
     */
    private function normalizeSql(string $sql): string
    {
        $sql = preg_replace('/\s+/', ' ', trim($sql)) ?? $sql;
        // Ganti literal angka & string dengan placeholder
        $sql = preg_replace('/\b\d+\b/', '?', $sql) ?? $sql;
        $sql = preg_replace("/'[^']*'/", '?', $sql) ?? $sql;
        return substr($sql, 0, 200);
    }
}

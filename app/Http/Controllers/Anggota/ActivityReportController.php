<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\ActivityReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ActivityReportController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityReport::where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('tanggal', 'desc')->get();

        return view('anggota.reports.index', compact('reports'));
    }

    public function export()
    {
        $user = Auth::user();

        // Get all reports sorted by date
        $reports = ActivityReport::where('user_id', $user->id)
            ->orderBy('tanggal', 'asc')
            ->get();

        // Load the provided template file
        $templatePath = base_path('LAPORAN MINGGUAN (INDIVIDU).xlsx');
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template file not found.'], 404);
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Fill student metadata
        $sheet->setCellValue('A2', 'NAMA MAHASISWA : ' . strtoupper($user->name));

        // 2. Map weekly schedule from KKN 2026 (starting July 20) and their row indices in the template
        $weeks = [
            [
                'start' => Carbon::create(2026, 7, 20)->startOfDay(),
                'end' => Carbon::create(2026, 7, 26)->endOfDay(),
                'start_row' => 10,
                'header_cell' => 'A10',
                'header_text' => '20 - 26 Juli 2026',
            ],
            [
                'start' => Carbon::create(2026, 7, 27)->startOfDay(),
                'end' => Carbon::create(2026, 8, 2)->endOfDay(),
                'start_row' => 17,
                'header_cell' => 'A17',
                'header_text' => '27 Juli - 2 Agustus 2026',
            ],
            [
                'start' => Carbon::create(2026, 8, 3)->startOfDay(),
                'end' => Carbon::create(2026, 8, 9)->endOfDay(),
                'start_row' => 23,
                'header_cell' => 'A23',
                'header_text' => '3 - 9 Agustus 2026',
            ],
            [
                'start' => Carbon::create(2026, 8, 10)->startOfDay(),
                'end' => Carbon::create(2026, 8, 16)->endOfDay(),
                'start_row' => 29,
                'header_cell' => 'A29',
                'header_text' => '10 - 16 Agustus 2026',
            ],
            [
                'start' => Carbon::create(2026, 8, 17)->startOfDay(),
                'end' => Carbon::create(2026, 8, 20)->endOfDay(),
                'start_row' => 35,
                'header_cell' => 'A35',
                'header_text' => '17 - 20 Agustus 2026',
            ],
        ];

        // Clear the 6th week header (A41) since it's unused now
        $sheet->setCellValue('A41', '');

        // 3. Populate data into corresponding week sections dynamically
        $rowOffset = 0;

        foreach ($weeks as $week) {
            $startRow = $week['start_row'] + $rowOffset;

            // Dynamically update the header text in the Excel sheet
            $sheet->setCellValue("A{$startRow}", $week['header_text']);

            $weekReports = $reports->filter(function ($r) use ($week) {
                $d = Carbon::parse($r->tanggal);
                return $d->between($week['start'], $week['end']);
            })->values();

            $reportCount = $weekReports->count();

            // If there are more reports than the template's default 6 rows per week, insert new rows
            if ($reportCount > 6) {
                $extraRows = $reportCount - 6;
                $sheet->insertNewRowBefore($startRow + 6, $extraRows);
                $rowOffset += $extraRows;
            }

            $totalRows = max(6, $reportCount);

            for ($i = 0; $i < $totalRows; $i++) {
                $row = $startRow + $i;
                $report = $weekReports->get($i);

                if ($report) {
                    $sheet->setCellValue("B{$row}", $i + 1);
                    $sheet->setCellValue("C{$row}", $report->nama_kegiatan);
                    $sheet->setCellValue("D{$row}", Carbon::parse($report->deadline)->format('d/m/Y'));
                    $sheet->setCellValue("E{$row}", $report->person_in_charge ?? '-');
                    $sheet->setCellValue("F{$row}", $report->status);
                    $sheet->setCellValue("G{$row}", $report->notes ?? '');

                    // Dynamically set background color matching status
                    $statusColor = match($report->status) {
                        'Done'        => '00CC66', // Green
                        'In Progress' => 'FFAA00', // Orange
                        default       => '00CCCC', // Cyan for To Do
                    };

                    $sheet->getStyle("F{$row}")->getFill()->setFillType(Fill::FILL_SOLID);
                    $sheet->getStyle("F{$row}")->getFill()->getStartColor()->setRGB($statusColor);
                } else {
                    // Clear cell content if no report for this row
                    if ($i >= 5) {
                        $sheet->setCellValue("B{$row}", '');
                    }
                    $sheet->setCellValue("C{$row}", '');
                    $sheet->setCellValue("D{$row}", '');
                    $sheet->setCellValue("E{$row}", '');
                    $sheet->setCellValue("F{$row}", '');
                    $sheet->setCellValue("G{$row}", '');
                    $sheet->getStyle("F{$row}")->getFill()->setFillType(Fill::FILL_NONE);
                }
            }
        }

        // 4. Output stream to download
        $filename = 'Laporan_Mingguan_KKN_' . str_replace(' ', '_', $user->name) . '_' . now()->format('Ymd') . '.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'           => 'required|date',
            'nama_kegiatan'     => 'required|string|max:255',
            'deadline'          => 'required|date',
            'person_in_charge'  => 'nullable|string|max:255',
            'status'            => 'required|in:To Do,In Progress,Done',
            'notes'             => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['pic']     = null;

        ActivityReport::create($validated);

        return redirect()->back()->with('success', 'Laporan kegiatan berhasil ditambahkan.');
    }

    public function update(Request $request, ActivityReport $report)
    {
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'tanggal'           => 'required|date',
            'nama_kegiatan'     => 'required|string|max:255',
            'deadline'          => 'required|date',
            'person_in_charge'  => 'nullable|string|max:255',
            'status'            => 'required|in:To Do,In Progress,Done',
            'notes'             => 'nullable|string',
        ]);

        $report->update($validated);

        return redirect()->back()->with('success', 'Laporan kegiatan berhasil diupdate.');
    }

    public function uploadPic(Request $request, ActivityReport $report)
    {
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'pic' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('pic')) {
            // Delete old local file if it exists and is not a URL
            if ($report->pic && !filter_var($report->pic, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($report->pic)) {
                Storage::disk('public')->delete($report->pic);
            }

            $file = $request->file('pic');
            $path = $file->store('activity_docs', 'public');
            $picValue = $path;

            // Try to upload to Google Drive if configured
            $googleDrive = app(\App\Services\GoogleDriveService::class);
            if ($googleDrive->isConfigured()) {
                $absoluteLocalPath = storage_path('app/public/' . $path);
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                $driveUrl = $googleDrive->uploadFile(
                    $absoluteLocalPath,
                    $fileName,
                    $report->user->name
                );

                if ($driveUrl) {
                    $picValue = $driveUrl;
                }
            }

            $report->update(['pic' => $picValue]);

            $message = $googleDrive->isConfigured()
                ? 'Dokumentasi berhasil diunggah ke Google Drive.'
                : 'Dokumentasi berhasil diunggah secara lokal (Google Drive belum dikonfigurasi).';

            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'Gagal mengunggah gambar.');
    }

    public function destroy(ActivityReport $report)
    {
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        if ($report->pic && Storage::disk('public')->exists($report->pic)) {
            Storage::disk('public')->delete($report->pic);
        }

        $report->delete();

        return redirect()->back()->with('success', 'Laporan kegiatan berhasil dihapus.');
    }
}

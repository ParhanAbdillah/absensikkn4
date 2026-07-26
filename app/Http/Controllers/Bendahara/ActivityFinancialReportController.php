<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\ActivityFinancialReport;
use App\Models\ActivityFinancialItem;
use App\Models\FinanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ActivityFinancialReportController extends Controller
{
    public function index()
    {
        $reports = ActivityFinancialReport::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('bendahara.finance.activities.index', compact('reports'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_name' => 'required|string|max:255',
            'date' => 'required|date',
            'city' => 'required|string|max:100',
            'cash_allocation' => 'nullable|numeric|min:0',
        ]);

        $report = ActivityFinancialReport::create([
            'activity_name' => $validated['activity_name'],
            'date' => $validated['date'],
            'city' => $validated['city'],
            'user_id' => Auth::id(),
        ]);

        $cashAllocation = $validated['cash_allocation'] ?? 0;
        if ($cashAllocation > 0) {
            // 1. Create income item inside this activity report
            $report->items()->create([
                'type' => 'income',
                'description' => 'Pemasukan dari kas',
                'qty' => 1,
                'price' => $cashAllocation,
                'total' => $cashAllocation,
            ]);

            // 2. Log transaction as expense in General Cash Book (finance_transactions table)
            FinanceTransaction::create([
                'date' => $validated['date'],
                'type' => 'expense',
                'category' => 'Lain-lain',
                'amount' => $cashAllocation,
                'description' => 'Alokasi Dana Kegiatan KKN: ' . $validated['activity_name'],
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('finance.activities.show', $report->id)->with('success', 'Laporan kegiatan berhasil dibuat dan dana alokasi tercatat!');
    }

    public function show(ActivityFinancialReport $report)
    {
        $incomeItems = $report->items()->where('type', 'income')->get();
        $expenseItems = $report->items()->where('type', 'expense')->get();

        $totalIncome = $incomeItems->sum('total');
        $totalExpense = $expenseItems->sum('total');
        $balance = $totalIncome - $totalExpense;

        return view('bendahara.finance.activities.show', compact('report', 'incomeItems', 'expenseItems', 'totalIncome', 'totalExpense', 'balance'));
    }

    public function destroy(ActivityFinancialReport $report)
    {
        if (Auth::id() !== $report->user_id && !Auth::user()->isKoordinator()) {
            abort(403, 'Anda tidak berhak menghapus laporan ini.');
        }

        $report->delete();

        return redirect()->route('finance.activities.index')->with('success', 'Laporan kegiatan berhasil dihapus.');
    }

    public function storeItem(Request $request, ActivityFinancialReport $report)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'description' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $total = $validated['qty'] * $validated['price'];

        $report->items()->create([
            'type' => $validated['type'],
            'description' => $validated['description'],
            'qty' => $validated['qty'],
            'price' => $validated['price'],
            'total' => $total,
        ]);

        return redirect()->route('finance.activities.show', $report->id)->with('success', 'Rincian laporan berhasil ditambahkan!');
    }

    public function destroyItem(ActivityFinancialItem $item)
    {
        $reportId = $item->activity_financial_report_id;
        $report = ActivityFinancialReport::findOrFail($reportId);

        if (Auth::id() !== $report->user_id && !Auth::user()->isKoordinator()) {
            abort(403, 'Anda tidak berhak menghapus item ini.');
        }

        $item->delete();

        return redirect()->route('finance.activities.show', $reportId)->with('success', 'Item berhasil dihapus dari laporan.');
    }

    public function export(ActivityFinancialReport $report)
    {
        $incomeItems = $report->items()->where('type', 'income')->get();
        $expenseItems = $report->items()->where('type', 'expense')->get();

        $totalIncome = $incomeItems->sum('total');
        $totalExpense = $expenseItems->sum('total');
        $balance = $totalIncome - $totalExpense;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kegiatan');

        // Font settings
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        // Header style (Emerald green matching the application theme)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'], // emerald-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
            ],
        ];

        // Summary yellow/gray style
        $summaryStyle = [
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6'], // gray-100
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
            ],
        ];

        $borderThin = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']],
            ],
        ];

        $currentRow = 2;

        // TITLE
        $sheet->setCellValue('A' . $currentRow, 'LAPORAN KEUANGAN KEGIATAN: ' . strtoupper($report->activity_name));
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);
        $currentRow += 2;

        // 1. RENCANA PEMASUKAN
        $sheet->setCellValue('A' . $currentRow, '1. Rencana Pemasukan');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        // Headers
        $sheet->setCellValue('A' . $currentRow, 'No');
        $sheet->setCellValue('B' . $currentRow, 'Uraian');
        $sheet->setCellValue('C' . $currentRow, 'Qty');
        $sheet->setCellValue('D' . $currentRow, 'Harga');
        $sheet->setCellValue('E' . $currentRow, 'Total');
        $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($currentRow)->setRowHeight(26);
        $incomeHeaderRow = $currentRow;
        $currentRow++;

        $no = 1;
        $incomeStartRow = $currentRow;
        foreach ($incomeItems as $item) {
            $sheet->setCellValue('A' . $currentRow, $no++);
            $sheet->setCellValue('B' . $currentRow, $item->description);
            $sheet->setCellValue('C' . $currentRow, $item->qty);
            $sheet->setCellValue('D' . $currentRow, $item->price);
            $sheet->setCellValue('E' . $currentRow, $item->total);

            // Alignment and formatting
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $currentRow)->getNumberFormat()->setFormatCode('Rp #,##0');
            $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode('Rp #,##0');
            $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($borderThin);
            $currentRow++;
        }
        $incomeEndRow = $currentRow - 1;

        // Total Income Row
        $sheet->setCellValue('A' . $currentRow, 'Total');
        $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($incomeItems->isEmpty()) {
            $sheet->setCellValue('E' . $currentRow, 0);
        } else {
            $sheet->setCellValue('E' . $currentRow, '=SUM(E' . $incomeStartRow . ':E' . $incomeEndRow . ')');
        }

        $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode('Rp #,##0');
        $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($summaryStyle);
        $totalIncomeCellRow = $currentRow;
        $currentRow += 3;

        // 2. RENCANA PENGELUARAN
        $sheet->setCellValue('A' . $currentRow, '2. Rencana Pengeluaran');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        // Headers
        $sheet->setCellValue('A' . $currentRow, 'No');
        $sheet->setCellValue('B' . $currentRow, 'Uraian');
        $sheet->setCellValue('C' . $currentRow, 'Qty');
        $sheet->setCellValue('D' . $currentRow, 'Harga');
        $sheet->setCellValue('E' . $currentRow, 'Total');
        $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($currentRow)->setRowHeight(26);
        $currentRow++;

        $no = 1;
        $expenseStartRow = $currentRow;
        foreach ($expenseItems as $item) {
            $sheet->setCellValue('A' . $currentRow, $no++);
            $sheet->setCellValue('B' . $currentRow, $item->description);
            $sheet->setCellValue('C' . $currentRow, $item->qty);
            $sheet->setCellValue('D' . $currentRow, $item->price);
            $sheet->setCellValue('E' . $currentRow, $item->total);

            // Alignment and formatting
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $currentRow)->getNumberFormat()->setFormatCode('Rp #,##0');
            $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode('Rp #,##0');
            $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($borderThin);
            $currentRow++;
        }
        $expenseEndRow = $currentRow - 1;

        // Total Expense Row
        $sheet->setCellValue('A' . $currentRow, 'Total');
        $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($expenseItems->isEmpty()) {
            $sheet->setCellValue('E' . $currentRow, 0);
        } else {
            $sheet->setCellValue('E' . $currentRow, '=SUM(E' . $expenseStartRow . ':E' . $expenseEndRow . ')');
        }

        $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode('Rp #,##0');
        $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($summaryStyle);
        $totalExpenseCellRow = $currentRow;
        $currentRow += 2;

        // SUMMARY BOX
        $sheet->setCellValue('A' . $currentRow, 'Total Pemasukan');
        $sheet->setCellValue('E' . $currentRow, '=E' . $totalIncomeCellRow);
        $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode('Rp #,##0');
        $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($summaryStyle);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Total Pengeluaran');
        $sheet->setCellValue('E' . $currentRow, '=E' . $totalExpenseCellRow);
        $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode('Rp #,##0');
        $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($summaryStyle);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Sisa Akhir');
        $sheet->setCellValue('E' . $currentRow, '=E' . ($currentRow - 2) . '-E' . ($currentRow - 1));
        $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode('Rp #,##0');
        $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray($summaryStyle);
        $currentRow += 2;

        // SIGNATURE BLOCK
        $formattedDate = $report->date->translatedFormat('d F Y');
        $sheet->setCellValue('D' . $currentRow, $report->city . ', ' . $formattedDate);
        $sheet->getStyle('D' . $currentRow)->getFont()->setItalic(true);
        $currentRow += 4;
        
        $sheet->setCellValue('D' . $currentRow, Auth::user()->name);
        $sheet->getStyle('D' . $currentRow)->getFont()->setBold(true)->setUnderline(true);
        $currentRow++;
        
        $sheet->setCellValue('D' . $currentRow, 'Bendahara Kelompok');
        $sheet->getStyle('D' . $currentRow)->getFont()->setSize(9);

        // Column widths auto
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Laporan_Kegiatan_' . str_replace(' ', '_', $report->activity_name) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

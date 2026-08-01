<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = FinanceTransaction::with('creator')->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        $weeks = [
            [
                'label' => 'Minggu 1 (20 - 26 Jul 2026)',
                'start' => '2026-07-20',
                'end' => '2026-07-26',
            ],
            [
                'label' => 'Minggu 2 (27 Jul - 2 Agt 2026)',
                'start' => '2026-07-27',
                'end' => '2026-08-02',
            ],
            [
                'label' => 'Minggu 3 (3 - 9 Agt 2026)',
                'start' => '2026-08-03',
                'end' => '2026-08-09',
            ],
            [
                'label' => 'Minggu 4 (10 - 16 Agt 2026)',
                'start' => '2026-08-10',
                'end' => '2026-08-16',
            ],
            [
                'label' => 'Minggu 5 (17 - 20 Agt 2026)',
                'start' => '2026-08-17',
                'end' => '2026-08-20',
            ],
        ];

        if ($request->filled('week')) {
            $weekIndex = $request->week;
            if (isset($weeks[$weekIndex])) {
                $request->merge([
                    'start_date' => $weeks[$weekIndex]['start'],
                    'end_date' => $weeks[$weekIndex]['end'],
                ]);
            }
        }

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $transactions = $query->paginate(15)->withQueryString();

        // Calculate Totals (ignores pagination)
        $totalIncome = FinanceTransaction::where('type', 'income')->sum('amount');
        $totalExpense = FinanceTransaction::where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Visual Data (Grouped by Category for Chart)
        $incomeByCategory = FinanceTransaction::where('type', 'income')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $expenseByCategory = FinanceTransaction::where('type', 'expense')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        // Monthly trends for line chart (driver-safe)
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            $monthlyTrends = FinanceTransaction::selectRaw("strftime('%Y-%m', date) as month, type, SUM(amount) as total")
                ->groupBy('month', 'type')
                ->orderBy('month', 'asc')
                ->get();
        } else {
            $monthlyTrends = FinanceTransaction::selectRaw("DATE_FORMAT(date, '%Y-%m') as month, type, SUM(amount) as total")
                ->groupBy('month', 'type')
                ->orderBy('month', 'asc')
                ->get();
        }

        return view('bendahara.finance.index', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'incomeByCategory',
            'expenseByCategory',
            'monthlyTrends',
            'weeks'
        ));
    }

    public function store(Request $request)
    {
        // Authorization check (Only Bendahara and Koordinator can create)
        $user = Auth::user();
        if (!$user->isBendahara() && !$user->isKoordinator()) {
            abort(403, 'Hanya Bendahara atau Koordinator yang dapat menginput transaksi keuangan.');
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $receiptPath = 'uploads/receipts/' . $filename;
        }

        FinanceTransaction::create([
            'date' => $validated['date'],
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'receipt_path' => $receiptPath,
            'created_by' => $user->id,
        ]);

        return redirect()->route('finance.index')->with('success', 'Transaksi keuangan berhasil dicatat!');
    }

    public function update(Request $request, FinanceTransaction $transaction)
    {
        $user = Auth::user();
        if (!$user->isBendahara() && !$user->isKoordinator()) {
            abort(403, 'Hanya Bendahara atau Koordinator yang dapat memperbarui transaksi keuangan.');
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            if ($transaction->receipt_path && file_exists(public_path($transaction->receipt_path))) {
                unlink(public_path($transaction->receipt_path));
            }
            $file = $request->file('receipt');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $validated['receipt_path'] = 'uploads/receipts/' . $filename;
        }

        $transaction->update($validated);

        return redirect()->route('finance.index')->with('success', 'Transaksi keuangan berhasil diperbarui!');
    }

    public function destroy(FinanceTransaction $transaction)
    {
        $user = Auth::user();
        if (!$user->isBendahara() && !$user->isKoordinator()) {
            abort(403, 'Hanya Bendahara atau Koordinator yang dapat menghapus transaksi keuangan.');
        }

        if ($transaction->receipt_path && file_exists(public_path($transaction->receipt_path))) {
            unlink(public_path($transaction->receipt_path));
        }

        $transaction->delete();

        return redirect()->route('finance.index')->with('success', 'Transaksi keuangan berhasil dihapus!');
    }

    public function export(Request $request)
    {
        $query = FinanceTransaction::with('creator')->orderBy('date', 'asc');

        // Apply same filters if active
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $transactions = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Buku Kas');

        // Page Header
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'BUKU KAS UMUM KKN KELOMPOK 4');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Desa Sirnaraja, Kec. Cihideung, Tasikmalaya');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Date Info
        $sheet->mergeCells('A3:G3');
        $dateText = 'Periode: Semua Transaksi';
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $start = $request->start_date ?? 'Awal';
            $end = $request->end_date ?? 'Akhir';
            $dateText = "Periode: $start s.d. $end";
        }
        $sheet->setCellValue('A3', $dateText);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getFont()->setSize(10);

        // Table Headers
        $headers = ['No', 'Tanggal', 'Kategori', 'Keterangan', 'Pemasukan (Rp)', 'Pengeluaran (Rp)', 'Saldo Akhir (Rp)'];
        $sheet->fromArray($headers, null, 'A5');
        
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']], // Emerald 500
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A5:G5')->applyFromArray($headerStyle);
        $sheet->getRowDimension('5')->setRowHeight(30);

        // Fill Data
        $row = 6;
        $runningBalance = 0;
        foreach ($transactions as $index => $t) {
            $dateFormatted = $t->date->format('Y-m-d');
            $income = $t->type === 'income' ? $t->amount : 0;
            $expense = $t->type === 'expense' ? $t->amount : 0;
            $runningBalance += $income - $expense;

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $dateFormatted);
            $sheet->setCellValue('C' . $row, $t->category);
            $sheet->setCellValue('D' . $row, $t->description);
            $sheet->setCellValue('E' . $row, $income ?: '-');
            $sheet->setCellValue('F' . $row, $expense ?: '-');
            $sheet->setCellValue('G' . $row, $runningBalance);

            // Row styles
            $sheet->getStyle('A' . $row . ':G' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Format numbers
            if ($income > 0) {
                $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            }
            if ($expense > 0) {
                $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            }
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row++;
        }

        // Summary Row
        $sheet->mergeCells("A$row:D$row");
        $sheet->setCellValue("A$row", 'TOTAL');
        $sheet->getStyle("A$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        $totalIn = FinanceTransaction::when($request->filled('type'), function($q) use ($request) {
            return $q->where('type', $request->type);
        })->where('type', 'income')->sum('amount');

        $totalOut = FinanceTransaction::when($request->filled('type'), function($q) use ($request) {
            return $q->where('type', $request->type);
        })->where('type', 'expense')->sum('amount');

        $sheet->setCellValue('E' . $row, $totalIn ?: '-');
        $sheet->setCellValue('F' . $row, $totalOut ?: '-');
        $sheet->setCellValue('G' . $row, $runningBalance);
        
        $sheet->getStyle("E$row:G$row")->getFont()->setBold(true);
        $sheet->getStyle("E$row")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("F$row")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("G$row")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("A$row:G$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Column Widths
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Write and Download
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Buku_Kas_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}

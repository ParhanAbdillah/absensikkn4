<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function rekap(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $selectedDate = Carbon::parse($date);

        $totalMembers = User::members()->count();

        $attendances = Attendance::with(['user', 'location'])
            ->whereDate('check_in_at', $selectedDate)
            ->latest('check_in_at')
            ->get();

        $hadirCount  = $attendances->where('status', 'hadir')->count();
        $tidakHadirCount = $totalMembers - $hadirCount;
        $persentase  = $totalMembers > 0 ? round(($hadirCount / $totalMembers) * 100) : 0;

        // Semua anggota yang belum absen hari ini
        $absentUserIds = $attendances->pluck('user_id')->toArray();
        $belumAbsen = User::members()
            ->whereNotIn('id', $absentUserIds)
            ->get();

        return view('koordinator.attendance.rekap', compact(
            'attendances',
            'selectedDate',
            'totalMembers',
            'hadirCount',
            'tidakHadirCount',
            'persentase',
            'belumAbsen'
        ));
    }

    public function print(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $selectedDate = Carbon::parse($date);

        $totalMembers = User::members()->count();

        $attendances = Attendance::with(['user', 'location'])
            ->whereDate('check_in_at', $selectedDate)
            ->latest('check_in_at')
            ->get();

        $hadirCount      = $attendances->where('status', 'hadir')->count();
        $tidakHadirCount = $totalMembers - $hadirCount;
        $persentase      = $totalMembers > 0 ? round(($hadirCount / $totalMembers) * 100) : 0;

        $absentUserIds = $attendances->pluck('user_id')->toArray();
        $belumAbsen = User::members()
            ->whereNotIn('id', $absentUserIds)
            ->get();

        return view('koordinator.attendance.print', compact(
            'attendances',
            'selectedDate',
            'totalMembers',
            'hadirCount',
            'tidakHadirCount',
            'persentase',
            'belumAbsen'
        ));
    }

    public function exportWord(Request $request)
    {
        $startDateStr = $request->input('start_date');
        $endDateStr = $request->input('end_date');
        $date = $request->input('date', \Carbon\Carbon::today()->toDateString());

        if ($startDateStr && $endDateStr) {
            $startDate = \Carbon\Carbon::parse($startDateStr);
            $endDate = \Carbon\Carbon::parse($endDateStr);
            $fileName = 'Daftar_Hadir_KKN_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.docx';
        } else {
            $startDate = \Carbon\Carbon::parse($date);
            $endDate = \Carbon\Carbon::parse($date);
            $fileName = 'Daftar_Hadir_KKN_' . $startDate->format('Y-m-d') . '.docx';
        }

        // Fetch all members (17 members including ketua & sekretaris)
        $members = \App\Models\User::members()->orderBy('name', 'asc')->get();

        // Create PHPWord instance
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Default font: Times New Roman for body, header/table will use Arial explicitly
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(11);
        $phpWord->setDefaultParagraphStyle(['lineHeight' => 1.15]);

        $schedule = \App\Models\Schedule::whereDate('activity_date', $startDate)->first();
        $pembahasan = $schedule ? $schedule->title : 'Kegiatan Harian KKN';

        // Section matching original (pgMar top=right=bottom=left=1000 twips, header=720)
        // Diperkecil marginnya agar muat lebih banyak baris
        $section = $phpWord->addSection([
            'marginTop'    => 1000,
            'marginBottom' => 1000,
            'marginLeft'   => 1000,
            'marginRight'  => 1000,
            'headerHeight' => 720,
        ]);

        // ── HEADER (KOP SURAT) ────────────────────────────────────────────────
        $header = $section->addHeader();

        // Font & paragraph styles
        $f14 = ['name' => 'Arial', 'bold' => true, 'size' => 14];
        $f10 = ['name' => 'Arial', 'size' => 10];
        $pC  = [
            'alignment'   => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
            'spaceAfter'  => 0,
            'spaceBefore' => 0,
            'lineHeight'  => 1.0,
        ];

        // Last paragraph: triple border === (thinThickThinMediumGap sz=18)
        $pCBorder = array_merge($pC, [
            'borderBottomStyle' => 'thinThickThinMediumGap',
            'borderBottomColor' => '000000',
            'borderBottomSize'  => 18,
            'borderBottomSpace' => 1,
        ]);

        // LEFT logo: floating In Front of Text, positioned at left margin
        $logoLp3iPath = public_path('logo-lp3i.png');
        if (file_exists($logoLp3iPath)) {
            $header->addImage($logoLp3iPath, [
                'height'           => 50,
                'wrappingStyle'    => 'infront',
                'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
                'posHorizontal'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_LEFT,
                'posHorizontalRel' => 'margin',
                'posVertical'      => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
                'posVerticalRel'   => 'line',
                'marginTop'        => 0,
                'marginLeft'       => 0,
            ]);
        }

        // RIGHT logo: floating In Front of Text, positioned at right margin
        $logoSirnarajaPath = public_path('logo_sirnaraja.png');
        if (file_exists($logoSirnarajaPath)) {
            $header->addImage($logoSirnarajaPath, [
                'height'           => 55,
                'wrappingStyle'    => 'infront',
                'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
                'posHorizontal'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_RIGHT,
                'posHorizontalRel' => 'margin',
                'posVertical'      => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
                'posVerticalRel'   => 'line',
                'marginTop'        => 0,
                'marginLeft'       => 0,
            ]);
        }

        // CENTER text: plain centered paragraphs filling the full width
        $header->addText('KULIAH KERJA NYATA (KKN)',           $f14, $pC);
        $header->addText('POLITEKNIK LP3I KAMPUS TASIKMALAYA', $f14, $pC);
        $header->addText("KELOMPOK 4 \xE2\x80\x93 Desa Sirnaraja", $f14, $pC);
        $header->addText(
            'Sekretariat KKN : Kp. Kuliah RT. 01 / RW. 01, Desa Kerja, Kec. Nyata, Kab. Sukses',
            $f10,
            $pC
        );
        $header->addText(
            "CP : +62 819-1831-7214 Naufal Qurrota A'yun \xE2\x80\x93 E-mail : kknlp3i0426.sirnaraja@gmail.com",
            $f10,
            $pCBorder
        );

        // ── BODY ─────────────────────────────────────────────────────────────
        $jcCenter = \PhpOffice\PhpWord\SimpleType\Jc::CENTER;

        // Title block
        $section->addText(
            'DAFTAR HADIR',
            ['name' => 'Arial', 'bold' => true, 'size' => 12],
            ['alignment' => $jcCenter, 'spaceAfter' => 0, 'spaceBefore' => 100]
        );
        $section->addText(
            'KEGIATAN KKN TEMATIK 2026',
            ['name' => 'Arial', 'bold' => true, 'size' => 12],
            ['alignment' => $jcCenter, 'spaceAfter' => 0]
        );
        $section->addText(
            'SEMESTER GANJIL TAHUN AKADEMIK 2026/2027',
            ['name' => 'Arial', 'bold' => true, 'size' => 12],
            ['alignment' => $jcCenter, 'spaceAfter' => 200]
        );

        // ── ATTENDANCE TABLE ──────────────────────────────────────────────────
        $phpWord->addTableStyle('AttTable', [
            'borderSize'  => 6,
            'borderColor' => '000000',
            'cellMargin'  => 40,
        ]);
        $table = $section->addTable('AttTable');

        // Header row
        $hF  = ['name' => 'Arial', 'bold' => true, 'size' => 10];
        $hP  = ['alignment' => $jcCenter, 'spaceAfter' => 0];
        $hBg = 'D9D9D9';

        // Prepare days array
        $days = [];
        $curr = $startDate->copy();
        while($curr->lte($endDate)) {
            $days[] = $curr->copy();
            $curr->addDay();
        }
        $numDays = count($days);

        if ($numDays === 1) {
            // ==========================================
            // KONDISI 1: RENTANG HANYA 1 TANGGAL
            // ==========================================

            $phpWord->addTableStyle('AttTable', [
                'borderSize'  => 6,
                'borderColor' => '000000',
                'cellMargin'  => 20, // Reduced cell margin
            ]);
            $table = $section->addTable('AttTable');

            // Header row
            $hF  = ['name' => 'Arial', 'bold' => true, 'size' => 12];
            $hP  = ['alignment' => $jcCenter, 'spaceAfter' => 0];
            $hBg = '92D050'; // Green background

            $table->addRow(300, ['exactHeight' => false]);
            $table->addCell(536,  ['valign' => 'center', 'bgColor' => $hBg])->addText('No',               $hF, $hP);
            $table->addCell(4000, ['valign' => 'center', 'bgColor' => $hBg])->addText('Nama Anggota',     $hF, $hP);
            $table->addCell(2000, ['valign' => 'center', 'bgColor' => $hBg])->addText('Divisi / Jabatan', $hF, $hP);
            $table->addCell(2875, ['valign' => 'center', 'bgColor' => $hBg, 'gridSpan' => 2])->addText('Tanda Tangan', $hF, $hP);

            // Data rows – Arial 10pt
            $bF = ['name' => 'Arial', 'size' => 12];
            $bP = ['spaceAfter' => 0, 'spaceBefore' => 0];

            // Fetch attendances for that day
            $attendances = \App\Models\Attendance::whereDate('check_in_at', $startDate)
                ->where('status', 'hadir')
                ->get()
                ->keyBy('user_id');

            foreach ($members as $i => $member) {
                $num = $i + 1;
                $table->addRow(300, ['exactHeight' => false]);

                $table->addCell(536,  ['valign' => 'center'])
                    ->addText($num, $bF, ['alignment' => $jcCenter] + $bP);
                $table->addCell(4000, ['valign' => 'center'])->addText($member->name,          $bF, $bP);
                $table->addCell(2000, ['valign' => 'center'])->addText($member->class ?? $member->divisi ?? '-', $bF, ['alignment' => $jcCenter] + $bP);

                $hasAttended = $attendances->has($member->id);

                if ($num % 2 !== 0) {
                    // Odd row
                    $cell1 = $table->addCell(1437, ['valign' => 'top', 'vMerge' => 'restart']);
                    $cell2 = $table->addCell(1438, ['valign' => 'top', 'vMerge' => 'restart']);

                    if ($hasAttended && $member->signature) {
                        $sigPath = storage_path('app/public/' . $member->signature);
                        file_exists($sigPath)
                            ? $cell1->addImage($sigPath, ['width' => 45, 'height' => 22])
                            : $cell1->addText($num . '.', $bF, $bP);
                    } else {
                        $cell1->addText($num . '.', $bF, $bP);
                    }

                    $nextMember = $members->get($i + 1);
                    if ($nextMember) {
                        $hasNextAttended = $attendances->has($nextMember->id);
                        if ($hasNextAttended && $nextMember->signature) {
                            $nextSigPath = storage_path('app/public/' . $nextMember->signature);
                            file_exists($nextSigPath)
                                ? $cell2->addImage($nextSigPath, ['width' => 45, 'height' => 22])
                                : $cell2->addText(($num + 1) . '.', $bF, $bP);
                        } else {
                            $cell2->addText(($num + 1) . '.', $bF, $bP);
                        }
                    } else {
                        $cell2->addText('', $bF, $bP);
                    }
                } else {
                    // Even row
                    $table->addCell(1437, ['valign' => 'top', 'vMerge' => 'continue']);
                    $table->addCell(1438, ['valign' => 'top', 'vMerge' => 'continue']);
                }
            }

            // Jika jumlah anggota ganjil, tambahkan 1 baris kosong genap di bawahnya
            // agar merge vertikal tanda tangan pada baris ganjil terakhir bisa selesai
            if ($members->count() % 2 !== 0) {
                $table->addRow(300, ['exactHeight' => false]);
                $table->addCell(536,  ['valign' => 'center'])->addText('', $bF, $bP);
                $table->addCell(4000, ['valign' => 'center'])->addText('', $bF, $bP);
                $table->addCell(2000, ['valign' => 'center'])->addText('', $bF, $bP);
                $table->addCell(1437, ['valign' => 'top', 'vMerge' => 'continue']);
                $table->addCell(1438, ['valign' => 'top', 'vMerge' => 'continue']);
            }

        } else {
            // ==========================================
            // KONDISI 2: RENTANG LEBIH DARI 1 TANGGAL
            // ==========================================

            $phpWord->addTableStyle('AttTable', [
                'borderSize'  => 6,
                'borderColor' => '000000',
                'cellMargin'  => 20, // Reduced margin
            ]);
            $table = $section->addTable('AttTable');

            // Header row
            $hF  = ['name' => 'Arial', 'bold' => true, 'size' => 12];
            $hP  = ['alignment' => $jcCenter, 'spaceAfter' => 0];
            $hBg = '92D050'; // Green background
            
            // Calculate dynamic width for date columns
            // With 1000 twips margins, available width is ~9900 twips
            // No: 400, Nama: 3600 (widened), Jurusan: 1500 (shrunk) -> 5500 total
            // Remaining: 4400 twips for dates
            $dayColWidth = floor(4400 / max($numDays, 1));

            // First Header Row (Spanning - Tanggal)
            $table->addRow(300, ['exactHeight' => false]);
            $table->addCell(400,  ['valign' => 'center', 'bgColor' => $hBg, 'vMerge' => 'restart'])->addText('No', $hF, $hP);
            $table->addCell(3600, ['valign' => 'center', 'bgColor' => $hBg, 'vMerge' => 'restart'])->addText('Nama', $hF, $hP);
            $table->addCell(1500, ['valign' => 'center', 'bgColor' => $hBg, 'vMerge' => 'restart'])->addText('Jurusan', $hF, $hP);
            $table->addCell($dayColWidth * $numDays, ['valign' => 'center', 'bgColor' => $hBg, 'gridSpan' => $numDays])->addText('Tanggal', $hF, $hP);

            // Second Header Row (Spanning - Bulan)
            $table->addRow(300, ['exactHeight' => false]);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $monthName = $startDate->locale('id')->isoFormat('MMMM');
            $table->addCell($dayColWidth * $numDays, ['valign' => 'center', 'bgColor' => $hBg, 'gridSpan' => $numDays])->addText($monthName, $hF, $hP);

            // Third Header Row (Dates)
            $table->addRow(300, ['exactHeight' => false]);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            
            foreach ($days as $day) {
                $table->addCell($dayColWidth, ['valign' => 'center', 'bgColor' => $hBg])->addText($day->format('j'), $hF, $hP);
            }

            // Data rows – Arial 9pt
            $bF = ['name' => 'Arial', 'size' => 12];
            $bP = ['spaceAfter' => 0, 'spaceBefore' => 0];

            // Fetch attendances for all days in the range
            $attendancesByDate = [];
            foreach ($days as $day) {
                $attendancesByDate[$day->format('Y-m-d')] = \App\Models\Attendance::whereDate('check_in_at', $day)
                    ->where('status', 'hadir')
                    ->pluck('user_id')
                    ->toArray();
            }

            foreach ($members as $i => $member) {
                $num = $i + 1;
                $table->addRow(300, ['exactHeight' => false]);

                $table->addCell(400,  ['valign' => 'center'])->addText($num, $bF, ['alignment' => $jcCenter] + $bP);
                $table->addCell(3600, ['valign' => 'center'])->addText($member->name, $bF, $bP);
                $table->addCell(1500, ['valign' => 'center'])->addText($member->class ?? '-', $bF, ['alignment' => $jcCenter] + $bP);

                foreach ($days as $day) {
                    $dateStr = $day->format('Y-m-d');
                    $isHadir = in_array($member->id, $attendancesByDate[$dateStr]);
                    
                    $cell = $table->addCell($dayColWidth, ['valign' => 'center']);
                    if ($isHadir && $member->signature) {
                        $sigPath = storage_path('app/public/' . $member->signature);
                        if (file_exists($sigPath)) {
                            $cell->addImage($sigPath, ['width' => 25, 'height' => 12, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                        } else {
                            $cell->addText('V', $bF, ['alignment' => $jcCenter] + $bP);
                        }
                    } else {
                        $cell->addText('', $bF, ['alignment' => $jcCenter] + $bP);
                    }
                }
            }
        }

        // Export
        $tempFile = tempnam(sys_get_temp_dir(), 'docx');
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}

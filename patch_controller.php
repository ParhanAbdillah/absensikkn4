<?php
$content = file_get_contents('app/Http/Controllers/Koordinator/AttendanceController.php');

// We will find the start of exportWord
$startPos = strpos($content, '    public function exportWord(Request $request)');
if ($startPos === false) { die("Start not found\n"); }

// The function is the last one in the class, so we can just replace everything from $startPos to the end,
// minus the last closing brace of the class.
$endPos = strrpos($content, '}');
$exportWordContent = substr($content, $startPos, $endPos - $startPos);

$newExportWord = <<< 'PHP'
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

        // Fetch all members
        $members = \App\Models\User::where('role', 'anggota')->orderBy('name', 'asc')->get();

        // Create PHPWord instance
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Default font: Times New Roman for body, header/table will use Arial explicitly
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(11);
        $phpWord->setDefaultParagraphStyle(['lineHeight' => 1.15]);

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            // Find the schedule for this day to get title and description
            $schedule = \App\Models\Schedule::whereDate('activity_date', $currentDate)->first();
            $pembahasan = $schedule ? $schedule->title : 'Kegiatan Harian KKN';

            // Fetch attendances for that day
            $attendances = \App\Models\Attendance::whereDate('check_in_at', $currentDate)
                ->where('status', 'hadir')
                ->get()
                ->keyBy('user_id');

            // Section matching original (pgMar top=right=bottom=left=1440 twips, header=720)
            $section = $phpWord->addSection([
                'marginTop'    => 1440,
                'marginBottom' => 1440,
                'marginLeft'   => 1440,
                'marginRight'  => 1440,
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
                'DAFTAR HADIR KKN',
                ['name' => 'Arial', 'bold' => true, 'size' => 12],
                ['alignment' => $jcCenter, 'spaceAfter' => 0, 'spaceBefore' => 200]
            );
            $section->addText(
                'KEHADIRAN HARIAN',
                ['name' => 'Arial', 'bold' => true, 'size' => 12],
                ['alignment' => $jcCenter, 'spaceAfter' => 200]
            );

            // Metadata table (invisible borders, Arial 12pt matching original document)
            $phpWord->addTableStyle('MetaTable', [
                'borderSize'  => 0,
                'borderColor' => 'ffffff',
                'cellMargin'  => 0,
            ]);
            $metaTable = $section->addTable('MetaTable');
            $mF = ['name' => 'Arial', 'size' => 12];
            $mP = ['spaceAfter' => 0, 'spaceBefore' => 0];

            $metaTable->addRow();
            $metaTable->addCell(2268)->addText('Kelompok',      $mF, $mP);
            $metaTable->addCell(6804)->addText(': 04',          $mF, $mP);

            $metaTable->addRow();
            $metaTable->addCell(2268)->addText('DPL',           $mF, $mP);
            $metaTable->addCell(6804)->addText(': Verra Rosyalia Widia Sofyan, S.E.,M.M.', $mF, $mP);

            $metaTable->addRow();
            $metaTable->addCell(2268)->addText('Hari / Tanggal', $mF, $mP);
            $metaTable->addCell(6804)->addText(': ' . $currentDate->isoFormat('dddd, D MMMM Y'), $mF, $mP);

            $metaTable->addRow();
            $metaTable->addCell(2268)->addText('Pembahasan',    $mF, $mP);
            $metaTable->addCell(6804)->addText(': ' . $pembahasan, $mF, $mP);

            $section->addText('', [], ['spaceAfter' => 200]);

            // ── ATTENDANCE TABLE ──────────────────────────────────────────────────
            $phpWord->addTableStyle('AttTable', [
                'borderSize'  => 6,
                'borderColor' => '000000',
                'cellMargin'  => 40,
            ]);
            $table = $section->addTable('AttTable');

            // Header row
            $hF  = ['name' => 'Arial', 'bold' => true, 'size' => 11];
            $hP  = ['alignment' => $jcCenter, 'spaceAfter' => 0];
            $hBg = 'D9D9D9';

            $table->addRow(400, ['exactHeight' => true]);
            $table->addCell(536,  ['valign' => 'center', 'bgColor' => $hBg])->addText('No',               $hF, $hP);
            $table->addCell(3802, ['valign' => 'center', 'bgColor' => $hBg])->addText('Nama Anggota',     $hF, $hP);
            $table->addCell(2137, ['valign' => 'center', 'bgColor' => $hBg])->addText('Divisi / Jabatan', $hF, $hP);
            $table->addCell(2875, ['valign' => 'center', 'bgColor' => $hBg, 'gridSpan' => 2])->addText('Tanda Tangan', $hF, $hP);

            // Data rows – Arial 11pt
            $bF = ['name' => 'Arial', 'size' => 11];
            $bP = ['spaceAfter' => 0, 'spaceBefore' => 0];

            foreach ($members as $i => $member) {
                $num = $i + 1;
                $table->addRow(400, ['exactHeight' => true]);

                $table->addCell(536,  ['valign' => 'center'])
                    ->addText($num, $bF, ['alignment' => $jcCenter] + $bP);
                $table->addCell(3802, ['valign' => 'center'])->addText($member->name,          $bF, $bP);
                $table->addCell(2137, ['valign' => 'center'])->addText($member->divisi ?? '-', $bF, $bP);

                $hasAttended = $attendances->has($member->id);

                if ($num % 2 !== 0) {
                    // Odd row
                    $cell1 = $table->addCell(1437, ['valign' => 'top', 'vMerge' => 'restart']);
                    $cell2 = $table->addCell(1438, ['valign' => 'top', 'vMerge' => 'restart']);

                    if ($hasAttended && $member->signature) {
                        $sigPath = storage_path('app/public/' . $member->signature);
                        file_exists($sigPath)
                            ? $cell1->addImage($sigPath, ['width' => 50, 'height' => 25])
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
                                ? $cell2->addImage($nextSigPath, ['width' => 50, 'height' => 25])
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

            $currentDate->addDay();
        }

        // Export
        $tempFile = tempnam(sys_get_temp_dir(), 'docx');
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
PHP;

$newContent = substr_replace($content, $newExportWord, $startPos, $endPos - $startPos);
file_put_contents('app/Http/Controllers/Koordinator/AttendanceController.php', $newContent);
echo "Successfully patched AttendanceController.php\n";

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์รายชื่อสมาชิกชุมนุม - <?= htmlspecialchars($club['club_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        @media print {
            @page { size: A4; margin: 1cm; }
            .no-print { display: none; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto bg-white shadow-lg p-8 md:p-12 min-h-[29.7cm]">
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-6 mb-8">
            <div class="flex items-center gap-4">
                <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-16 h-16">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">โรงเรียนพิชัย</h1>
                    <p class="text-slate-500 uppercase tracking-widest text-sm font-bold">รายชื่อสมาชิกชุมนุม</p>
                </div>
            </div>
            <button onclick="window.print()" class="no-print bg-slate-900 text-white px-6 py-2 rounded-xl font-bold hover:bg-slate-800 transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> พิมพ์เอกสาร
            </button>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-8">
            <div class="space-y-2">
                <div class="flex gap-2">
                    <span class="font-bold text-slate-500 min-w-[100px]">ชุมนุม:</span>
                    <span class="font-bold text-slate-900"><?= htmlspecialchars($club['club_name']) ?></span>
                </div>
                <div class="flex gap-2">
                    <span class="font-bold text-slate-500 min-w-[100px]">รหัสชุมนุม:</span>
                    <span class="text-slate-700 font-mono"><?= htmlspecialchars($club['club_id']) ?></span>
                </div>
                <div class="flex gap-2">
                    <span class="font-bold text-slate-500 min-w-[100px]">ที่ปรึกษา:</span>
                    <span class="text-slate-700"><?= htmlspecialchars($advisor_name) ?></span>
                </div>
            </div>
            <div class="space-y-2 text-right">
                <div class="flex justify-end gap-2">
                    <span class="font-bold text-slate-500">ภาคเรียน/ปีการศึกษา:</span>
                    <span class="text-slate-700"><?= htmlspecialchars($club['term']) ?> / <?= htmlspecialchars($club['year']) ?></span>
                </div>
                <div class="flex justify-end gap-2 text-sm text-slate-400">
                    <span>พิมพ์เมื่อ:</span>
                    <span><?= date('d/m/Y H:i') ?></span>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <table class="w-full border-collapse border border-slate-300">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="border border-slate-300 py-3 px-4 text-center text-sm font-bold w-16">ลำดับ</th>
                        <th class="border border-slate-300 py-3 px-4 text-center text-sm font-bold">รหัสประจำตัว</th>
                        <th class="border border-slate-300 py-3 px-4 text-left text-sm font-bold">ชื่อ-นามสกุล</th>
                        <th class="border border-slate-300 py-3 px-4 text-center text-sm font-bold w-24">ระดับชั้น</th>
                        <th class="border border-slate-300 py-3 px-4 text-center text-sm font-bold w-16">เลขที่</th>
                        <th class="border border-slate-300 py-3 px-4 text-center text-sm font-bold">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="6" class="border border-slate-300 py-8 text-center text-slate-400 italic">ไม่พบข้อมูลสมาชิก</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($students as $i => $stu): ?>
                    <tr>
                        <td class="border border-slate-300 py-2 px-4 text-center text-sm"><?= $i + 1 ?></td>
                        <td class="border border-slate-300 py-2 px-4 text-center text-sm font-mono"><?= htmlspecialchars($stu['student_id']) ?></td>
                        <td class="border border-slate-300 py-2 px-4 text-sm"><?= htmlspecialchars($stu['name']) ?></td>
                        <td class="border border-slate-300 py-2 px-4 text-center text-sm"><?= htmlspecialchars($stu['class_name']) ?></td>
                        <td class="border border-slate-300 py-2 px-4 text-center text-sm"><?= htmlspecialchars($stu['Stu_no']) ?></td>
                        <td class="border border-slate-300 py-2 px-4 text-sm"></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-12">
            <div class="flex justify-end">
                <div class="text-center min-w-[300px]">
                    <p class="mb-20 font-bold">ลงชื่อ..............................................................</p>
                    <p class="font-bold">( <?= htmlspecialchars($advisor_name) ?> )</p>
                    <p class="text-slate-500 mt-2 font-bold">ครูที่ปรึกษาชุมนุม</p>
                    <p class="text-slate-400 text-sm mt-1">วันที่..........เดือน..........................พ.ศ................</p>
                </div>
            </div>
        </div>

        <div class="mt-auto pt-12 text-center text-[10px] text-slate-300 border-t border-slate-100 no-print">
            เอกสารฉบับนี้พิมพ์โดยระบบสารสนเทศเพื่อการจัดการชุมนุม โรงเรียนพิชัย
        </div>
    </div>
</body>
</html>

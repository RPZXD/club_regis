<?php 
// Read configuration from JSON file
$regis_setting = json_decode(file_get_contents('../regis_setting.json'), true);
$levels = ['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'];
$colors = [
    'ม.1' => ['from-rose-500', 'to-red-600', 'rose'],
    'ม.2' => ['from-amber-500', 'to-orange-600', 'amber'], 
    'ม.3' => ['from-emerald-500', 'to-teal-600', 'emerald'],
    'ม.4' => ['from-blue-500', 'to-indigo-600', 'blue'],
    'ม.5' => ['from-violet-500', 'to-purple-600', 'violet'],
    'ม.6' => ['from-pink-500', 'to-rose-600', 'pink']
];
?>

<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-500 to-gray-600 flex items-center justify-center text-white shadow-lg shadow-slate-500/30">
            <i class="fas fa-cog text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-black text-gray-800 dark:text-white">ตั้งค่าระบบ</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">กำหนดวันเวลาเปิด-ปิดรับสมัครชุมนุม</p>
        </div>
    </div>
</div>

<!-- Info Alert -->
<div class="glass rounded-2xl p-4 mb-6 border-l-4 border-blue-500">
    <div class="flex items-center gap-3">
        <i class="fas fa-info-circle text-blue-500 text-xl"></i>
        <p class="text-blue-700 dark:text-blue-300 font-bold text-sm">กำหนดวันเวลาในการเปิดและปิดรับสมัครชุมนุมสำหรับแต่ละระดับชั้น</p>
    </div>
</div>

<!-- Settings Form -->
<form id="settingForm">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <?php foreach ($levels as $level): 
            $regis_start = isset($regis_setting[$level]['regis_start']) ? $regis_setting[$level]['regis_start'] : '';
            $regis_end = isset($regis_setting[$level]['regis_end']) ? $regis_setting[$level]['regis_end'] : '';
            $start_datetime = $regis_start ? date('Y-m-d\TH:i', strtotime($regis_start)) : '';
            $end_datetime = $regis_end ? date('Y-m-d\TH:i', strtotime($regis_end)) : '';
            $color = $colors[$level];
            
            // Calculate status
            $status = 'not_set';
            if ($regis_start && $regis_end) {
                $now = new DateTime();
                $start = new DateTime($regis_start);
                $end = new DateTime($regis_end);
                if ($now < $start) $status = 'pending';
                elseif ($now >= $start && $now <= $end) $status = 'open';
                else $status = 'closed';
            }
        ?>
        <div class="glass rounded-2xl overflow-hidden">
            <!-- Grade Header -->
            <div class="bg-gradient-to-r <?= $color[0] ?> <?= $color[1] ?> px-5 py-4 flex items-center justify-between text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center font-black text-lg">
                        <?= substr($level, -1) ?>
                    </div>
                    <span class="font-black text-lg"><?= $level ?></span>
                </div>
                <?php if ($status === 'open'): ?>
                    <span class="px-3 py-1 rounded-full bg-white/20 text-xs font-bold flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse"></span> เปิดรับสมัคร
                    </span>
                <?php elseif ($status === 'pending'): ?>
                    <span class="px-3 py-1 rounded-full bg-white/20 text-xs font-bold flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-yellow-300"></span> รอเปิด
                    </span>
                <?php elseif ($status === 'closed'): ?>
                    <span class="px-3 py-1 rounded-full bg-white/20 text-xs font-bold flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-red-300"></span> ปิดแล้ว
                    </span>
                <?php else: ?>
                    <span class="px-3 py-1 rounded-full bg-white/20 text-xs font-bold flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-gray-300"></span> ยังไม่ตั้งค่า
                    </span>
                <?php endif; ?>
            </div>
            
            <!-- Form Fields -->
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-600 dark:text-gray-300 mb-2">
                        <i class="fas fa-play-circle text-emerald-500 mr-1"></i> เปิดรับสมัคร
                    </label>
                    <input type="datetime-local" 
                           name="regis_start[<?= $level ?>]" 
                           value="<?= $start_datetime ?>"
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 font-bold focus:border-<?= $color[2] ?>-500 focus:outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600 dark:text-gray-300 mb-2">
                        <i class="fas fa-stop-circle text-rose-500 mr-1"></i> ปิดรับสมัคร
                    </label>
                    <input type="datetime-local" 
                           name="regis_end[<?= $level ?>]" 
                           value="<?= $end_datetime ?>"
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 font-bold focus:border-<?= $color[2] ?>-500 focus:outline-none transition-all">
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Submit Button -->
    <div class="flex gap-3">
        <button type="submit" class="flex-1 md:flex-none bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-black py-4 px-8 rounded-xl shadow-lg shadow-emerald-500/30 transition-all active:scale-95 flex items-center justify-center gap-2">
            <i class="fas fa-save"></i>
            บันทึกการตั้งค่า
        </button>
        <button type="button" id="btn-reset" class="px-6 py-4 rounded-xl bg-gray-200 dark:bg-slate-700 font-bold text-gray-600 dark:text-gray-300 transition-all active:scale-95">
            <i class="fas fa-undo"></i>
        </button>
    </div>
</form>

<!-- Status Table -->
<div class="glass rounded-2xl overflow-hidden mt-8">
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-4">
        <h3 class="text-lg font-black text-white flex items-center gap-2">
            <i class="fas fa-table"></i>
            สรุปสถานะการรับสมัคร
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 dark:bg-slate-800">
                    <th class="px-5 py-3 text-left font-black text-gray-600 dark:text-gray-300">ระดับชั้น</th>
                    <th class="px-5 py-3 text-center font-black text-gray-600 dark:text-gray-300">เปิดรับ</th>
                    <th class="px-5 py-3 text-center font-black text-gray-600 dark:text-gray-300">ปิดรับ</th>
                    <th class="px-5 py-3 text-center font-black text-gray-600 dark:text-gray-300">สถานะ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <?php foreach ($levels as $level): 
                    $regis_start = isset($regis_setting[$level]['regis_start']) ? $regis_setting[$level]['regis_start'] : '';
                    $regis_end = isset($regis_setting[$level]['regis_end']) ? $regis_setting[$level]['regis_end'] : '';
                ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br <?= $colors[$level][0] ?> <?= $colors[$level][1] ?> flex items-center justify-center text-white font-bold text-sm"><?= substr($level,-1) ?></div>
                            <span class="font-bold text-gray-800 dark:text-white"><?= $level ?></span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-center text-sm text-gray-600 dark:text-gray-400">
                        <?= $regis_start ? date('d/m/Y H:i', strtotime($regis_start)) : '-' ?>
                    </td>
                    <td class="px-5 py-4 text-center text-sm text-gray-600 dark:text-gray-400">
                        <?= $regis_end ? date('d/m/Y H:i', strtotime($regis_end)) : '-' ?>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <?php 
                        if ($regis_start && $regis_end) {
                            $now = new DateTime();
                            $start = new DateTime($regis_start);
                            $end = new DateTime($regis_end);
                            if ($now < $start) echo '<span class="px-3 py-1 rounded-full bg-amber-100 text-amber-600 text-xs font-bold">รอเปิด</span>';
                            elseif ($now >= $start && $now <= $end) echo '<span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold">เปิดรับ</span>';
                            else echo '<span class="px-3 py-1 rounded-full bg-rose-100 text-rose-600 text-xs font-bold">ปิดแล้ว</span>';
                        } else {
                            echo '<span class="px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-bold">ยังไม่ตั้งค่า</span>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('settingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const fd = new FormData(this);
    
    try {
        Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        const res = await fetch('api/save_regis_setting.php', { method: 'POST', body: fd });
        const data = await res.json();
        
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ', showConfirmButton: false, timer: 1500 }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: data.message || 'ไม่สามารถบันทึกได้' });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้' });
    }
});

document.getElementById('btn-reset').addEventListener('click', function() {
    Swal.fire({
        title: 'รีเซ็ตฟอร์ม?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, รีเซ็ต',
        cancelButtonText: 'ยกเลิก'
    }).then(r => { if (r.isConfirmed) document.getElementById('settingForm').reset(); });
});
</script>

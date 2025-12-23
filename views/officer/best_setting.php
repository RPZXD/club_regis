<?php 
// Read configuration from JSON file
$best_setting_file = '../best_regis_setting.json';
$best_setting = file_exists($best_setting_file) ? json_decode(file_get_contents($best_setting_file), true) : [];

$levels = ['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'];
$colors = [
    'ม.1' => ['from-emerald-500', 'to-teal-600', 'emerald'],
    'ม.2' => ['from-cyan-500', 'to-blue-600', 'cyan'], 
    'ม.3' => ['from-indigo-500', 'to-violet-600', 'indigo'],
    'ม.4' => ['from-purple-500', 'to-pink-600', 'purple'],
    'ม.5' => ['from-rose-500', 'to-orange-600', 'rose'],
    'ม.6' => ['from-amber-500', 'to-yellow-600', 'amber']
];
?>

<!-- Header Section -->
<div class="mb-6 animate__animated animate__fadeIn">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
            <i class="fas fa-award text-xl text-yellow-300"></i>
        </div>
        <div>
            <h1 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Best For Teen Setting</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold italic uppercase tracking-widest">Enrollment Time Configuration</p>
        </div>
    </div>
</div>

<!-- Info Banner -->
<div class="glass rounded-3xl p-6 mb-8 border border-white/20 shadow-xl bg-gradient-to-r from-emerald-500/10 to-teal-500/10 relative overflow-hidden">
    <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl"></div>
    <div class="flex flex-col md:flex-row items-center gap-6 relative z-10">
        <div class="w-16 h-16 rounded-2xl bg-white dark:bg-slate-800 shadow-lg flex items-center justify-center text-3xl animate__animated animate__pulse animate__infinite">
            🏆
        </div>
        <div class="flex-1 text-center md:text-left">
            <h2 class="text-lg font-black text-emerald-700 dark:text-emerald-400 mb-1 uppercase tracking-tighter">กำหนดช่วงเวลาเปิดรับสมัคร Best For Teen</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">เจ้าหน้าที่สามารถกำหนดวันและเวลาที่ให้นักเรียนแต่ละระดับชั้นเข้าสมัครได้อย่างอิสระ ข้อมูลจะถูกบันทึกและแสดงผลทันที</p>
        </div>
        <button onclick="loadBestSettingLogs()" class="px-6 py-3 rounded-xl bg-white dark:bg-slate-800 border border-emerald-100 dark:border-slate-700 text-emerald-600 dark:text-emerald-400 font-black text-xs uppercase shadow-sm hover:shadow-md transition-all active:scale-95 flex items-center gap-2">
            <i class="fas fa-history"></i> ประวัติการตั้งค่า
        </button>
    </div>
</div>

<!-- Settings Form -->
<form id="bestSettingForm" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($levels as $index => $level): 
            $regis_start = isset($best_setting[$level]['regis_start']) ? $best_setting[$level]['regis_start'] : '';
            $regis_end = isset($best_setting[$level]['regis_end']) ? $best_setting[$level]['regis_end'] : '';
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
        <div class="group glass rounded-3xl overflow-hidden border border-white/20 shadow-xl hover:shadow-2xl transition-all duration-500 animate__animated animate__fadeInUp" style="animation-delay: <?= $index * 0.1 ?>s">
            <!-- Grade Card Header -->
            <div class="bg-gradient-to-r <?= $color[0] ?> <?= $color[1] ?> px-6 py-5 text-white relative">
                <div class="absolute top-0 left-0 w-full h-full bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center font-black text-2xl shadow-inner">
                            <?= substr($level, -1) ?>
                        </div>
                        <div>
                            <div class="text-[10px] font-black uppercase opacity-80 tracking-widest leading-none mb-1">GRADE LEVEL</div>
                            <h3 class="text-xl font-black leading-none"><?= $level ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Inputs -->
            <div class="p-6 space-y-5 bg-white/50 dark:bg-slate-900/50">
                <div class="relative">
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-2 ml-1 tracking-widest">
                        <i class="fas fa-calendar-plus mr-1"></i> Opening Time
                    </label>
                    <input type="datetime-local" 
                           name="best_regis_start[<?= $level ?>]" 
                           id="best_regis_start_<?= $level ?>"
                           value="<?= $start_datetime ?>"
                           class="w-full px-5 py-4 rounded-2xl bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-800 font-bold text-gray-700 dark:text-gray-200 focus:border-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-500/5 transition-all shadow-sm">
                </div>
                
                <div class="relative">
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-2 ml-1 tracking-widest">
                        <i class="fas fa-calendar-check mr-1"></i> Closing Time
                    </label>
                    <input type="datetime-local" 
                           name="best_regis_end[<?= $level ?>]" 
                           id="best_regis_end_<?= $level ?>"
                           value="<?= $end_datetime ?>"
                           class="w-full px-5 py-4 rounded-2xl bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-800 font-bold text-gray-700 dark:text-gray-200 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-500/5 transition-all shadow-sm">
                </div>

                <!-- Status Indicator Inside Card -->
                <div class="pt-2 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-tighter italic">Status:</div>
                    <?php if ($status === 'open'): ?>
                        <span class="px-3 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 border border-emerald-100 dark:border-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Enrollment Active
                        </span>
                    <?php elseif ($status === 'pending'): ?>
                        <span class="px-3 py-1 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 border border-amber-100 dark:border-amber-800">
                            <i class="fas fa-clock"></i> Upcoming
                        </span>
                    <?php elseif ($status === 'closed'): ?>
                        <span class="px-3 py-1 rounded-lg bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 border border-rose-100 dark:border-rose-800">
                            <i class="fas fa-times-circle"></i> Closed
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded-lg bg-gray-50 dark:bg-slate-800 text-gray-400 text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 border border-gray-200 dark:border-slate-700">
                            <i class="fas fa-question-circle"></i> Config Required
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row gap-4 pt-10 items-center justify-center">
        <button type="submit" class="group w-full sm:w-auto bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-black py-5 px-12 rounded-3xl shadow-2xl shadow-emerald-500/30 hover:shadow-teal-500/40 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-3">
            <i class="fas fa-save text-xl group-hover:rotate-12 transition-transform"></i>
            <span class="text-lg">บันทึกการตั้งค่า Best</span>
        </button>
        <button type="button" id="btn-reset" class="px-8 py-5 rounded-3xl bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-gray-400 font-black uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-slate-700 transition-all active:scale-95 flex items-center gap-2">
            <i class="fas fa-undo-alt"></i> RESET FORM
        </button>
    </div>
</form>

<!-- Comparison Table -->
<div class="glass rounded-3xl overflow-hidden mt-16 border border-white/20 shadow-2xl animate__animated animate__fadeInUp">
    <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-8 py-6 flex items-center justify-between">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-desktop text-emerald-400"></i>
            Current Status Table
        </h3>
        <span class="px-4 py-1.5 rounded-full bg-white/10 text-white/70 text-[10px] font-black uppercase tracking-widest border border-white/10">Summary View</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 dark:bg-slate-900/50">
                    <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">ระดับชั้น</th>
                    <th class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">เริ่มรับสมัคร</th>
                    <th class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">สิ้นสุดรับสมัคร</th>
                    <th class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest w-40">สถานะ</th>
                    <th class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">เวลาคงเหลือ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                <?php foreach ($levels as $level): 
                    $regis_start = isset($best_setting[$level]['regis_start']) ? $best_setting[$level]['regis_start'] : '';
                    $regis_end = isset($best_setting[$level]['regis_end']) ? $best_setting[$level]['regis_end'] : '';
                ?>
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br <?= $colors[$level][0] ?> <?= $colors[$level][1] ?> flex items-center justify-center text-white font-black text-sm"><?= substr($level,-1) ?></div>
                            <span class="font-black text-gray-800 dark:text-white"><?= $level ?></span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center font-mono text-xs font-bold text-gray-600 dark:text-gray-400">
                        <?= $regis_start ? date('d/m/Y H:i', strtotime($regis_start)) : '-' ?>
                    </td>
                    <td class="px-8 py-5 text-center font-mono text-xs font-bold text-gray-600 dark:text-gray-400">
                        <?= $regis_end ? date('d/m/Y H:i', strtotime($regis_end)) : '-' ?>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <?php 
                        if ($regis_start && $regis_end) {
                            $now = new DateTime();
                            $start = new DateTime($regis_start);
                            $end = new DateTime($regis_end);
                            if ($now < $start) echo '<span class="px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 text-[10px] font-black uppercase border border-amber-100 dark:border-amber-800">รอเปิด</span>';
                            elseif ($now >= $start && $now <= $end) echo '<span class="px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase border border-emerald-100 dark:border-emerald-800 animate-pulse">กำลังรับสมัคร</span>';
                            else echo '<span class="px-3 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-[10px] font-black uppercase border border-rose-100 dark:border-rose-800 opacity-60">ปิดแล้ว</span>';
                        } else {
                            echo '<span class="px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-slate-800 text-gray-400 text-[10px] font-black uppercase border border-gray-200 dark:border-slate-700">ไม่ได้ระบุ</span>';
                        }
                        ?>
                    </td>
                    <td class="px-8 py-5 text-center font-black text-xs text-gray-500">
                        <?php 
                        if ($regis_start && $regis_end) {
                            $now = new DateTime();
                            $start = new DateTime($regis_start);
                            $end = new DateTime($regis_end);
                            if ($now < $start) {
                                $diff = $now->diff($start);
                                echo '<span class="text-amber-500"><i class="fas fa-hourglass-start mr-1"></i>' . $diff->days . 'd ' . $diff->h . 'h</span>';
                            } elseif ($now >= $start && $now <= $end) {
                                $diff = $now->diff($end);
                                echo '<span class="text-emerald-500"><i class="fas fa-hourglass-half mr-1"></i>' . $diff->days . 'd ' . $diff->h . 'h</span>';
                            } else {
                                echo '<span class="text-rose-400"><i class="fas fa-hourglass-end mr-1"></i>0d 0h</span>';
                            }
                        } else echo '-';
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Logs Timeline Modal or Area -->
<div id="logsModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeLogs()"></div>
    <div class="glass relative bg-white dark:bg-slate-900 w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden animate__animated animate__zoomIn">
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 flex justify-between items-center text-white">
            <h3 class="text-lg font-black uppercase tracking-tight">Configuration History</h3>
            <button onclick="closeLogs()" class="w-8 h-8 rounded-full bg-black/20 flex items-center justify-center hover:bg-black/30 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-8 max-h-[70vh] overflow-y-auto scrollbar-thin">
            <div id="bestLogsContainer" class="space-y-4">
                <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                    <div class="w-10 h-10 border-4 border-emerald-200 border-t-emerald-500 rounded-full animate-spin mb-4"></div>
                    <p class="font-black uppercase text-xs tracking-widest animate-pulse">Fetching Logs...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#bestSettingForm').on('submit', async function(e) {
        e.preventDefault();
        
        // Basic Validation
        let isValid = true;
        let errors = [];
        
        <?php foreach ($levels as $l): ?>
        {
            let s = $('#best_regis_start_<?= $l ?>').val();
            let e = $('#best_regis_end_<?= $l ?>').val();
            if (s && e && new Date(e) <= new Date(s)) {
                isValid = false;
                errors.push('ม.<?= substr($l,-1) ?>: เวลาปิดต้องมากกว่าเวลาเปิด');
            }
        }
        <?php endforeach; ?>
        
        if (!isValid) {
            return Swal.fire({ icon: 'error', title: 'ข้อมูลไม่ถูกต้อง', html: errors.join('<br>'), confirmButtonText: 'ตกลง' });
        }
        
        Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        $.ajax({
            url: 'api/save_best_regis_setting.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ', showConfirmButton: false, timer: 1500 }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: res.message });
                }
            },
            error: () => Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้' })
        });
    });

    $('#btn-reset').on('click', () => {
        Swal.fire({
            title: 'ต้องการรีเซ็ตฟอร์ม?',
            text: 'ข้อมูลที่พิมพ์ค้างไว้จะหายไป',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then(r => { if(r.isConfirmed) document.getElementById('bestSettingForm').reset(); });
    });
});

function closeLogs() {
    $('#logsModal').addClass('hidden');
}

function loadBestSettingLogs() {
    $('#logsModal').removeClass('hidden');
    $('#bestLogsContainer').html('<div class="flex flex-col items-center justify-center py-20 text-gray-400"><div class="w-10 h-10 border-4 border-emerald-200 border-t-emerald-500 rounded-full animate-spin mb-4"></div><p class="font-black uppercase text-xs tracking-widest animate-pulse">Fetching Logs...</p></div>');
    
    $.ajax({
        url: 'api/get_best_setting_logs.php',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.success && res.logs && res.logs.length > 0) {
                let html = '<div class="space-y-6">';
                res.logs.forEach(log => {
                    let d = new Date(log.timestamp);
                    html += `
                        <div class="relative pl-8 border-l-2 border-emerald-100 dark:border-slate-800 pb-1">
                            <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-emerald-500 border-4 border-white dark:border-slate-900"></div>
                            <div class="mb-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-black text-gray-800 dark:text-white text-sm">${log.user} <span class="text-xs font-normal text-gray-400 ml-2">updated settings</span></div>
                                    <div class="text-[10px] font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded">${d.toLocaleString('th-TH')}</div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 bg-gray-50 dark:bg-slate-800/80 p-3 rounded-2xl border border-gray-100 dark:border-slate-800">
                    `;
                    
                    Object.keys(log.data).forEach(level => {
                        html += `
                            <div class="text-[10px]">
                                <span class="font-black text-gray-400 uppercase">${level}:</span> 
                                <span class="text-gray-600 dark:text-gray-300 ml-1">${log.data[level].regis_start ? new Date(log.data[level].regis_start).toLocaleDateString('th-TH') : 'Not set'}</span>
                            </div>
                        `;
                    });

                    html += `</div></div></div>`;
                });
                html += '</div>';
                $('#bestLogsContainer').html(html);
            } else {
                $('#bestLogsContainer').html('<div class="text-center py-20 text-gray-400 font-black uppercase text-xs">No logs found</div>');
            }
        },
        error: () => $('#bestLogsContainer').html('<div class="text-center py-20 text-rose-500 font-black uppercase text-xs">Error loading logs</div>')
    });
}
</script>

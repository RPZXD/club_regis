<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
            <i class="fas fa-users text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-black text-gray-800 dark:text-white">สมัครชุมนุม</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">เลือกชุมนุมสำหรับระดับชั้น <?= htmlspecialchars($stu_grade) ?></p>
        </div>
    </div>
</div>

<?php if (!$regisOpen): ?>
<!-- Registration Closed Alert -->
<div class="glass rounded-2xl p-5 mb-6 border-l-4 border-amber-500 bg-amber-50 dark:bg-amber-900/20">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center text-white flex-shrink-0">
            <i class="fas fa-clock text-xl"></i>
        </div>
        <div>
            <h3 class="font-black text-amber-700 dark:text-amber-300 text-lg">ระบบสมัครชุมนุมยังไม่เปิด</h3>
            <?php if ($regisStart && $regisEnd): ?>
                <p class="text-amber-600 dark:text-amber-400 text-sm mt-1">
                    เปิดรับสมัคร: <b><?= date('d/m/Y H:i', $regisStart) ?></b> ถึง <b><?= date('d/m/Y H:i', $regisEnd) ?></b>
                </p>
            <?php else: ?>
                <p class="text-amber-600 dark:text-amber-400 text-sm mt-1">ยังไม่ได้ตั้งค่าเวลาเปิด-ปิดรับสมัครสำหรับระดับชั้นนี้</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Search Box -->
<div class="glass rounded-2xl p-4 mb-6">
    <div class="relative">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
        <input type="text" id="club-search" placeholder="ค้นหาชื่อชุมนุม..." 
               class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white dark:bg-slate-800 border-2 border-emerald-100 dark:border-emerald-900/50 text-gray-700 dark:text-gray-200 focus:outline-none focus:border-emerald-400 transition-all font-bold placeholder:text-gray-400 placeholder:font-normal">
    </div>
</div>

<!-- Mobile Cards View -->
<div id="club-cards" class="space-y-4 md:hidden">
    <?php if (empty($clubs)): ?>
        <div class="text-center py-16 glass rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
            <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-folder-open text-gray-300 dark:text-gray-500 text-2xl"></i>
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-bold">ไม่พบชุมนุมสำหรับระดับชั้นนี้</p>
        </div>
    <?php else: ?>
        <?php foreach ($clubs as $club): 
            $max = (int)$club['max_members'];
            $current = isset($club['current_members_count']) ? (int)$club['current_members_count'] : 0;
            $percent = $max > 0 ? round(($current / $max) * 100) : 0;
            $isFull = $percent >= 100;
        ?>
        <div class="club-card bg-white dark:bg-slate-800 rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/20 overflow-hidden border border-gray-100 dark:border-gray-700" data-name="<?= htmlspecialchars(strtolower($club['club_name'])) ?>">
            <div class="p-4 pb-3 overflow-hidden">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br <?= $isFull ? 'from-rose-500 to-red-600' : 'from-emerald-500 to-teal-600' ?> flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas <?= $isFull ? 'fa-lock' : 'fa-users' ?> text-lg"></i>
                    </div>
                    <div class="flex-1 w-0">
                        <h3 class="font-black text-gray-800 dark:text-white text-base leading-tight whitespace-nowrap overflow-hidden text-ellipsis"><?= htmlspecialchars($club['club_name']) ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 whitespace-nowrap overflow-hidden text-ellipsis">
                            <i class="fas fa-user-tie text-xs mr-1"></i><?= htmlspecialchars($club['advisor_teacher_name']) ?>
                        </p>
                    </div>
                    <?php if ($isFull): ?>
                        <span class="flex-shrink-0 px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black ml-1">เต็ม</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Grade Badges -->
            <div class="px-4 pb-3 flex flex-wrap gap-1.5">
                <?php foreach (explode(',', $club['grade_levels']) as $g): ?>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300 text-xs font-black"><?= trim($g) ?></span>
                <?php endforeach; ?>
            </div>
            
            <!-- Description -->
            <div class="px-4 pb-3">
                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2"><?= htmlspecialchars($club['description']) ?></p>
            </div>
            
            <!-- Progress Bar -->
            <div class="mx-4 mb-4 p-3 rounded-xl bg-gray-50 dark:bg-slate-900/50">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-black text-gray-500 dark:text-gray-400 uppercase">สมาชิก</span>
                    <span class="text-sm font-black <?= $isFull ? 'text-rose-500' : 'text-emerald-600' ?>"><?= $current ?> / <?= $max ?> คน</span>
                </div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all <?= $isFull ? 'bg-gradient-to-r from-rose-500 to-red-500' : 'bg-gradient-to-r from-emerald-500 to-teal-500' ?>" style="width: <?= $percent ?>%"></div>
                </div>
            </div>
            
            <!-- Apply Button -->
            <div class="px-4 py-3 bg-gray-50 dark:bg-slate-900/30 border-t border-gray-100 dark:border-gray-700">
                <button class="apply-btn w-full py-3 rounded-xl font-black text-white transition-all active:scale-[0.98] flex items-center justify-center gap-2 <?= ($isFull || !$regisOpen) ? 'bg-gray-400 cursor-not-allowed' : 'bg-gradient-to-r from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30' ?>"
                        data-id="<?= $club['club_id'] ?>"
                        <?= ($isFull || !$regisOpen) ? 'disabled' : '' ?>>
                    <i class="fas <?= $isFull ? 'fa-ban' : 'fa-check-circle' ?>"></i>
                    <?= $isFull ? 'เต็มแล้ว' : 'สมัครชุมนุมนี้' ?>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block glass rounded-2xl overflow-hidden">
    <table id="club-table" class="w-full">
        <thead>
            <tr class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
                <th class="py-4 px-4 text-left font-black">ชื่อชุมนุม</th>
                <th class="py-4 px-4 text-left font-black">ครูที่ปรึกษา</th>
                <th class="py-4 px-4 text-center font-black">ระดับชั้น</th>
                <th class="py-4 px-4 text-center font-black">สมาชิก</th>
                <th class="py-4 px-4 text-center font-black w-32">สมัคร</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <?php foreach ($clubs as $club): 
                $max = (int)$club['max_members'];
                $current = isset($club['current_members_count']) ? (int)$club['current_members_count'] : 0;
                $percent = $max > 0 ? round(($current / $max) * 100) : 0;
                $isFull = $percent >= 100;
            ?>
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="py-4 px-4">
                    <div class="font-black text-gray-800 dark:text-white"><?= htmlspecialchars($club['club_name']) ?></div>
                    <div class="text-xs text-gray-400">ID: <?= htmlspecialchars($club['club_id']) ?></div>
                </td>
                <td class="py-4 px-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($club['advisor_teacher_name']) ?></td>
                <td class="py-4 px-4 text-center">
                    <?php foreach (explode(',', $club['grade_levels']) as $g): ?>
                        <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 text-emerald-600 text-[10px] font-bold mr-1"><?= trim($g) ?></span>
                    <?php endforeach; ?>
                </td>
                <td class="py-4 px-4 text-center">
                    <span class="font-bold <?= $isFull ? 'text-rose-500' : 'text-emerald-600' ?>"><?= $current ?>/<?= $max ?></span>
                </td>
                <td class="py-4 px-4 text-center">
                    <button class="apply-btn px-4 py-2 rounded-xl font-bold text-white transition-all <?= ($isFull || !$regisOpen) ? 'bg-gray-400 cursor-not-allowed' : 'bg-emerald-500 hover:bg-emerald-600 shadow' ?>"
                            data-id="<?= $club['club_id'] ?>"
                            <?= ($isFull || !$regisOpen) ? 'disabled' : '' ?>>
                        <?= $isFull ? 'เต็ม' : 'สมัคร' ?>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Note -->
<div class="mt-6 glass rounded-2xl p-4 border-l-4 border-blue-500">
    <p class="text-blue-700 dark:text-blue-300 font-bold flex items-center gap-2 text-sm">
        <i class="fas fa-info-circle text-blue-500"></i>
        หมายเหตุ: สมัครได้เพียง 1 ชุมนุมต่อปี หากมีข้อสงสัย ติดต่อครูที่ปรึกษาชุมนุมหรือฝ่ายกิจกรรมพัฒนาผู้เรียน
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality for mobile cards
    document.getElementById('club-search').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.club-card').forEach(card => {
            const name = card.dataset.name;
            card.style.display = name.includes(query) ? '' : 'none';
        });
    });

    // Apply button click handler
    document.querySelectorAll('.apply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.disabled) return;
            const clubId = this.dataset.id;
            
            Swal.fire({
                title: 'ยืนยันการสมัคร',
                text: 'คุณต้องการสมัครเข้าชุมนุมนี้ใช่หรือไม่?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: '<i class="fas fa-check mr-2"></i>สมัครเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../controllers/RegisClubController.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: new URLSearchParams({ club_id: clubId })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สมัครสำเร็จ!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: data.message });
                        }
                    })
                    .catch(() => {
                        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้' });
                    });
                }
            });
        });
    });
});
</script>

<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center text-white shadow-lg shadow-rose-500/30">
            <i class="fas fa-heart text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-black text-gray-800 dark:text-white">ชุมนุมของฉัน</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">ดูชุมนุมที่คุณสมัครไว้</p>
        </div>
    </div>
</div>

<?php if (empty($myClubs)): ?>
<!-- Empty State -->
<div class="text-center py-20 glass rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
    <div class="w-24 h-24 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-heart-broken text-gray-300 dark:text-gray-500 text-4xl"></i>
    </div>
    <h2 class="text-xl font-black text-gray-600 dark:text-gray-300 mb-2">ยังไม่ได้สมัครชุมนุม</h2>
    <p class="text-gray-400 mb-6">คุณยังไม่ได้สมัครชุมนุมใด ๆ ในปีนี้</p>
    <a href="club_regis.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
        <i class="fas fa-users"></i>
        ไปสมัครชุมนุมเลย
    </a>
</div>
<?php else: ?>
<!-- Club Cards -->
<div class="space-y-6">
    <?php foreach ($myClubs as $club): ?>
    <div class="glass rounded-2xl overflow-hidden shadow-xl">
        <!-- Header -->
        <div class="bg-gradient-to-r from-rose-500 to-pink-600 p-5 text-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-black truncate"><?= htmlspecialchars($club['club_name']) ?></h2>
                    <p class="text-rose-100 text-sm">รหัส: <?= htmlspecialchars($club['club_id']) ?></p>
                </div>
                <span class="px-3 py-1 rounded-full bg-white/20 text-[11px] font-bold uppercase">สมาชิก</span>
            </div>
        </div>
        
        <!-- Body -->
        <div class="p-5 space-y-4">
            <!-- Advisor -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-gray-400 uppercase">ครูที่ปรึกษา</div>
                    <div class="font-bold text-gray-800 dark:text-white"><?= htmlspecialchars($club['advisor_name'] ?? $club['advisor_teacher']) ?></div>
                </div>
            </div>
            
            <!-- Grade Levels -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-gray-400 uppercase">ระดับชั้นที่เปิดรับ</div>
                    <div class="flex gap-1 mt-1">
                        <?php foreach (explode(',', $club['grade_levels']) as $g): ?>
                            <span class="px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold"><?= trim($g) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 flex-shrink-0">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-gray-400 uppercase">รายละเอียด</div>
                    <p class="text-gray-700 dark:text-gray-300 text-sm"><?= htmlspecialchars($club['description']) ?></p>
                </div>
            </div>
            
            <!-- Year/Term -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center text-violet-600 dark:text-violet-400">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-gray-400 uppercase">ปีการศึกษา</div>
                    <div class="font-bold text-gray-800 dark:text-white"><?= htmlspecialchars($club['year']) ?> ภาคเรียน <?= htmlspecialchars($club['term']) ?></div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="px-5 py-3 bg-gray-50 dark:bg-slate-900/30 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <span class="text-xs text-gray-400">
                <i class="far fa-clock mr-1"></i>สมัครเมื่อ <?= htmlspecialchars($club['created_at']) ?>
            </span>
            <span class="px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold">
                <i class="fas fa-check-circle mr-1"></i>ลงทะเบียนแล้ว
            </span>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

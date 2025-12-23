<!-- Welcome Card -->
<div class="glass rounded-2xl shadow-xl p-6 mb-6">
    <div class="flex flex-col md:flex-row items-center gap-6">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-4xl font-black shadow-lg">
            <?php echo mb_substr($user['Stu_name'] ?? 'S', 0, 1, 'UTF-8'); ?>
        </div>
        <div class="text-center md:text-left flex-1">
            <h1 class="text-2xl font-black text-gray-800 dark:text-white">
                สวัสดี! <?php echo htmlspecialchars($user['Stu_name'] . ' ' . $user['Stu_sur']); ?> 👋
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">ยินดีต้อนรับเข้าสู่ระบบลงทะเบียนชุมนุม</p>
            <div class="flex flex-wrap gap-3 mt-3 justify-center md:justify-start">
                <span class="px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm font-bold">
                    📅 ปีการศึกษา <?php echo htmlspecialchars($pee); ?>
                </span>
                <span class="px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-sm font-bold">
                    📚 ภาคเรียน <?php echo htmlspecialchars($term); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Steps Card -->
<div class="glass rounded-2xl shadow-xl p-6 mb-6">
    <h2 class="text-xl font-black text-gray-800 dark:text-white mb-6 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-route text-white"></i>
        </div>
        ขั้นตอนการสมัครเข้าร่วมชุมนุม
    </h2>
    
    <div class="grid gap-4">
        <div class="flex items-start gap-4 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold shrink-0">1</div>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">🔍 ดูรายชื่อชุมนุม</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">เลือกดูว่ามีชุมนุมอะไรบ้างที่เปิดรับสมัครในปีนี้</p>
            </div>
        </div>
        
        <div class="flex items-start gap-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800">
            <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold shrink-0">2</div>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">✅ เลือกชุมนุมที่สนใจ</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">แล้วกดปุ่ม <span class="px-2 py-0.5 rounded bg-blue-500 text-white text-xs">สมัคร</span> ข้างชุมนุมนั้น</p>
            </div>
        </div>
        
        <div class="flex items-start gap-4 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800">
            <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center text-white font-bold shrink-0">3</div>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">📝 ยืนยันการสมัคร</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">ระบบจะถามยืนยัน ให้กด "สมัคร" อีกครั้งเพื่อยืนยัน</p>
            </div>
        </div>
        
        <div class="flex items-start gap-4 p-4 rounded-xl bg-violet-50 dark:bg-violet-900/20 border border-violet-100 dark:border-violet-800">
            <div class="w-10 h-10 rounded-full bg-violet-500 flex items-center justify-center text-white font-bold shrink-0">4</div>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">📄 รอครูที่ปรึกษาตรวจสอบ</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">เมื่อสมัครแล้ว รอครูที่ปรึกษาชุมนุมอนุมัติ</p>
            </div>
        </div>
        
        <div class="flex items-start gap-4 p-4 rounded-xl bg-pink-50 dark:bg-pink-900/20 border border-pink-100 dark:border-pink-800">
            <div class="w-10 h-10 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold shrink-0">5</div>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">🎉 เสร็จสิ้น!</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">เมื่อได้รับการอนุมัติ สามารถเข้าร่วมกิจกรรมของชุมนุมได้เลย</p>
            </div>
        </div>
    </div>
    
    <div class="mt-6 p-4 rounded-xl bg-blue-100 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800">
        <p class="text-blue-700 dark:text-blue-300 font-bold flex items-center gap-2">
            <i class="fas fa-lightbulb text-amber-500"></i>
            หมายเหตุ: สมัครได้เพียง 1 ชุมนุมต่อปี หากมีข้อสงสัย ติดต่อครูที่ปรึกษาชุมนุมหรือฝ่ายกิจกรรมพัฒนาผู้เรียน
        </p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <a href="club_regis.php" class="card-hover glass rounded-2xl p-6 flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-users text-white text-2xl"></i>
        </div>
        <div>
            <h3 class="font-bold text-gray-800 dark:text-white">สมัครชุมนุม</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">เลือกชุมนุมที่ต้องการสมัคร</p>
        </div>
        <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
    </a>
    
    <a href="my_club.php" class="card-hover glass rounded-2xl p-6 flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-heart text-white text-2xl"></i>
        </div>
        <div>
            <h3 class="font-bold text-gray-800 dark:text-white">ชุมนุมของฉัน</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">ดูชุมนุมที่สมัครไว้</p>
        </div>
        <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
    </a>
</div>

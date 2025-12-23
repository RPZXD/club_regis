<!-- Welcome Card -->
<div class="glass rounded-2xl shadow-xl p-6 mb-6">
    <div class="flex flex-col md:flex-row items-center gap-6">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-4xl font-black shadow-lg">
            <?php echo mb_substr($_SESSION['user']['Teach_name'] ?? 'T', 0, 1, 'UTF-8'); ?>
        </div>
        <div class="text-center md:text-left flex-1">
            <h1 class="text-2xl font-black text-gray-800 dark:text-white">
                สวัสดีครับ/ค่ะ! <?php echo htmlspecialchars($_SESSION['user']['Teach_name']); ?> 👋
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">ยินดีต้อนรับเข้าสู่ระบบลงทะเบียนชุมนุม (ครู)</p>
            <span class="inline-block mt-2 px-3 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-sm font-bold">
                👨‍🏫 ครูที่ปรึกษาชุมนุม
            </span>
        </div>
    </div>
</div>

<!-- Guide Card -->
<div class="glass rounded-2xl shadow-xl p-6 mb-6">
    <h2 class="text-xl font-black text-gray-800 dark:text-white mb-6 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-book-open text-white"></i>
        </div>
        วิธีใช้งานหน้ารายการชุมนุมสำหรับครู
    </h2>
    
    <div class="grid gap-4">
        <div class="flex items-start gap-4 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
            <span class="text-2xl">🔎</span>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">ดูรายการชุมนุม</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">ตารางจะแสดงชุมนุมทั้งหมดที่เปิดในปีการศึกษานี้ พร้อมรายละเอียดครบถ้วน</p>
            </div>
        </div>
        
        <div class="flex items-start gap-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800">
            <span class="text-2xl">➕</span>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">สร้างชุมนุมใหม่</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">กดปุ่ม <span class="px-2 py-0.5 rounded bg-blue-500 text-white text-xs">+ สร้างชุมนุม</span> เพื่อเพิ่มชุมนุมใหม่ กรอกข้อมูลให้ครบถ้วนแล้วกดบันทึก</p>
            </div>
        </div>
        
        <div class="flex items-start gap-4 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800">
            <span class="text-2xl">✏️</span>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">แก้ไขชุมนุม</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">กดปุ่ม <span class="px-2 py-0.5 rounded bg-yellow-400 text-white text-xs">แก้ไข</span> ในแถวของชุมนุมที่ต้องการ แล้วปรับข้อมูลตามต้องการ</p>
            </div>
        </div>
        
        <div class="flex items-start gap-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800">
            <span class="text-2xl">🗑️</span>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">ลบชุมนุม</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">กดปุ่ม <span class="px-2 py-0.5 rounded bg-red-500 text-white text-xs">ลบ</span> หากไม่มีสมาชิกในชุมนุมจะสามารถลบได้</p>
            </div>
        </div>
        
        <div class="flex items-start gap-4 p-4 rounded-xl bg-violet-50 dark:bg-violet-900/20 border border-violet-100 dark:border-violet-800">
            <span class="text-2xl">🎯</span>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">กรองระดับชั้น</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">ใช้เมนู <span class="px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-xs">ระดับชั้น</span> เพื่อดูเฉพาะชุมนุมที่เปิดรับระดับชั้นที่ต้องการ</p>
            </div>
        </div>
    </div>
    
    <div class="mt-6 p-4 rounded-xl bg-blue-100 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800">
        <p class="text-blue-700 dark:text-blue-300 font-bold flex items-center gap-2">
            <i class="fas fa-lightbulb text-amber-500"></i>
            คุณครูสามารถแก้ไข/ลบได้เฉพาะชุมนุมที่ตนเองเป็นที่ปรึกษาเท่านั้น
        </p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="club_list.php" class="card-hover glass rounded-2xl p-6 text-center">
        <div class="w-14 h-14 mx-auto rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg mb-4">
            <i class="fas fa-list-check text-white text-2xl"></i>
        </div>
        <h3 class="font-bold text-gray-800 dark:text-white">รายชื่อชุมนุม</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">ดูและจัดการชุมนุม</p>
    </a>
    
    <a href="club_members.php" class="card-hover glass rounded-2xl p-6 text-center">
        <div class="w-14 h-14 mx-auto rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg mb-4">
            <i class="fas fa-user-graduate text-white text-2xl"></i>
        </div>
        <h3 class="font-bold text-gray-800 dark:text-white">จัดการนักเรียน</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">อนุมัติ/จัดการสมาชิก</p>
    </a>
    
    <a href="print_club.php" class="card-hover glass rounded-2xl p-6 text-center">
        <div class="w-14 h-14 mx-auto rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg mb-4">
            <i class="fas fa-print text-white text-2xl"></i>
        </div>
        <h3 class="font-bold text-gray-800 dark:text-white">พิมพ์รายชื่อ</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">พิมพ์รายชื่อสมาชิก</p>
    </a>
</div>

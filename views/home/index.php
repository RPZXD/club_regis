<!-- Hero Section -->
<div class="glass rounded-3xl shadow-xl p-8 md:p-12 mb-8 text-center">
    <div class="mb-8">
        <img src="<?php echo 'dist/img/'.$global['logoLink']; ?>" 
             alt="<?php echo $global['nameschool']; ?> Logo" 
             class="max-h-32 mx-auto rounded-2xl shadow-lg">
    </div>
    
    <div class="text-6xl mb-6">🎓✨</div>
    
    <h1 class="text-4xl md:text-5xl font-black gradient-text mb-4">
        <?php echo $global['pageTitle']; ?>
    </h1>
    
    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto leading-relaxed">
        ยินดีต้อนรับสู่ระบบ<?php echo $global['pageTitle']; ?><br>
        <span class="text-gray-500 dark:text-gray-400"><?php echo $global['nameschool']; ?></span>
    </p>
    
    <p class="text-lg text-gray-500 dark:text-gray-400 mb-8">
        สมัครเข้าร่วมกิจกรรมที่คุณสนใจได้ง่ายๆ เพียงไม่กี่ขั้นตอน<br>
        <span class="text-3xl">🤝🏫🎉</span>
    </p>
    
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="login.php" 
           class="btn-primary inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-white font-bold text-lg shadow-xl">
            <i class="fas fa-sign-in-alt"></i>
            เข้าสู่ระบบ
        </a>
        <a href="club_list.php" 
           class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 font-bold text-lg shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all">
            <i class="fas fa-list"></i>
            ดูรายชื่อชุมนุม
        </a>
    </div>
</div>

<!-- Quick Links Section -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="card-hover glass rounded-2xl p-6 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-list-check text-white text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">รายชื่อชุมนุม</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-4">ดูรายชื่อชุมนุมทั้งหมดที่เปิดรับสมัคร</p>
        <a href="club_list.php" class="text-primary-500 font-bold hover:underline">ดูเพิ่มเติม →</a>
    </div>
    
    <div class="card-hover glass rounded-2xl p-6 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-trophy text-white text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Best For Teen 2025</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-4">สมัครร่วมกิจกรรม Best For Teen 2025</p>
        <a href="best_list.php" class="text-primary-500 font-bold hover:underline">ดูเพิ่มเติม →</a>
    </div>
    
    <div class="card-hover glass rounded-2xl p-6 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-sign-in-alt text-white text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">เข้าสู่ระบบ</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-4">เข้าสู่ระบบเพื่อสมัครชุมนุม</p>
        <a href="login.php" class="text-primary-500 font-bold hover:underline">เข้าสู่ระบบ →</a>
    </div>
</div>

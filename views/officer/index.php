<!-- Welcome Card -->
<div class="glass rounded-2xl shadow-xl p-6 mb-6 animate__animated animate__fadeInDown">
    <div class="flex flex-col md:flex-row items-center gap-6">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center text-white text-4xl font-black shadow-lg shadow-rose-500/30">
            <?php echo mb_substr($_SESSION['user']['Teach_name'] ?? 'O', 0, 1, 'UTF-8'); ?>
        </div>
        <div class="text-center md:text-left flex-1">
            <h1 class="text-2xl font-black text-gray-800 dark:text-white leading-tight">
                สวัสดีครับ/ค่ะ! <span class="text-rose-600"><?php echo htmlspecialchars($_SESSION['user']['Teach_name']); ?></span> 👋
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium italic">ยินดีต้อนรับเข้าสู่ระบบลงทะเบียนชุมนุม (เจ้าหน้าที่)</p>
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-3">
                <span class="px-3 py-1 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-[10px] font-black uppercase tracking-widest border border-rose-200 dark:border-rose-800/50">
                    👔 ADMINISTRATOR PORTAL
                </span>
                <span class="px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-200 dark:border-blue-800/50">
                    PHICHAI SCHOOL
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Live Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="glass rounded-2xl p-6 border border-white/40 shadow-xl animate__animated animate__zoomIn" style="animation-delay: 0.1s">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 shadow-sm border border-blue-200 dark:border-blue-800">
                <i class="fas fa-list-ul text-xl"></i>
            </div>
            <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest leading-none">TOTAL CLUBS</span>
        </div>
        <div class="text-3xl font-black text-gray-800 dark:text-white" id="stat-total-clubs">0</div>
        <div class="text-xs text-gray-400 font-bold mt-1">ชุมนุมทั้งหมดในปีการศึกษานี้</div>
    </div>
    
    <div class="glass rounded-2xl p-6 border border-white/40 shadow-xl animate__animated animate__zoomIn" style="animation-delay: 0.2s">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-200 dark:border-emerald-800">
                <i class="fas fa-user-check text-xl"></i>
            </div>
            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest leading-none">TOTAL REGISTRARS</span>
        </div>
        <div class="text-3xl font-black text-gray-800 dark:text-white" id="stat-total-students">0</div>
        <div class="text-xs text-gray-400 font-bold mt-1">นักเรียนที่ลงทะเบียนสำเร็จ</div>
    </div>
    
    <div class="glass rounded-2xl p-6 border border-white/40 shadow-xl animate__animated animate__zoomIn" style="animation-delay: 0.3s">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 shadow-sm border border-amber-200 dark:border-amber-800">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest leading-none">OVERALL FILL RATE</span>
        </div>
        <div class="text-3xl font-black text-gray-800 dark:text-white" id="stat-fill-rate">0%</div>
        <div class="text-xs text-gray-400 font-bold mt-1">สัดส่วนการลงทะเบียนทั้งหมด</div>
    </div>
</div>

<!-- Quick Link Grid -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <a href="club_list.php" class="group glass rounded-2xl p-4 flex flex-col items-center justify-center border border-white/40 shadow-md hover:shadow-xl hover:scale-105 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white mb-3 shadow-lg group-hover:rotate-12 transition-transform">
            <i class="fas fa-tasks text-xl"></i>
        </div>
        <span class="font-black text-gray-700 dark:text-gray-200 text-xs text-center">จัดการชุมนุม</span>
    </a>
    
    <a href="best_report.php" class="group glass rounded-2xl p-4 flex flex-col items-center justify-center border border-white/40 shadow-md hover:shadow-xl hover:scale-105 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white mb-3 shadow-lg group-hover:rotate-12 transition-transform">
            <i class="fas fa-award text-xl"></i>
        </div>
        <span class="font-black text-gray-700 dark:text-gray-200 text-xs text-center">Best For Teen</span>
    </a>
    
    <a href="club_report.php" class="group glass rounded-2xl p-4 flex flex-col items-center justify-center border border-white/40 shadow-md hover:shadow-xl hover:scale-105 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white mb-3 shadow-lg group-hover:rotate-12 transition-transform">
            <i class="fas fa-file-chart-pie text-xl"></i>
        </div>
        <span class="font-black text-gray-700 dark:text-gray-200 text-xs text-center">รายงานชุมนุม</span>
    </a>
    
    <a href="setting.php" class="group glass rounded-2xl p-4 flex flex-col items-center justify-center border border-white/40 shadow-md hover:shadow-xl hover:scale-105 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center text-white mb-3 shadow-lg group-hover:rotate-12 transition-transform">
            <i class="fas fa-sliders-h text-xl"></i>
        </div>
        <span class="font-black text-gray-700 dark:text-gray-200 text-xs text-center">ตั้งค่าระบบ</span>
    </a>
</div>

<!-- Guide Card -->
<div class="glass rounded-3xl p-6 mb-6 shadow-xl border border-white/10 overflow-hidden relative">
    <div class="absolute -right-10 -top-10 w-40 h-40 bg-rose-500/10 rounded-full blur-3xl"></div>
    <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
    
    <h2 class="text-xl font-black text-gray-800 dark:text-white mb-6 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-terminal text-white text-xs"></i>
        </div>
        GUIDE & ACTIONS
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 rounded-2xl bg-white/50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700 flex gap-4 transition-all hover:translate-x-1">
            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex-shrink-0 flex items-center justify-center text-blue-500 text-sm">
                <i class="fas fa-search"></i>
            </div>
            <div>
                <h3 class="font-black text-gray-800 dark:text-white text-sm mb-1 uppercase tracking-tight">ตรวจสอบข้อมูล</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium leading-relaxed">ค้นหาและตรวจสอบสถานะการลงทะเบียนของนักเรียนรายบุคคลหรือรายห้องเรียน</p>
            </div>
        </div>
        
        <div class="p-4 rounded-2xl bg-white/50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700 flex gap-4 transition-all hover:translate-x-1">
            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex-shrink-0 flex items-center justify-center text-emerald-500 text-sm">
                <i class="fas fa-print"></i>
            </div>
            <div>
                <h3 class="font-black text-gray-800 dark:text-white text-sm mb-1 uppercase tracking-tight">พิมพ์รายงาน</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium leading-relaxed">ส่งออกข้อมูลรายชื่อนักเรียนแยกตามชุมนุมหรือระดับชั้น เพื่อใช้ในการจัดทำสถิติ</p>
            </div>
        </div>
        
        <div class="p-4 rounded-2xl bg-white/50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700 flex gap-4 transition-all hover:translate-x-1">
            <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex-shrink-0 flex items-center justify-center text-amber-500 text-sm">
                <i class="fas fa-lock"></i>
            </div>
            <div>
                <h3 class="font-black text-gray-800 dark:text-white text-sm mb-1 uppercase tracking-tight">จำกัดสิทธิ์</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium leading-relaxed">คุณสมารถดูข้อมูลได้ทุกชุมนุม แต่อำนาจการแก้ไขจะเป็นของครูที่ปรึกษาโดยตรง</p>
            </div>
        </div>
        
        <div class="p-4 rounded-2xl bg-white/50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700 flex gap-4 transition-all hover:translate-x-1">
            <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-900/30 flex-shrink-0 flex items-center justify-center text-rose-500 text-sm">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h3 class="font-black text-gray-800 dark:text-white text-sm mb-1 uppercase tracking-tight">ประวัติสถิติ</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium leading-relaxed">ย้อนดูข้อมูลการเปิดชุมนุมและสถิติในปีก่อน ๆ (เร็วๆ นี้)</p>
            </div>
        </div>
    </div>
</div>

<script>
async function animateNumber(id, endValue, suffix = '') {
    const obj = document.getElementById(id);
    if (!obj) return;
    let startValue = 0;
    let duration = 1500;
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const current = Math.floor(progress * (endValue - startValue) + startValue);
        obj.innerHTML = current.toLocaleString() + suffix;
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            obj.innerHTML = endValue.toLocaleString() + suffix;
        }
    };
    window.requestAnimationFrame(step);
}

async function loadStats() {
    try {
        const res = await fetch('api/fetch_club_report.php');
        const data = await res.json();
        const studentRes = await fetch('api/fetch_student_count_by_level.php');
        const studentData = await studentRes.json();
        
        if (Array.isArray(data)) {
            const totalClubs = data.length;
            const totalStudents = data.reduce((acc, curr) => acc + (curr.total_count || 0), 0);
            const maxStudents = Object.values(studentData).reduce((a,b)=>a+b, 0);
            const fillRate = maxStudents > 0 ? Math.round((totalStudents / maxStudents) * 100) : 0;
            
            animateNumber('stat-total-clubs', totalClubs);
            animateNumber('stat-total-students', totalStudents);
            animateNumber('stat-fill-rate', fillRate, '%');
        }
    } catch (e) {
        console.error('Stats loading error:', e);
    }
}

document.addEventListener('DOMContentLoaded', loadStats);
</script>

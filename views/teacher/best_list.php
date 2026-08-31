<!-- Header Section -->
<div class="mb-6 animate__animated animate__fadeIn">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 flex items-center justify-center shadow-lg shadow-orange-500/30 text-white shrink-0">
                <i class="fas fa-trophy text-2xl"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl md:text-2xl font-black text-gray-800 dark:text-white">Best For Teen (ห้องประจำชั้น)</h1>
                    <span id="year-badge" class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/50">
                        ปีการศึกษา <?= $current_year ?> (ปัจจุบัน)
                    </span>
                </div>
                <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 font-medium mt-0.5">ตรวจสอบและติดตามการสมัครกิจกรรม Best For Teen ของนักเรียน</p>
            </div>
        </div>

        <!-- Academic Year Dropdown & Refresh -->
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 bg-white/80 dark:bg-slate-800 rounded-2xl px-3.5 py-2.5 border border-gray-200/80 dark:border-slate-700 shadow-sm backdrop-blur-md">
                <i class="fas fa-calendar-alt text-amber-500 text-sm"></i>
                <label for="year-select" class="text-xs font-black text-gray-700 dark:text-gray-200 whitespace-nowrap">ปีการศึกษา:</label>
                <div class="relative flex items-center">
                    <select id="year-select" class="appearance-none bg-transparent font-black text-sm text-gray-800 dark:text-white focus:outline-none cursor-pointer pr-6">
                        <?php foreach ($available_years as $y): ?>
                        <option value="<?= $y ?>" <?= $y == $current_year ? 'selected' : '' ?> class="bg-white text-gray-800 dark:bg-slate-900 dark:text-white">
                            <?= $y ?> <?= $y == $current_year ? '(ปัจจุบัน)' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute right-0 text-[10px] text-gray-400 pointer-events-none"></i>
                </div>
            </div>

            <button id="btn-refresh" class="p-3 rounded-2xl bg-white/80 dark:bg-slate-800 text-amber-600 dark:text-amber-400 shadow-sm border border-gray-200/80 dark:border-slate-700 hover:shadow-md transition-all active:scale-95 backdrop-blur-md" title="รีเฟรชข้อมูล">
                <i class="fas fa-sync-alt" id="refresh-icon"></i>
            </button>
        </div>
    </div>
</div>

<!-- History Notice (Shown when viewing previous academic year) -->
<div id="history-notice" class="hidden mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 flex items-center justify-between gap-3 text-amber-800 dark:text-amber-300 animate__animated animate__fadeIn">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
            <i class="fas fa-history text-base"></i>
        </div>
        <div>
            <p class="font-black text-xs md:text-sm">กำลังดูข้อมูลย้อนหลัง ปีการศึกษา <span id="history-year-text" class="underline font-black"></span></p>
            <p class="text-[11px] opacity-80">ข้อมูลนี้เป็นข้อมูลสำหรับดูสถิติย้อนหลัง</p>
        </div>
    </div>
    <button onclick="resetToCurrentYear()" class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-black transition-all shadow-sm shrink-0">
        กลับสู่ปีปัจจุบัน
    </button>
</div>

<!-- Advisory Classroom Banner & Print Action Box -->
<div class="glass rounded-3xl p-5 md:p-6 mb-6 shadow-sm border border-white/40 dark:border-white/10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-amber-500/30 shrink-0">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">ห้องประจำชั้นที่ปรึกษา:</span>
                    <span class="text-lg md:text-xl font-black text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-950/60 px-3 py-0.5 rounded-xl border border-amber-200/60 dark:border-amber-800/50">
                        ม.<?= $assigned_level ?>/<?= $assigned_room ?>
                    </span>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mt-1">
                    <i class="fas fa-user-tie text-amber-500 mr-1"></i>ครูที่ปรึกษา: <b><?= htmlspecialchars($teacherInfo['Teach_name'] ?? $_SESSION['username']) ?></b>
                </p>
            </div>
        </div>

        <!-- Print Action Button -->
        <div class="flex items-center gap-2 shrink-0">
            <a id="btn-print-room" href="print_best_room.php?level=<?= $assigned_level ?>&room=<?= $assigned_room ?>&year=<?= $current_year ?>" target="_blank" 
               class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-sm font-black shadow-lg shadow-amber-500/30 transition-all active:scale-95">
                <i class="fas fa-print text-base"></i>
                <span>พิมพ์รายชื่อห้องเรียน</span>
            </a>
        </div>
    </div>
</div>

<!-- Summary Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
    <!-- Total Students Card -->
    <div class="glass rounded-3xl p-5 card-hover relative overflow-hidden border border-white/40 dark:border-white/10 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                <i class="fas fa-user-graduate text-lg"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/40 px-2.5 py-1 rounded-full">ทั้งหมด</span>
        </div>
        <div id="stat-total" class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white mb-1">0</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">นักเรียนในห้อง (คน)</p>
    </div>

    <!-- Registered Card -->
    <div class="glass rounded-3xl p-5 card-hover relative overflow-hidden border border-white/40 dark:border-white/10 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 px-2.5 py-1 rounded-full">สมัครแล้ว</span>
        </div>
        <div id="stat-registered" class="text-2xl md:text-3xl font-black text-emerald-600 dark:text-emerald-400 mb-1">0</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">เลือกกิจกรรมแล้ว (คน)</p>
    </div>

    <!-- Unregistered Card -->
    <div class="glass rounded-3xl p-5 card-hover relative overflow-hidden border border-white/40 dark:border-white/10 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white shadow-lg shadow-rose-500/20">
                <i class="fas fa-clock text-lg"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-wider text-rose-600 dark:text-rose-400 bg-rose-100 dark:bg-rose-900/40 px-2.5 py-1 rounded-full">ยังไม่สมัคร</span>
        </div>
        <div id="stat-unregistered" class="text-2xl md:text-3xl font-black text-rose-500 dark:text-rose-400 mb-1">0</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">ยังไม่เลือกกิจกรรม (คน)</p>
    </div>

    <!-- Fill Rate Card -->
    <div class="glass rounded-3xl p-5 card-hover relative overflow-hidden border border-white/40 dark:border-white/10 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/20">
                <i class="fas fa-chart-pie text-lg"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/40 px-2.5 py-1 rounded-full">อัตราการสมัคร</span>
        </div>
        <div id="stat-fill-rate" class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white mb-1">0%</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">ความคืบหน้าของห้อง</p>
    </div>
</div>

<!-- Search & Status Filter Tabs -->
<div class="glass rounded-3xl p-4 md:p-5 mb-6 border border-white/40 dark:border-white/10 shadow-sm space-y-3">
    <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
        <!-- Search Input -->
        <div class="relative flex-1 group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-amber-500 transition-colors text-sm"></i>
            <input type="text" id="search-box" placeholder="ค้นหานักเรียน (รหัส, ชื่อ-สกุล, ชื่อกิจกรรม)..." 
                   class="w-full pl-11 pr-10 py-3 rounded-2xl border-2 border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-gray-700 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition-all font-bold text-xs md:text-sm placeholder:text-gray-400 placeholder:font-normal shadow-sm">
            <button type="button" id="btn-clear-search" onclick="clearSearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>

        <!-- Filter Status Buttons -->
        <div class="flex items-center gap-1 bg-black/5 dark:bg-white/5 p-1 rounded-2xl shrink-0">
            <button type="button" class="status-tab px-3.5 py-2 rounded-xl text-xs font-black bg-white dark:bg-slate-800 text-gray-800 dark:text-white shadow-sm transition-all" data-status="all">
                ทั้งหมด (<span id="count-tab-all">0</span>)
            </button>
            <button type="button" class="status-tab px-3.5 py-2 rounded-xl text-xs font-black text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-all" data-status="registered">
                สมัครแล้ว (<span id="count-tab-reg">0</span>)
            </button>
            <button type="button" class="status-tab px-3.5 py-2 rounded-xl text-xs font-black text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-all" data-status="unregistered">
                ยังไม่สมัคร (<span id="count-tab-unreg">0</span>)
            </button>
        </div>
    </div>
</div>

<!-- Mobile Cards View -->
<div id="student-cards" class="md:hidden space-y-3 mb-8">
    <div class="text-center py-16 glass rounded-3xl border border-white/40 dark:border-white/10">
        <div class="w-10 h-10 border-4 border-amber-200 border-t-amber-500 rounded-full animate-spin mx-auto mb-3"></div>
        <p class="text-gray-500 dark:text-gray-400 font-bold text-sm">กำลังโหลดรายชื่อนักเรียน...</p>
    </div>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block glass rounded-3xl shadow-xl overflow-hidden mb-8 border border-white/40 dark:border-white/10">
    <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 p-4 px-6 flex items-center justify-between text-white">
        <h3 class="text-base font-black flex items-center gap-2">
            <i class="fas fa-list-ol"></i>
            <span>รายชื่อนักเรียนชั้น <span id="table-room-title">ม.1/1</span></span>
        </h3>
        <span id="table-year-label" class="text-xs font-bold opacity-90">ปีการศึกษา <?= $current_year ?></span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/60 dark:bg-slate-800/60 border-b border-gray-100 dark:border-slate-700 text-xs font-black text-gray-600 dark:text-gray-300">
                    <th class="py-4 px-4 text-center w-16">เลขที่</th>
                    <th class="py-4 px-4 text-center w-28">รหัสประจำตัว</th>
                    <th class="py-4 px-6">ชื่อ - นามสกุล</th>
                    <th class="py-4 px-4 text-center w-24">ชั้น/ห้อง</th>
                    <th class="py-4 px-6">กิจกรรม Best For Teen ที่สมัคร</th>
                    <th class="py-4 px-4 text-center w-32">วันที่สมัคร</th>
                    <th class="py-4 px-4 text-center w-28">สถานะ</th>
                </tr>
            </thead>
            <tbody id="student-table-body" class="divide-y divide-gray-100 dark:divide-slate-800/60 text-sm">
                <tr>
                    <td colspan="7" class="py-16 text-center text-gray-400 font-bold">
                        <div class="w-10 h-10 border-4 border-amber-200 border-t-amber-500 rounded-full animate-spin mx-auto mb-3"></div>
                        กำลังโหลดข้อมูลนักเรียน...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
const defaultCurrentYear = <?= $current_year ?>;
const teacherAssignedLevel = <?= json_encode($assigned_level) ?>;
const teacherAssignedRoom = <?= json_encode((string)$assigned_room) ?>;
let selectedYear = defaultCurrentYear;
let selectedLevel = teacherAssignedLevel;
let selectedRoom = teacherAssignedRoom;
let currentStudentsList = [];
let selectedStatusFilter = 'all';

function resetToCurrentYear() {
    document.getElementById('year-select').value = defaultCurrentYear;
    selectedYear = defaultCurrentYear;
    loadStudents();
}

function updatePrintLink() {
    const btn = document.getElementById('btn-print-room');
    if (btn) {
        btn.href = `print_best_room.php?level=${selectedLevel}&room=${selectedRoom}&year=${selectedYear}`;
    }
}

async function loadStudents() {
    const isCurrent = (selectedYear === defaultCurrentYear);
    const badge = document.getElementById('year-badge');
    const historyNotice = document.getElementById('history-notice');
    const tableYearLabel = document.getElementById('table-year-label');
    const tableRoomTitle = document.getElementById('table-room-title');

    if (badge) {
        badge.innerHTML = `ปีการศึกษา ${selectedYear} ${isCurrent ? '(ปัจจุบัน)' : ''}`;
        badge.className = isCurrent 
            ? 'px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/50'
            : 'px-2.5 py-0.5 rounded-full text-xs font-black bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/50';
    }

    if (historyNotice) {
        if (!isCurrent) {
            document.getElementById('history-year-text').innerText = selectedYear;
            historyNotice.classList.remove('hidden');
        } else {
            historyNotice.classList.add('hidden');
        }
    }

    if (tableYearLabel) tableYearLabel.innerText = `ปีการศึกษา ${selectedYear}`;
    if (tableRoomTitle) tableRoomTitle.innerText = `ม.${selectedLevel}/${selectedRoom}`;
    updatePrintLink();

    try {
        const res = await fetch(`../controllers/BestActivityController.php?action=room_students&level=${selectedLevel}&room=${selectedRoom}&year=${selectedYear}`);
        const data = await res.json();
        
        if (data.success) {
            currentStudentsList = data.data || [];
            updateSummary(data.summary || {});
            applyFilters();
        } else {
            currentStudentsList = [];
            updateSummary({});
            applyFilters();
        }
    } catch (e) {
        console.error('Error fetching room students:', e);
        currentStudentsList = [];
        updateSummary({});
        applyFilters();
    }
}

function updateSummary(summary) {
    const total = summary.total_students || 0;
    const reg = summary.registered_count || 0;
    const unreg = summary.unregistered_count || 0;
    const rate = summary.fill_rate !== undefined ? summary.fill_rate : (total > 0 ? Math.round((reg / total) * 100) : 0);

    animateCounter(document.getElementById('stat-total'), total);
    animateCounter(document.getElementById('stat-registered'), reg);
    animateCounter(document.getElementById('stat-unregistered'), unreg);
    animateCounter(document.getElementById('stat-fill-rate'), rate, '%');

    document.getElementById('count-tab-all').innerText = total;
    document.getElementById('count-tab-reg').innerText = reg;
    document.getElementById('count-tab-unreg').innerText = unreg;
}

function animateCounter(element, target, suffix = '') {
    if (!element) return;
    let current = 0;
    const step = Math.max(1, Math.floor(target / 15));
    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            element.textContent = target.toLocaleString() + suffix;
            clearInterval(timer);
        } else {
            element.textContent = current.toLocaleString() + suffix;
        }
    }, 25);
}

function clearSearch() {
    const box = document.getElementById('search-box');
    box.value = '';
    document.getElementById('btn-clear-search').classList.add('hidden');
    applyFilters();
}

function applyFilters() {
    const q = document.getElementById('search-box').value.trim().toLowerCase();
    const clearBtn = document.getElementById('btn-clear-search');
    if (clearBtn) {
        if (q) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }

    let filtered = currentStudentsList;

    // Filter by Status Tab
    if (selectedStatusFilter === 'registered') {
        filtered = filtered.filter(s => s.is_registered);
    } else if (selectedStatusFilter === 'unregistered') {
        filtered = filtered.filter(s => !s.is_registered);
    }

    // Filter by search query
    if (q) {
        filtered = filtered.filter(s => 
            (s.fullname || '').toLowerCase().includes(q) ||
            (s.student_id || '').toString().includes(q) ||
            (s.number || '').toString().includes(q) ||
            (s.activity_name || '').toLowerCase().includes(q)
        );
    }

    renderTable(filtered);
    renderMobileCards(filtered);
}

function renderTable(list) {
    const tbody = document.getElementById('student-table-body');
    if (!tbody) return;

    if (list.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="py-16 text-center text-gray-400 font-bold">
                    <i class="fas fa-folder-open text-gray-300 dark:text-gray-600 text-3xl mb-3 block"></i>
                    ไม่พบข้อมูลนักเรียนตามเงื่อนไขที่เลือก
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = list.map((s, idx) => {
        const isReg = s.is_registered;
        const regDate = s.registered_at ? new Date(s.registered_at).toLocaleDateString('th-TH', { day: 'numeric', month: 'short', year: '2-digit' }) : '-';

        return `
            <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                <td class="py-4 px-4 text-center font-bold text-gray-500 dark:text-gray-400">${s.number || '-'}</td>
                <td class="py-4 px-4 text-center font-black text-gray-700 dark:text-gray-300">${s.student_id}</td>
                <td class="py-4 px-6 font-black text-gray-800 dark:text-white">${s.fullname}</td>
                <td class="py-4 px-4 text-center font-bold text-gray-600 dark:text-gray-300">${s.class_name}</td>
                <td class="py-4 px-6 font-black ${isReg ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 italic'}">
                    ${isReg ? `<i class="fas fa-star text-amber-500 mr-1.5"></i>${s.activity_name}` : 'ยังไม่เลือกกิจกรรม'}
                </td>
                <td class="py-4 px-4 text-center text-xs font-bold text-gray-400 dark:text-gray-500">${regDate}</td>
                <td class="py-4 px-4 text-center">
                    <span class="px-2.5 py-1 rounded-full text-xs font-black inline-flex items-center gap-1 ${isReg ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400'}">
                        <i class="fas ${isReg ? 'fa-check' : 'fa-times'} text-[10px]"></i>
                        ${isReg ? 'สมัครแล้ว' : 'ยังไม่สมัคร'}
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

function renderMobileCards(list) {
    const container = document.getElementById('student-cards');
    if (!container) return;

    if (list.length === 0) {
        container.innerHTML = `
            <div class="text-center py-16 glass rounded-3xl border border-white/40 dark:border-white/10">
                <i class="fas fa-folder-open text-gray-300 dark:text-gray-600 text-3xl mb-3 block"></i>
                <p class="text-gray-500 dark:text-gray-400 font-bold mb-1">ไม่พบข้อมูลนักเรียน</p>
                <p class="text-xs text-gray-400">ลองเปลี่ยนเงื่อนไขหรือตัวกรองค้นหา</p>
            </div>`;
        return;
    }

    container.innerHTML = list.map(s => {
        const isReg = s.is_registered;
        return `
            <div class="glass rounded-3xl p-4 shadow-sm border border-white/40 dark:border-white/10">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br ${isReg ? 'from-amber-500 to-orange-600' : 'from-gray-400 to-slate-500'} flex items-center justify-center text-white font-black text-sm shadow-md shrink-0">
                            ${s.number || '#'}
                        </div>
                        <div>
                            <h4 class="font-black text-gray-800 dark:text-white text-sm">${s.fullname}</h4>
                            <p class="text-xs text-gray-400">รหัส: ${s.student_id} | ${s.class_name}</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black shrink-0 ${isReg ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400'}">
                        ${isReg ? 'สมัครแล้ว' : 'ยังไม่สมัคร'}
                    </span>
                </div>
                <div class="bg-black/5 dark:bg-white/5 rounded-2xl p-2.5 mt-2 flex items-center justify-between text-xs">
                    <span class="text-gray-500 dark:text-gray-400 font-bold">กิจกรรม:</span>
                    <span class="font-black ${isReg ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 italic'} truncate max-w-[200px]">
                        ${isReg ? s.activity_name : 'ยังไม่เลือกกิจกรรม'}
                    </span>
                </div>
            </div>
        `;
    }).join('');
}

document.addEventListener('DOMContentLoaded', async function() {
    // Load students for advisory classroom
    await loadStudents();

    // Year change listener
    document.getElementById('year-select').addEventListener('change', function() {
        selectedYear = parseInt(this.value);
        loadStudents();
    });

    // Refresh button
    document.getElementById('btn-refresh').addEventListener('click', () => {
        const icon = document.getElementById('refresh-icon');
        icon.classList.add('fa-spin');
        loadStudents().then(() => {
            setTimeout(() => icon.classList.remove('fa-spin'), 600);
        });
    });

    // Search input
    document.getElementById('search-box').addEventListener('input', applyFilters);

    // Status Filter Tabs
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            selectedStatusFilter = this.dataset.status;
            document.querySelectorAll('.status-tab').forEach(t => {
                t.className = 'status-tab px-3.5 py-2 rounded-xl text-xs font-black text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-all';
            });
            this.className = 'status-tab px-3.5 py-2 rounded-xl text-xs font-black bg-white dark:bg-slate-800 text-gray-800 dark:text-white shadow-sm transition-all';
            applyFilters();
        });
    });
});
</script>

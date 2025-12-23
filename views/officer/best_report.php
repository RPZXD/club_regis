<?php
// Dummy data initial logic could go here if needed
?>

<!-- Header Section with Stats Bar -->
<div class="mb-8 animate__animated animate__fadeIn">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                <i class="fas fa-trophy text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-800 dark:text-white tracking-tight">รายงาน Best For Teen</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">สรุปข้อมูลกิจกรรมและสถิติการลงทะเบียน</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="refreshData()" class="p-3 rounded-xl bg-white dark:bg-slate-800 text-amber-600 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all active:scale-95" title="รีเฟรชข้อมูล">
                <i class="fas fa-sync-alt" id="refresh-icon"></i>
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="stats-container">
        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm animate__animated animate__zoomIn" style="animation-delay: 0.1s">
            <div class="text-[10px] uppercase font-black text-amber-500 mb-1 tracking-wider">กิจกรรมทั้งหมด</div>
            <div class="text-2xl font-black text-gray-800 dark:text-white" id="stat-activities">-</div>
        </div>
        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm animate__animated animate__zoomIn" style="animation-delay: 0.2s">
            <div class="text-[10px] uppercase font-black text-emerald-500 mb-1 tracking-wider">ผู้สมัครทั้งหมด</div>
            <div class="text-2xl font-black text-gray-800 dark:text-white" id="stat-members">-</div>
        </div>
        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm animate__animated animate__zoomIn" style="animation-delay: 0.3s">
            <div class="text-[10px] uppercase font-black text-blue-500 mb-1 tracking-wider">อัตราการเติมเต็ม</div>
            <div class="text-2xl font-black text-gray-800 dark:text-white" id="stat-fill">-%</div>
        </div>
        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm animate__animated animate__zoomIn" style="animation-delay: 0.4s">
            <div class="text-[10px] uppercase font-black text-rose-500 mb-1 tracking-wider">กิจกรรมที่เต็ม</div>
            <div class="text-2xl font-black text-gray-800 dark:text-white" id="stat-full">-</div>
        </div>
    </div>
</div>

<!-- Tab Navigation -->
<div class="flex flex-wrap gap-2 mb-6 overflow-x-auto pb-2 scrollbar-hide">
    <button class="report-tab active px-5 py-3 rounded-xl font-bold transition-all flex items-center gap-2 whitespace-nowrap" data-tab="overview">
        <i class="fas fa-chart-pie"></i>
        <span>ภาพรวม</span>
    </button>
    <button class="report-tab px-5 py-3 rounded-xl font-bold transition-all flex items-center gap-2 whitespace-nowrap" data-tab="level">
        <i class="fas fa-layer-group"></i>
        <span>ตามระดับชั้น</span>
    </button>
    <button class="report-tab px-5 py-3 rounded-xl font-bold transition-all flex items-center gap-2 whitespace-nowrap" data-tab="room">
        <i class="fas fa-door-open"></i>
        <span>รายห้อง</span>
    </button>
    <button class="report-tab px-5 py-3 rounded-xl font-bold transition-all flex items-center gap-2 whitespace-nowrap" data-tab="activity">
        <i class="fas fa-star"></i>
        <span>ตามกิจกรรม</span>
    </button>
</div>

<!-- Tab Contents -->
<div id="tab-overview" class="tab-content animate__animated animate__fadeIn">
    <!-- Desktop Table View -->
    <div class="hidden md:block glass rounded-3xl overflow-hidden border border-white/40 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-amber-500 to-orange-600 text-white">
                        <th class="py-4 px-6 font-black text-sm uppercase text-center w-16">#</th>
                        <th class="py-4 px-6 font-black text-sm uppercase">กิจกรรม</th>
                        <th class="py-4 px-6 font-black text-sm uppercase text-center w-24">รับได้</th>
                        <th class="py-4 px-6 font-black text-sm uppercase text-center w-24">ลงแล้ว</th>
                        <th class="py-4 px-6 font-black text-sm uppercase text-center w-20">%</th>
                        <th class="py-4 px-6 font-black text-sm uppercase">ชั้นที่รับ</th>
                    </tr>
                </thead>
                <tbody id="best-overview-body" class="divide-y divide-gray-100 dark:divide-slate-800 bg-white/50 dark:bg-slate-900/50">
                    <!-- Rows injected here -->
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Mobile Card View -->
    <div id="best-overview-mobile" class="md:hidden space-y-4">
        <!-- Cards injected here -->
    </div>
</div>

<div id="tab-level" class="tab-content hidden animate__animated animate__fadeIn">
    <div class="glass rounded-3xl p-6 border border-white/40 shadow-xl">
        <div class="max-w-md mx-auto mb-8">
            <label class="block text-sm font-black text-gray-700 dark:text-gray-300 mb-2 ml-1">เลือกระดับชั้น</label>
            <select id="level-select" class="w-full px-5 py-4 rounded-2xl bg-white dark:bg-slate-800 border-2 border-amber-50 dark:border-slate-800 text-lg font-black text-gray-800 dark:text-white focus:outline-none focus:border-amber-400 transition-all shadow-sm">
                <?php for($i=1; $i<=6; $i++): ?>
                <option value="<?= $i ?>">มัธยมศึกษาปีที่ <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800/50 text-gray-500 dark:text-gray-400 text-[10px] uppercase font-black tracking-widest">
                        <th class="py-4 px-6 text-center w-16">#</th>
                        <th class="py-4 px-6">ชื่อกิจกรรม</th>
                        <th class="py-4 px-6 text-center w-40">จำนวนผู้สมัคร</th>
                    </tr>
                </thead>
                <tbody id="best-level-body" class="divide-y divide-gray-50 dark:divide-slate-800">
                    <!-- Rows injected here -->
                </tbody>
            </table>
        </div>

        <!-- Mobile View -->
        <div id="best-level-mobile" class="md:hidden space-y-3">
            <!-- Cards injected here -->
        </div>
    </div>
</div>

<div id="tab-room" class="tab-content hidden animate__animated animate__fadeIn">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Room Selector -->
        <div class="lg:col-span-4 lg:sticky lg:top-24 h-fit">
            <div class="glass rounded-3xl p-6 border border-white/40 shadow-xl space-y-4">
                <h3 class="font-black text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-filter text-amber-500"></i> ตัวกรองรายห้อง
                </h3>
                <div class="grid grid-cols-2 lg:grid-cols-1 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1 ml-1">ชั้น</label>
                        <select id="room-level-select" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-800 font-bold focus:border-amber-400 focus:outline-none transition-all"></select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1 ml-1">ห้อง</label>
                        <select id="room-room-select" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-800 font-bold focus:border-amber-400 focus:outline-none transition-all"></select>
                    </div>
                </div>
                <button id="room-search-btn" class="w-full py-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-black shadow-lg shadow-amber-500/20 active:scale-95 transition-all">
                    แสดงรายชื่อ
                </button>
            </div>
            
            <div class="glass rounded-3xl p-6 border border-white/40 shadow-xl mt-6">
                <h4 class="text-sm font-black text-gray-800 dark:text-white mb-4">สรุปจำนวนรายห้อง</h4>
                <div class="max-h-60 overflow-y-auto pr-2 scrollbar-thin">
                    <table class="w-full text-xs">
                        <tbody id="best-room-summary-body" class="divide-y divide-gray-50"></tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Student List -->
        <div class="lg:col-span-8">
            <div class="glass rounded-3xl overflow-hidden border border-white/40 shadow-xl">
                <div class="bg-white/50 dark:bg-slate-800/50 p-5 border-b border-gray-100 dark:border-slate-800">
                    <h3 class="font-black text-gray-800 dark:text-white"><i class="fas fa-users-viewfinder text-emerald-500 mr-2"></i>รายชื่อนักเรียน</h3>
                </div>
                
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-900/50 text-[10px] text-gray-400 font-black uppercase">
                                <th class="py-4 px-6 text-center w-12">#</th>
                                <th class="py-4 px-6 w-24">รหัส</th>
                                <th class="py-4 px-6">ชื่อ-สกุล</th>
                                <th class="py-4 px-6 w-20">ห้อง</th>
                                <th class="py-4 px-6">กิจกรรมที่เลือก</th>
                            </tr>
                        </thead>
                        <tbody id="best-room-students-body" class="divide-y divide-gray-50 dark:divide-slate-800">
                            <!-- Rows -->
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div id="best-room-students-mobile" class="md:hidden p-4 space-y-3">
                    <div class="py-10 text-center text-gray-400 font-bold uppercase underline decoration-dotted">กรุณาเลือกชั้นและห้องเรียน</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="tab-activity" class="tab-content hidden animate__animated animate__fadeIn">
    <div class="glass rounded-3xl p-6 border border-white/40 shadow-xl mb-6">
        <div class="max-w-2xl mx-auto flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-sm font-black text-gray-700 dark:text-gray-300 mb-2 ml-1">เลือกกิจกรรม</label>
                <select id="activity-select" class="w-full px-5 py-4 rounded-2xl bg-white dark:bg-slate-800 border-2 border- amber-50 dark:border-slate-800 text-lg font-black text-gray-800 dark:text-white focus:outline-none focus:border-amber-400 transition-all shadow-sm">
                    <option value="">-- เลือกกิจกรรม --</option>
                </select>
            </div>
            <button id="activity-load-btn" class="w-full md:w-auto px-8 py-4 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-black shadow-lg shadow-emerald-500/20 active:scale-95 transition-all h-[60px]">
                <i class="fas fa-download mr-2"></i> โหลดรายชื่อ
            </button>
        </div>
    </div>

    <div class="glass rounded-3xl overflow-hidden border border-white/40 shadow-xl">
        <div id="activity-report-header" class="hidden p-6 bg-gradient-to-r from-emerald-500 to-teal-600 text-white flex justify-between items-center">
            <div>
                <h3 id="selected-activity-name" class="text-xl font-black uppercase leading-none mb-1">...</h3>
                <p id="selected-activity-stats" class="text-xs font-bold opacity-80 uppercase tracking-widest">สมาชิก 0 คน</p>
            </div>
            <button onclick="window.print()" class="w-10 h-10 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition-all">
                <i class="fas fa-print"></i>
            </button>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-900/50 text-[10px] text-gray-400 font-black uppercase">
                        <th class="py-4 px-6 text-center w-12">#</th>
                        <th class="py-4 px-6 w-24">รหัส</th>
                        <th class="py-4 px-6">ชื่อ-สกุล</th>
                        <th class="py-4 px-6 w-20">ห้อง</th>
                        <th class="py-4 px-6">เวลาลงทะเบียน</th>
                    </tr>
                </thead>
                <tbody id="best-activity-members-body" class="divide-y divide-gray-50 dark:divide-slate-800">
                    <tr><td colspan="5" class="py-20 text-center text-gray-400 font-bold uppercase">กรุณาเลือกกิจกรรมและกดปุ่มโหลดรายชื่อ</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div id="best-activity-members-mobile" class="md:hidden p-4 space-y-3">
             <div class="py-10 text-center text-gray-400 font-bold uppercase underline decoration-dotted tracking-tighter">กรุณาเลือกกิจกรรมและกดปุ่มโหลดรายชื่อ</div>
        </div>
    </div>
</div>

<style>
.report-tab { background: rgb(243, 244, 246); color: rgb(107, 114, 128); }
.report-tab.active { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3); }
.dark .report-tab { background: rgb(51, 65, 85); color: rgb(148, 163, 184); }
.dark .report-tab.active { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
let bestClubs = [];

// Tab switching
document.querySelectorAll('.report-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.report-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
        
        if (this.dataset.tab === 'overview') updateOverview();
        if (this.dataset.tab === 'level') updateLevel();
        if (this.dataset.tab === 'room') updateRoomUi();
    });
});

async function refreshData() {
    const icon = document.getElementById('refresh-icon');
    icon.classList.add('fa-spin');
    await loadInitialData();
    icon.classList.remove('fa-spin');
    Swal.fire({ icon: 'success', title: 'อัปเดตข้อมูลแล้ว', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
}

async function loadInitialData() {
    try {
        const res = await fetch('api/best_fetch_overview.php');
        const data = await res.json();
        if (data && (data.success || data.ok)) {
            const rawData = data.data || data.members || [];
            bestClubs = rawData.map(c => ({
                ...c,
                activity_id: c.activity_id || c.id,
                activity_name: c.activity_name || c.name || 'ไม่ระบุชื่อ',
                current_members_count: parseInt(c.current_members_count || c.count || 0)
            }));
            
            updateStats();
            updateOverview();
            populateActivitySelect();
            populateRoomSelectors();
        }
    } catch (e) {
        console.error('Initial data error:', e);
    }
}

function updateStats() {
    const total = bestClubs.length;
    const members = bestClubs.reduce((s, c) => s + (parseInt(c.current_members_count) || 0), 0);
    const capacity = bestClubs.reduce((s, c) => s + (parseInt(c.max_members) || 0), 0);
    const full = bestClubs.filter(c => parseInt(c.current_members_count) >= parseInt(c.max_members)).length;
    
    animateNumber('stat-activities', total);
    animateNumber('stat-members', members);
    animateNumber('stat-fill', capacity ? Math.round((members/capacity)*100) : 0, '%');
    animateNumber('stat-full', full);
}

function animateNumber(id, val, suffix = '') {
    const el = document.getElementById(id);
    if (!el) return;
    let start = 0;
    const end = val;
    const duration = 1000;
    const step = end / (duration / 16);
    const count = () => {
        start += step;
        if (start < end) { el.innerText = Math.floor(start).toLocaleString() + suffix; requestAnimationFrame(count); }
        else { el.innerText = end.toLocaleString() + suffix; }
    };
    count();
}

function updateOverview() {
    const body = document.getElementById('best-overview-body');
    const mobileGrid = document.getElementById('best-overview-mobile');
    
    if (!bestClubs.length) {
        const emptyMsg = 'ไม่พบข้อมูลกิจกรรม';
        body.innerHTML = `<tr><td colspan="6" class="py-10 text-center text-gray-400 font-bold uppercase">${emptyMsg}</td></tr>`;
        mobileGrid.innerHTML = `<div class="py-10 text-center text-gray-400 font-bold uppercase">${emptyMsg}</div>`;
        return;
    }

    // Render Desktop Table
    body.innerHTML = bestClubs.map((c, i) => {
        const current = parseInt(c.current_members_count) || 0;
        const max = parseInt(c.max_members) || 0;
        const percent = max ? Math.round((current/max)*100) : 0;
        return `
            <tr class="hover:bg-amber-50 dark:hover:bg-amber-900/10 transition-colors">
                <td class="py-4 px-6 text-center font-bold text-gray-400">${i+1}</td>
                <td class="py-4 px-6">
                    <div class="font-black text-gray-800 dark:text-white">${c.activity_name}</div>
                    <div class="text-[10px] text-gray-500 uppercase font-bold italic line-clamp-1">${c.description || 'ไม่มีรายละเอียด'}</div>
                </td>
                <td class="py-4 px-6 text-center font-black text-gray-600 dark:text-gray-400">${max}</td>
                <td class="py-4 px-6 text-center font-black ${percent >= 100 ? 'text-rose-500' : 'text-emerald-500'}">${current}</td>
                <td class="py-4 px-6">
                    <div class="flex flex-col items-center">
                        <span class="text-[9px] font-black mb-1">${percent}%</span>
                        <div class="w-12 h-1 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-1000 ${percent >= 100 ? 'bg-rose-500' : 'bg-emerald-500'}" style="width: ${percent}%"></div>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <div class="flex flex-wrap gap-1">
                        ${(c.grade_levels || '').split(',').map(g => `<span class="px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-slate-800 text-[9px] font-black text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-slate-700">${g.trim()}</span>`).join('')}
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    // Render Mobile Cards
    mobileGrid.innerHTML = bestClubs.map((c, i) => {
        const current = parseInt(c.current_members_count) || 0;
        const max = parseInt(c.max_members) || 0;
        const percent = max ? Math.round((current/max)*100) : 0;
        return `
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-slate-700 mb-4 animate__animated animate__fadeInUp" style="animation-delay: ${i * 0.05}s">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <div class="text-[10px] text-amber-500 font-black mb-1 uppercase tracking-wider">#${i+1}</div>
                        <h4 class="font-black text-gray-800 dark:text-white leading-tight">${c.activity_name}</h4>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700 px-3 py-1 rounded-lg border border-gray-100 dark:border-slate-600">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase block leading-none mb-1">สมาชิก</span>
                        <span class="text-sm font-black dark:text-white ${percent >= 100 ? 'text-rose-500' : 'text-emerald-500'}">${current}/${max}</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">ความเป้าหมาย</span>
                            <span class="text-[10px] font-black ${percent >= 100 ? 'text-rose-500' : 'text-emerald-500'}">${percent}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-1000 ${percent >= 100 ? 'bg-rose-500' : 'bg-emerald-500'}" style="width: ${percent}%"></div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        ${(c.grade_levels || '').split(',').map(g => `<span class="px-2 py-0.5 rounded-md bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-800 text-[9px] font-black text-gray-500 uppercase">${g.trim()}</span>`).join('')}
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

async function updateLevel() {
    const level = document.getElementById('level-select').value;
    const body = document.getElementById('best-level-body');
    const mobileBody = document.getElementById('best-level-mobile');
    
    document.querySelectorAll('.tab-content:not(.hidden) .loading-spinner').forEach(s => s.classList.remove('hidden'));
    
    try {
        const res = await fetch(`api/best_fetch_level_activity.php?level=${level}`);
        const data = await res.json();
        
        if (data && (data.ok || data.success)) {
            const rawData = data.data || [];
            if (rawData.length === 0) {
                const msg = `ไม่พบข้อมูล ม.${level}`;
                body.innerHTML = `<tr><td colspan="3" class="py-10 text-center text-gray-400 font-bold uppercase">${msg}</td></tr>`;
                mobileBody.innerHTML = `<div class="py-10 text-center text-gray-400 font-bold uppercase">${msg}</div>`;
                return;
            }
            
            // Desktop Table
            body.innerHTML = rawData.map((c, i) => `
                <tr class="hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-colors">
                    <td class="py-4 px-6 text-center font-bold text-gray-300">${i+1}</td>
                    <td class="py-4 px-6 font-black text-gray-700 dark:text-gray-200">${c.activity_name || c.name || 'ไม่ระบุชื่อ'}</td>
                    <td class="py-4 px-6 text-center">
                        <span class="px-4 py-1.5 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-black text-sm">${c.current_members_count || c.count || 0} คน</span>
                    </td>
                </tr>
            `).join('');

            // Mobile Cards
            mobileBody.innerHTML = rawData.map((c, i) => `
                <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-gray-100 dark:border-slate-700 flex justify-between items-center mb-3 shadow-sm shadow-emerald-500/5">
                    <div>
                        <div class="text-[10px] font-black text-emerald-500 mb-0.5 tracking-wider">ACTIVITY #${i+1}</div>
                        <div class="font-black text-gray-800 dark:text-white">${c.activity_name || c.name || 'ไม่ระบุ'}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] font-black text-gray-400 uppercase mb-1 tracking-tighter">สมาชิก</div>
                        <div class="text-lg font-black text-emerald-500 leading-none">${c.current_members_count || c.count || 0} <span class="text-[10px]">คน</span></div>
                    </div>
                </div>
            `).join('');
        }
    } catch (e) {
        console.error('Level data error:', e);
    } finally {
        document.querySelectorAll('.loading-spinner').forEach(s => s.classList.add('hidden'));
    }
}

function populateActivitySelect() {
    const select = document.getElementById('activity-select');
    select.innerHTML = '<option value="">-- เลือกกิจกรรม --</option>' + 
        bestClubs.map(c => `<option value="${c.activity_id}">${c.activity_name}</option>`).join('');
}

function populateRoomSelectors() {
    const lSelect = document.getElementById('room-level-select');
    lSelect.innerHTML = [1,2,3,4,5,6].map(i => `<option value="${i}">ม. ${i}</option>`).join('');
    
    const rSelect = document.getElementById('room-room-select');
    rSelect.innerHTML = Array.from({length: 15}, (_, i) => `<option value="${i+1}">ห้อง ${i+1}</option>`).join('');
}

function updateRoomUi() {
    const level = document.getElementById('room-level-select').value;
    updateRoomSummary(level);
}

async function updateRoomSummary(level) {
    const body = document.getElementById('best-room-summary-body');
    try {
        const res = await fetch(`api/best_fetch_room_activity.php?level=${level}`);
        const data = await res.json();
        if (data.rooms) {
            body.innerHTML = Object.keys(data.rooms).map(room => `
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 cursor-pointer transition-colors" onclick="loadRoomStudents('${level}', '${room}')">
                    <td class="py-3 px-2 font-bold text-gray-600 dark:text-gray-400 flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center text-[10px] text-gray-500 font-bold">${room}</div>
                        <span>ห้อง ${room}</span>
                    </td>
                    <td class="py-3 px-2 text-right">
                        <span class="px-2 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-black text-[10px] underline decoration-dotted">${data.rooms[room]} คน</span>
                    </td>
                </tr>
            `).join('');
        }
    } catch(e) {}
}

async function loadRoomStudents(level, room) {
    const body = document.getElementById('best-room-students-body');
    const mobileBody = document.getElementById('best-room-students-mobile');
    
    [body, mobileBody].forEach(b => b.innerHTML = '<div class="py-20 text-center col-span-full"><div class="w-8 h-8 border-4 border-amber-200 border-t-amber-500 rounded-full animate-spin mx-auto"></div></div>');
    
    try {
        const res = await fetch(`api/best_fetch_room_students.php?level=${level}&room=${room}`);
        const data = await res.json();
        const rawData = data.data || data.students || [];
        
        if ((data.success || data.ok) && rawData) {
            if (rawData.length === 0) {
                const msg = 'ไม่พบข้อมูลนักเรียน';
                body.innerHTML = `<tr><td colspan="5" class="py-10 text-center text-gray-400 font-bold">${msg}</td></tr>`;
                mobileBody.innerHTML = `<div class="py-10 text-center text-gray-400 font-bold uppercase">${msg}</div>`;
                return;
            }

            // Desktop Table
            body.innerHTML = rawData.map((s, i) => `
                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors">
                    <td class="py-4 px-6 text-center text-gray-300 font-bold">${i+1}</td>
                    <td class="py-4 px-6 font-mono text-xs font-black text-gray-400">${s.student_id}</td>
                    <td class="py-4 px-6 font-black text-gray-700 dark:text-gray-200">${s.name}</td>
                    <td class="py-4 px-6 text-center font-bold text-gray-500">${s.room || s.class_name}</td>
                    <td class="py-4 px-6">
                        <span class="px-3 py-1 rounded-lg bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 font-black text-[10px] uppercase border border-violet-200/50 dark:border-violet-800">${s.activity || s.activity_name || 'ไม่ได้สมัคร'}</span>
                    </td>
                </tr>
            `).join('');

            // Mobile Cards
            mobileBody.innerHTML = rawData.map((s, i) => `
                <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border-l-4 ${s.activity ? 'border-violet-500' : 'border-gray-300'} shadow-sm mb-3">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center text-[10px] font-black dark:text-white">${i+1}</span>
                            <span class="font-mono text-[10px] font-black text-gray-400">${s.student_id}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-gray-50 dark:bg-slate-700 text-[10px] font-black text-gray-500">${s.room || s.class_name}</span>
                    </div>
                    <div class="font-black text-gray-800 dark:text-white mb-3 text-sm">${s.name}</div>
                    <div class="flex items-center gap-2 bg-gray-50 dark:bg-slate-900 p-2 rounded-xl">
                        <i class="fas fa-star text-[10px] ${s.activity ? 'text-violet-500' : 'text-gray-300'}"></i>
                        <span class="text-[10px] font-black uppercase ${s.activity ? 'text-violet-600 dark:text-violet-400' : 'text-gray-400'}">${s.activity || s.activity_name || 'ยังไม่ได้ระบุรายวิชา'}</span>
                    </div>
                </div>
            `).join('');
        }
    } catch(e) {}
}

document.getElementById('level-select').addEventListener('change', updateLevel);
document.getElementById('room-level-select').addEventListener('change', updateRoomUi);
document.getElementById('room-search-btn').addEventListener('click', () => {
    const level = document.getElementById('room-level-select').value;
    const room = document.getElementById('room-room-select').value;
    loadRoomStudents(level, room);
});

document.getElementById('activity-load-btn').addEventListener('click', async function() {
    const activityId = document.getElementById('activity-select').value;
    if (!activityId) return Swal.fire('โปรดเลือกกิจกรรม', '', 'warning');
    
    const body = document.getElementById('best-activity-members-body');
    const mobileBody = document.getElementById('best-activity-members-mobile');
    const header = document.getElementById('activity-report-header');
    
    [body, mobileBody].forEach(b => b.innerHTML = '<div class="py-20 text-center col-span-full"><div class="w-10 h-10 border-4 border-emerald-200 border-t-emerald-500 rounded-full animate-spin mx-auto"></div></div>');
    
    try {
        const res = await fetch(`api/best_fetch_activity_members.php?id=${activityId}`);
        const data = await res.json();
        const members = data.data || data.members || [];
        
        if ((data.success || data.ok) && members) {
            const club = bestClubs.find(c => c.activity_id == activityId);
            document.getElementById('selected-activity-name').innerText = club ? club.activity_name : 'กิจกรรม';
            document.getElementById('selected-activity-stats').innerText = `สมาชิก ${members.length} คน / ชั้นที่รับ: ${club ? (club.grade_levels || '-') : '-'}`;
            header.classList.remove('hidden');
            
            // Desktop Table
            body.innerHTML = members.map((m, i) => `
                <tr class="hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-colors">
                    <td class="py-4 px-6 text-center text-gray-300 font-bold">${i+1}</td>
                    <td class="py-4 px-6 font-mono text-xs font-black text-gray-400">${m.student_id}</td>
                    <td class="py-4 px-6 font-black text-gray-700 dark:text-gray-200">${m.name}</td>
                    <td class="py-4 px-6 text-center font-bold text-gray-500">${m.room || m.class_name || ''}</td>
                    <td class="py-4 px-6 text-gray-400 text-[10px] font-bold">${m.created_at || '-'}</td>
                </tr>
            `).join('');

            // Mobile Cards
            mobileBody.innerHTML = members.map((m, i) => `
                <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 mb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">MEMBER #${i+1}</span>
                        <span class="text-[10px] font-mono text-gray-400 bg-gray-50 dark:bg-slate-700 px-1.5 py-0.5 rounded">${m.student_id}</span>
                    </div>
                    <div class="font-black text-gray-800 dark:text-white mb-2 leading-none">${m.name}</div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-50 dark:border-slate-700/50">
                        <div class="flex items-center gap-1.5 font-bold text-gray-500 text-[10px]">
                            <i class="fas fa-door-open text-gray-300"></i>
                            <span>${m.room || m.class_name || 'N/A'}</span>
                        </div>
                        <div class="text-[9px] font-bold text-gray-300 italic"><i class="fas fa-clock mr-1"></i>${m.created_at || '-'}</div>
                    </div>
                </div>
            `).join('');
        }
    } catch(e) {}
});

// Init
loadInitialData();

// Init
loadInitialData();
</script>

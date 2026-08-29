<!-- Header Section with Year Selector -->
<div class="mb-8 animate__animated animate__fadeIn">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 flex items-center justify-center shadow-lg shadow-orange-500/30 text-white shrink-0">
                <i class="fas fa-trophy text-2xl"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white">Best For Teen</h1>
                    <span id="year-badge" class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/50">
                        ปีการศึกษา <?= $current_year ?> (ปัจจุบัน)
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">กิจกรรมพัฒนาศักยภาพผู้เรียนและเสริมสร้างทักษะชีวิต</p>
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

            <button id="best-refresh" class="p-3 rounded-2xl bg-white/80 dark:bg-slate-800 text-amber-600 dark:text-amber-400 shadow-sm border border-gray-200/80 dark:border-slate-700 hover:shadow-md transition-all active:scale-95 backdrop-blur-md" title="รีเฟรชข้อมูล">
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

<!-- Summary Cards Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
    <!-- Activities Card -->
    <div class="glass rounded-3xl p-5 card-hover relative overflow-hidden border border-white/40 dark:border-white/10 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                <i class="fas fa-calendar-check text-lg"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/40 px-2.5 py-1 rounded-full">กิจกรรม</span>
        </div>
        <div id="card-activities" class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white mb-1">0</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">กิจกรรมทั้งหมด</p>
    </div>
    
    <!-- Capacity Card -->
    <div class="glass rounded-3xl p-5 card-hover relative overflow-hidden border border-white/40 dark:border-white/10 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                <i class="fas fa-users text-lg"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 px-2.5 py-1 rounded-full">ความจุ</span>
        </div>
        <div id="card-capacity" class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white mb-1">0</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">ที่เปิดรับทั้งหมด (คน)</p>
    </div>
    
    <!-- Registered Card -->
    <div class="glass rounded-3xl p-5 card-hover relative overflow-hidden border border-white/40 dark:border-white/10 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/20">
                <i class="fas fa-user-check text-lg"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/40 px-2.5 py-1 rounded-full">สมัครแล้ว</span>
        </div>
        <div id="card-registered" class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white mb-1">0</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">ยอดสมัครรวม (คน)</p>
    </div>
    
    <!-- Fill Rate Card -->
    <div class="glass rounded-3xl p-5 card-hover relative overflow-hidden border border-white/40 dark:border-white/10 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-violet-500/20">
                <i class="fas fa-chart-pie text-lg"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-wider text-violet-600 dark:text-violet-400 bg-violet-100 dark:bg-violet-900/40 px-2.5 py-1 rounded-full">อัตราการเต็ม</span>
        </div>
        <div id="card-fill" class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white mb-1">0%</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">ความหนาแน่นผู้สมัคร</p>
    </div>
</div>

<!-- Collapsible Chart Section -->
<div class="glass rounded-3xl shadow-lg border border-white/40 dark:border-white/10 p-5 md:p-6 mb-8">
    <div class="flex items-center justify-between cursor-pointer select-none" onclick="toggleChart()">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-md shadow-amber-500/20">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div>
                <h3 class="font-black text-gray-800 dark:text-white text-base">สถิติผู้สมัครยอดนิยม (Top 10)</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">เปรียบเทียบจำนวนผู้สมัครและที่นั่งคงเหลือ</p>
            </div>
        </div>
        <button type="button" class="p-2 rounded-xl glass text-gray-500 hover:text-amber-500 transition-colors">
            <i class="fas fa-chevron-up transition-transform duration-300" id="chart-toggle-icon"></i>
        </button>
    </div>
    
    <div id="chart-container" class="mt-6 pt-4 border-t border-gray-100 dark:border-slate-800 transition-all duration-300">
        <div class="h-64 md:h-72">
            <canvas id="best-chart"></canvas>
        </div>
    </div>
</div>

<!-- Filter & Search Section -->
<div class="glass rounded-3xl p-5 mb-8 border border-white/40 dark:border-white/10 shadow-sm space-y-4">
    <div class="flex flex-col md:flex-row gap-3">
        <!-- Search Input -->
        <div class="flex-1 relative group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-amber-500 transition-colors text-sm"></i>
            <input type="text" id="best-search" placeholder="ค้นหากิจกรรม (ชื่อ, ระดับชั้น, ID)..." 
                   class="w-full pl-11 pr-10 py-3.5 rounded-2xl border-2 border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-gray-700 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition-all font-bold text-sm placeholder:text-gray-400 placeholder:font-normal shadow-sm">
            <button type="button" id="btn-clear-search" onclick="clearSearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>

        <!-- Grade Filter Chips -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 shrink-0">
            <button type="button" class="grade-chip px-3.5 py-2.5 rounded-xl text-xs font-black bg-amber-500 text-white shadow-sm transition-all" data-grade="all">ทุกระดับชั้น</button>
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <button type="button" class="grade-chip px-3.5 py-2.5 rounded-xl text-xs font-black bg-white/70 dark:bg-slate-800/70 border border-gray-200/60 dark:border-slate-700/60 text-gray-600 dark:text-gray-300 hover:bg-amber-100 hover:text-amber-700 dark:hover:bg-slate-700 transition-all" data-grade="ม.<?= $i ?>">ม.<?= $i ?></button>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Mobile Cards View -->
<div id="activity-cards" class="md:hidden grid grid-cols-1 gap-4 mb-8">
    <div class="col-span-1 text-center py-16 glass rounded-3xl border border-white/40 dark:border-white/10">
        <div class="w-10 h-10 border-4 border-amber-200 border-t-amber-500 rounded-full animate-spin mx-auto mb-3"></div>
        <p class="text-gray-500 dark:text-gray-400 font-bold text-sm">กำลังโหลดรายการกิจกรรม...</p>
    </div>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block glass rounded-3xl shadow-xl overflow-hidden mb-8 border border-white/40 dark:border-white/10">
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-4 px-6 flex items-center justify-between text-white">
        <h3 class="text-base font-black flex items-center gap-2">
            <i class="fas fa-list"></i>
            <span>รายการกิจกรรม Best For Teen</span>
        </h3>
        <span id="table-year-label" class="text-xs font-bold opacity-90">ปีการศึกษา <?= $current_year ?></span>
    </div>
    <div class="overflow-x-auto">
        <table id="best-table" class="w-full">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-700 text-xs font-black text-gray-600 dark:text-gray-300">
                    <th class="py-4 px-4 text-center w-16">#</th>
                    <th class="py-4 px-6 text-left">ชื่อกิจกรรม</th>
                    <th class="py-4 px-4 text-center">ระดับชั้นที่รับ</th>
                    <th class="py-4 px-6 text-center w-48">สมาชิก / จำนวนที่รับ</th>
                    <th class="py-4 px-4 text-center w-28">สถานะ</th>
                </tr>
            </thead>
            <tbody id="best-body" class="divide-y divide-gray-100 dark:divide-slate-800/60">
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-400 font-bold">
                        <div class="w-10 h-10 border-4 border-amber-200 border-t-amber-500 rounded-full animate-spin mx-auto mb-3"></div>
                        กำลังโหลดข้อมูล...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const defaultCurrentYear = <?= $current_year ?>;
let selectedYear = defaultCurrentYear;
let allBestData = [];
let bestChart = null;
let selectedGradeFilter = 'all';
let isChartCollapsed = false;

function toggleChart() {
    isChartCollapsed = !isChartCollapsed;
    const container = document.getElementById('chart-container');
    const icon = document.getElementById('chart-toggle-icon');
    if (isChartCollapsed) {
        container.classList.add('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        container.classList.remove('hidden');
        icon.style.transform = 'rotate(0deg)';
        if (bestChart) bestChart.resize();
    }
}

function resetToCurrentYear() {
    document.getElementById('year-select').value = defaultCurrentYear;
    selectedYear = defaultCurrentYear;
    loadActivities();
}

async function loadActivities() {
    const isCurrent = (selectedYear === defaultCurrentYear);
    const badge = document.getElementById('year-badge');
    const historyNotice = document.getElementById('history-notice');
    const tableYearLabel = document.getElementById('table-year-label');

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

    try {
        const res = await fetch(`controllers/BestActivityController.php?action=list&year=${selectedYear}`);
        const data = await res.json();
        
        if (data.success) {
            allBestData = data.data || [];
            render(allBestData);
        } else {
            allBestData = [];
            render([]);
        }
    } catch (e) {
        console.error('Error fetching best activities:', e);
        render([]);
    }
}

function render(list) {
    updateSummary(list);
    renderChart(list);
    applyFilters();
}

function updateSummary(list) {
    const totalActivities = list.length;
    const totalCapacity = list.reduce((s, a) => s + (parseInt(a.max_members || 0)), 0);
    const totalCurrent = list.reduce((s, a) => s + (parseInt(a.current_members_count || 0)), 0);
    const fill = totalCapacity > 0 ? Math.round((totalCurrent / totalCapacity) * 100) : 0;
    
    animateCounter(document.getElementById('card-activities'), totalActivities);
    animateCounter(document.getElementById('card-capacity'), totalCapacity);
    animateCounter(document.getElementById('card-registered'), totalCurrent);
    animateCounter(document.getElementById('card-fill'), fill, '%');
}

function animateCounter(element, target, suffix = '') {
    if (!element) return;
    let current = 0;
    const step = Math.max(1, Math.floor(target / 20));
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

function renderChart(list) {
    const chartEl = document.getElementById('best-chart');
    if (!chartEl) return;
    
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.05)';
    const remainingBg = isDark ? 'rgba(51, 65, 85, 0.6)' : 'rgba(226, 232, 240, 0.8)';
    const remainingBorder = isDark ? 'rgba(71, 85, 105, 0.8)' : 'rgba(203, 213, 225, 1)';
    const legendColor = isDark ? '#e2e8f0' : '#334155';

    const ordered = [...list].sort((a, b) => (parseInt(b.current_members_count || 0) - parseInt(a.current_members_count || 0)));
    const top = ordered.slice(0, 10);
    const labels = top.map(a => (a.name || ('กิจกรรม #' + a.id)).substring(0, 14));
    const current = top.map(a => parseInt(a.current_members_count || 0));
    const capacity = top.map(a => parseInt(a.max_members || 0));
    const remaining = capacity.map((c, i) => Math.max(0, c - current[i]));
    const ctx = chartEl.getContext('2d');
    
    if (bestChart) { bestChart.destroy(); }
    
    bestChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { 
                    label: 'สมัครแล้ว (คน)', 
                    data: current, 
                    backgroundColor: 'rgba(245, 158, 11, 0.85)',
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 1,
                    borderRadius: 8
                },
                { 
                    label: 'คงเหลือ (ที่นั่ง)', 
                    data: remaining, 
                    backgroundColor: remainingBg,
                    borderColor: remainingBorder,
                    borderWidth: 1,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: {
                        color: legendColor,
                        font: { family: 'Mali', size: 12, weight: 'bold' },
                        usePointStyle: true,
                        boxWidth: 8
                    }
                }
            },
            scales: { 
                x: { 
                    stacked: true,
                    grid: { display: false },
                    ticks: {
                        color: textColor,
                        font: { family: 'Mali', size: 10, weight: 'bold' },
                        maxRotation: 30,
                        minRotation: 30
                    }
                }, 
                y: { 
                    stacked: true, 
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { family: 'Mali', size: 10 } }
                }
            }
        }
    });
}

function clearSearch() {
    const el = document.getElementById('best-search');
    el.value = '';
    document.getElementById('btn-clear-search').classList.add('hidden');
    applyFilters();
}

function applyFilters() {
    const q = document.getElementById('best-search').value.trim().toLowerCase();
    const clearBtn = document.getElementById('btn-clear-search');
    if (clearBtn) {
        if (q) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }

    let filtered = allBestData;
    if (q) {
        filtered = filtered.filter(a => 
            (a.name || '').toLowerCase().includes(q) || 
            (a.id || '').toString().includes(q) ||
            (a.grade_levels || '').toLowerCase().includes(q) ||
            (a.description || '').toLowerCase().includes(q)
        );
    }

    if (selectedGradeFilter !== 'all') {
        filtered = filtered.filter(a => 
            (a.grade_levels || '').includes(selectedGradeFilter) || 
            (a.grade_levels || '').includes('ทั้งหมด')
        );
    }

    renderMobileCards(filtered);
    renderDesktopTable(filtered);
}

function renderMobileCards(list) {
    const container = document.getElementById('activity-cards');
    if (!container) return;
    
    if (list.length === 0) {
        container.innerHTML = `
            <div class="col-span-1 text-center py-16 glass rounded-3xl border border-white/40 dark:border-white/10">
                <i class="fas fa-folder-open text-gray-300 dark:text-gray-600 text-3xl mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400 font-bold mb-1">ไม่พบข้อมูลกิจกรรม</p>
                <p class="text-xs text-gray-400">ลองใช้คำค้นหาอื่นหรือเปลี่ยนตัวกรองระดับชั้น</p>
            </div>`;
        return;
    }
    
    container.innerHTML = list.map((a, index) => {
        const current = parseInt(a.current_members_count || 0);
        const max = parseInt(a.max_members || 0);
        const percent = max > 0 ? Math.round((current / max) * 100) : 0;
        const isFull = current >= max && max > 0;

        const gradeLevels = (a.grade_levels || '').split(',').map(g => 
            `<span class="px-2 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-[10px] font-black">${g.trim()}</span>`
        ).join(' ');
        
        return `
            <div class="glass rounded-3xl p-5 shadow-sm border border-white/40 dark:border-white/10 relative overflow-hidden transition-all duration-300 hover:shadow-md">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br ${isFull ? 'from-rose-500 to-red-600' : 'from-amber-500 to-orange-600'} flex items-center justify-center text-white text-lg font-black shadow-lg shrink-0">
                            <i class="fas ${isFull ? 'fa-lock' : 'fa-star'}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[10px] text-amber-500 font-black tracking-wider uppercase">#${a.id}</div>
                            <h4 class="font-black text-gray-800 dark:text-white leading-tight text-base truncate">${a.name || '-'}</h4>
                            ${a.description ? `<p class="text-xs text-gray-400 dark:text-gray-500 line-clamp-1 mt-0.5">${a.description}</p>` : ''}
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black ${isFull ? 'bg-rose-500 text-white' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300'} shrink-0">
                        ${isFull ? 'เต็ม' : 'เปิดรับ'}
                    </span>
                </div>

                <div class="flex flex-wrap gap-1 mb-4">
                    ${gradeLevels}
                </div>
                
                <div class="bg-black/5 dark:bg-white/5 rounded-2xl p-3">
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-[10px] uppercase font-black text-gray-400 dark:text-gray-500">สมาชิก / ความจุ</span>
                        <span class="text-xs font-black ${isFull ? 'text-rose-500' : 'text-amber-600 dark:text-amber-400'}">${current} / ${max} คน (${percent}%)</span>
                    </div>
                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-1000 ${isFull ? 'bg-rose-500' : 'bg-gradient-to-r from-amber-500 to-orange-500'}" style="width: ${Math.min(percent, 100)}%"></div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function renderDesktopTable(list) {
    const tbody = document.getElementById('best-body');
    if (!tbody) return;

    if (list.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="py-16 text-center text-gray-400 font-bold">
                    <i class="fas fa-folder-open text-3xl mb-3 block text-gray-300 dark:text-gray-600"></i>
                    ไม่พบข้อมูลกิจกรรม
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = list.map((a, idx) => {
        const current = parseInt(a.current_members_count || 0);
        const max = parseInt(a.max_members || 0);
        const percent = max > 0 ? Math.round((current / max) * 100) : 0;
        const isFull = current >= max && max > 0;
        
        const grades = (a.grade_levels || '').split(',').map(g => 
            `<span class="px-2 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-xs font-black">${g.trim()}</span>`
        ).join(' ');

        return `
            <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-700/40 transition-colors">
                <td class="py-4 px-4 text-center font-bold text-gray-400 dark:text-gray-500">${idx + 1}</td>
                <td class="py-4 px-6">
                    <div class="font-black text-gray-800 dark:text-white text-base">${a.name || ''}</div>
                    ${a.description ? `<div class="text-xs text-gray-400 dark:text-gray-500 line-clamp-1 mt-0.5">${a.description}</div>` : ''}
                </td>
                <td class="py-4 px-4 text-center">
                    <div class="flex gap-1 justify-center flex-wrap">${grades}</div>
                </td>
                <td class="py-4 px-6">
                    <div class="w-full max-w-[160px] mx-auto">
                        <div class="flex justify-between items-center text-xs font-black mb-1">
                            <span class="${isFull ? 'text-rose-500' : 'text-amber-600 dark:text-amber-400'}">${current} / ${max}</span>
                            <span class="text-gray-400 dark:text-gray-500 font-bold">${percent}%</span>
                        </div>
                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-1000 ${isFull ? 'bg-rose-500' : 'bg-gradient-to-r from-amber-500 to-orange-500'}" style="width: ${Math.min(percent, 100)}%"></div>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-4 text-center">
                    <span class="px-3 py-1 rounded-full text-xs font-black ${isFull ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300'}">
                        ${isFull ? 'เต็ม' : 'เปิดรับ'}
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

document.addEventListener('DOMContentLoaded', function() {
    loadActivities();
    
    // Year change listener
    document.getElementById('year-select').addEventListener('change', function() {
        selectedYear = parseInt(this.value);
        loadActivities();
    });

    // Search input
    document.getElementById('best-search').addEventListener('input', applyFilters);

    // Refresh button
    document.getElementById('best-refresh').addEventListener('click', () => {
        const icon = document.getElementById('refresh-icon');
        icon.classList.add('fa-spin');
        loadActivities().then(() => {
            setTimeout(() => icon.classList.remove('fa-spin'), 600);
        });
    });

    // Grade filter buttons
    document.querySelectorAll('.grade-chip').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedGradeFilter = this.dataset.grade;
            document.querySelectorAll('.grade-chip').forEach(b => {
                b.className = 'grade-chip px-3.5 py-2.5 rounded-xl text-xs font-black bg-white/70 dark:bg-slate-800/70 border border-gray-200/60 dark:border-slate-700/60 text-gray-600 dark:text-gray-300 hover:bg-amber-100 hover:text-amber-700 dark:hover:bg-slate-700 transition-all';
            });
            this.className = 'grade-chip px-3.5 py-2.5 rounded-xl text-xs font-black bg-amber-500 text-white shadow-sm transition-all';
            applyFilters();
        });
    });

    // Theme change listener for Chart.js
    window.addEventListener('themeChanged', () => {
        if (allBestData && allBestData.length > 0) {
            renderChart(allBestData);
        }
    });

    const observer = new MutationObserver(() => {
        if (allBestData && allBestData.length > 0) {
            renderChart(allBestData);
        }
    });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});
</script>


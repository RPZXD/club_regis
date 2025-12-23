<!-- Header Section -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 flex items-center justify-center shadow-lg shadow-orange-500/30">
            <i class="fas fa-trophy text-white text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white">Best For Teen 2025</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">กิจกรรมพัฒนาทักษะสำหรับนักเรียน</p>
        </div>
    </div>
</div>

<!-- Summary Cards - Mobile Optimized -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Activities Card -->
    <div class="glass rounded-2xl p-4 md:p-5 card-hover border-b-4 border-blue-500">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <i class="fas fa-calendar-check text-blue-600 dark:text-blue-400"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500">รายการ</span>
        </div>
        <div id="card-activities" class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white">0</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">กิจกรรมทั้งหมด</p>
    </div>
    
    <!-- Capacity Card -->
    <div class="glass rounded-2xl p-4 md:p-5 card-hover border-b-4 border-emerald-500">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                <i class="fas fa-users text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-500">รองรับ</span>
        </div>
        <div id="card-capacity" class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white">0</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">ที่ว่างทั้งหมด</p>
    </div>
    
    <!-- Registered Card -->
    <div class="glass rounded-2xl p-4 md:p-5 card-hover border-b-4 border-violet-500">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                <i class="fas fa-user-check text-violet-600 dark:text-violet-400"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-violet-500">ลงชื่อ</span>
        </div>
        <div id="card-registered" class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white">0</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">สมัครแล้ว</p>
    </div>
    
    <!-- Fill Rate Card -->
    <div class="glass rounded-2xl p-4 md:p-5 card-hover border-b-4 border-amber-500">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                <i class="fas fa-chart-pie text-amber-600 dark:text-amber-400"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-500">อัตรา</span>
        </div>
        <div id="card-fill" class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white">0%</div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">ความหนาแน่น</p>
    </div>
</div>

<!-- Chart Section -->
<div class="hidden sm:block glass rounded-2xl shadow-xl p-6 mb-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center shadow-lg">
            <i class="fas fa-chart-line text-white"></i>
        </div>
        <h3 class="font-bold text-gray-800 dark:text-white">สถิติการสมัคร (Top 10)</h3>
    </div>
    <div class="h-64">
        <canvas id="best-chart"></canvas>
    </div>
</div>

<!-- Filter & Search Section -->
<div class="glass rounded-2xl shadow-xl p-4 md:p-6 mb-8">
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-amber-500 transition-colors"></i>
            <input type="text" id="best-search" placeholder="ค้นหาชื่อกิจกรรม..." 
                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all">
        </div>
        <div class="flex gap-2">
            <select id="best-grade-filter" class="px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all min-w-[140px]">
                <option value="">ทุกระดับชั้น</option>
                <option value="ม.1">ม.1</option>
                <option value="ม.2">ม.2</option>
                <option value="ม.3">ม.3</option>
                <option value="ม.4">ม.4</option>
                <option value="ม.5">ม.5</option>
                <option value="ม.6">ม.6</option>
            </select>
            <button id="best-refresh" class="p-3 rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/20">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
</div>

<!-- Mobile Cards View -->
<div id="activity-cards" class="md:hidden grid grid-cols-1 gap-4 mb-8">
    <!-- Cards populated by JS -->
    <div class="col-span-1 text-center py-12 glass rounded-2xl">
        <div class="loader mx-auto mb-4"></div>
        <p class="text-gray-500 dark:text-gray-400 font-medium font-mali">กำลังโหลดรายการกิจกรรม...</p>
    </div>
</div>

<!-- Desktop Table Card -->
<div class="hidden md:block glass rounded-2xl shadow-xl overflow-hidden">
    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="best-table" class="w-full display">
                <thead>
                    <tr class="bg-gradient-to-r from-amber-500 to-orange-600 text-white">
                        <th class="py-3 px-4 text-center font-bold">รหัส</th>
                        <th class="py-3 px-4 text-center font-bold">ชื่อกิจกรรม</th>
                        <th class="py-3 px-4 text-center font-bold">ระดับชั้น</th>
                        <th class="py-3 px-4 text-center font-bold">จำนวนที่รับ</th>
                        <th class="py-3 px-4 text-center font-bold">ปี</th>
                        <th class="py-3 px-4 text-center font-bold">สมาชิก</th>
                    </tr>
                </thead>
                <tbody id="best-body"></tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
let allBestData = [];
let table; let bestChart = null;

function fetchList() {
    return fetch('controllers/BestActivityController.php?action=list').then(r => r.json());
}

function renderMobileCards(list) {
    const container = document.getElementById('activity-cards');
    
    if (list.length === 0) {
        container.innerHTML = `
            <div class="col-span-1 text-center py-12 glass rounded-2xl">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-gray-400 text-xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-bold mb-1">ไม่พบข้อมูลกิจกรรม</p>
                <p class="text-xs text-gray-400">ลองใช้คำค้นหาอื่นหรือเปลี่ยนตัวกรอง</p>
            </div>`;
        return;
    }
    
    container.innerHTML = list.map((a, index) => {
        const current = parseInt(a.current_members_count || 0);
        const max = parseInt(a.max_members || 0);
        const percent = max > 0 ? Math.round((current / max) * 100) : 0;
        const isFull = current >= max && max > 0;
        
        // Dynamic colors
        const accentColor = isFull ? 'red' : percent >= 80 ? 'orange' : 'emerald';
        const bgGradient = isFull 
            ? 'from-red-50 to-white dark:from-red-900/10 dark:to-slate-900' 
            : 'from-amber-50 to-white dark:from-amber-900/10 dark:to-slate-900';

        const gradeLevels = (a.grade_levels || '').split(',').map(g => 
            `<span class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-[10px] font-bold shadow-sm">${g.trim()}</span>`
        ).join('');
        
        return `
            <div class="glass rounded-2xl p-4 card-hover overflow-hidden relative group">
                <!-- Status Badge -->
                <div class="absolute top-0 right-0">
                    <div class="px-4 py-1 pb-2 rounded-bl-2xl ${isFull ? 'bg-red-500' : 'bg-emerald-500'} text-white text-[10px] font-black shadow-lg">
                        ${isFull ? 'เต็มแล้ว' : 'เปิดรับสมัคร'}
                    </div>
                </div>

                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-lg font-black shadow-lg shrink-0 group-hover:rotate-12 transition-transform">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="flex-1 min-w-0 pr-16">
                        <h4 class="font-black text-gray-800 dark:text-white leading-tight mb-1 truncate text-lg">${a.name || '-'}</h4>
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                            <span class="flex items-center gap-1"><i class="far fa-calendar-alt"></i> ปี ${a.year || '-'}</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                            <span class="flex items-center gap-1 text-amber-600 dark:text-amber-400"><i class="fas fa-fingerprint"></i> ID: ${a.id}</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-1.5 mb-5 pl-1">
                    ${gradeLevels}
                </div>
                
                <div class="bg-gray-50 dark:bg-slate-800/50 rounded-2xl p-3 border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-end mb-2">
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-wider font-bold text-gray-400 dark:text-gray-500">ความจุสมาชิก</span>
                            <div class="flex items-baseline gap-1">
                                <span class="text-xl font-black text-gray-800 dark:text-white">${current}</span>
                                <span class="text-sm font-bold text-gray-400">/ ${max}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-black ${isFull ? 'text-red-500' : 'text-emerald-500'}">${percent}%</span>
                        </div>
                    </div>
                    <div class="h-2.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden shadow-inner flex">
                        <div class="h-full rounded-full transition-all duration-1000 ease-out ${isFull ? 'bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]' : 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]'}" 
                             style="width: ${Math.min(percent, 100)}%"></div>
                    </div>
                </div>

                <!-- Glow effect on hover -->
                <div class="absolute -inset-1 bg-gradient-to-r ${isFull ? 'from-red-500 to-orange-500' : 'from-emerald-500 to-teal-500'} rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity blur"></div>
            </div>
        `;
    }).join('');
}

function filterAndRender() {
    const searchTerm = document.getElementById('best-search').value.toLowerCase();
    const gradeFilter = document.getElementById('best-grade-filter').value;
    
    let filtered = allBestData;
    
    if (searchTerm) {
        filtered = filtered.filter(a => 
            (a.name || '').toLowerCase().includes(searchTerm) || 
            (a.id || '').toString().includes(searchTerm)
        );
    }
    
    if (gradeFilter) {
        filtered = filtered.filter(a => 
            (a.grade_levels || '').includes(gradeFilter)
        );
    }
    
    // Update both mobile and desktop views
    renderMobileCards(filtered);
    
    // For desktop table, we use DataTable's built-in search/filter
    if (table) {
        table.search(searchTerm);
        if (gradeFilter) {
            table.column(2).search(gradeFilter);
        } else {
            table.column(2).search('');
        }
        table.draw();
    }
}

function render(list) {
    allBestData = list;
    
    // Initial Render
    renderMobileCards(list);
    
    // Render desktop table
    if (!table) {
        table = $('#best-table').DataTable({
            ordering: true,
            order: [[0,'asc']],
            dom: 'tpr', // Remove duplicate search/length controls
            language: { 
                zeroRecords: "ไม่พบข้อมูล",
                infoEmpty: "ไม่มีข้อมูล",
                paginate: { first: "แรก", last: "สุดท้าย", next: "ถัดไป", previous: "ก่อนหน้า" }
            }
        });
    }
    table.clear();
    list.forEach(a => {
        const current = parseInt(a.current_members_count||0);
        const max = parseInt(a.max_members||0);
        const percent = max > 0 ? Math.round((current/max)*100) : 0;
        let barColor = percent >= 100 ? 'bg-red-500' : percent >= 70 ? 'bg-yellow-400' : 'bg-emerald-500';
        const progress = `
            <div class="w-32 mx-auto">
                <div class="relative h-6 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="absolute left-0 top-0 h-6 ${barColor} transition-all" style="width:${Math.min(percent, 100)}%"></div>
                    <div class="absolute w-full text-xs text-center top-0 left-0 h-6 leading-6 font-bold text-gray-700 dark:text-gray-200">${current} / ${max}</div>
                </div>
            </div>`;
        table.row.add([
            a.id,
            `<span class="font-bold text-amber-600 dark:text-amber-400">${a.name}</span>`,
            a.grade_levels,
            progress,
            a.year,
            current
        ]);
    });
    table.draw(false);
    updateSummary(list);
    renderChart(list);
}

function animateCounter(element, target, suffix = '') {
    let current = 0;
    const increment = target / 20;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toLocaleString() + suffix;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString() + suffix;
        }
    }, 30);
}

function updateSummary(list) {
    const totalActivities = list.length;
    const totalCapacity = list.reduce((s,a)=> s + (parseInt(a.max_members||0)), 0);
    const totalCurrent = list.reduce((s,a)=> s + (parseInt(a.current_members_count||0)), 0);
    const fill = totalCapacity > 0 ? Math.round((totalCurrent/totalCapacity)*100) : 0;
    
    animateCounter(document.getElementById('card-activities'), totalActivities);
    animateCounter(document.getElementById('card-capacity'), totalCapacity);
    animateCounter(document.getElementById('card-registered'), totalCurrent);
    animateCounter(document.getElementById('card-fill'), fill, '%');
}

function renderChart(list) {
    const chartEl = document.getElementById('best-chart');
    if (!chartEl) return;
    
    const ordered = [...list].sort((a,b)=> (parseInt(b.current_members_count||0) - parseInt(a.current_members_count||0)));
    const top = ordered.slice(0, 10);
    const labels = top.map(a=> (a.name || ('กิจกรรม #' + a.id)).substring(0, 12));
    const current = top.map(a=> parseInt(a.current_members_count||0));
    const capacity = top.map(a=> parseInt(a.max_members||0));
    const remaining = capacity.map((c, i)=> Math.max(0, c - current[i]));
    const ctx = chartEl.getContext('2d');
    
    if (bestChart) { bestChart.destroy(); }
    
    bestChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { 
                    label: 'สมัครแล้ว', 
                    data: current, 
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                },
                { 
                    label: 'คงเหลือ', 
                    data: remaining, 
                    backgroundColor: 'rgba(209, 213, 219, 0.5)',
                    borderColor: 'rgba(209, 213, 219, 0.8)',
                    borderWidth: 1,
                    borderRadius: 4
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
                        font: { family: 'Mali', size: 11 },
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
                        font: { family: 'Mali', size: 9 },
                        maxRotation: 45,
                        minRotation: 45
                    }
                }, 
                y: { 
                    stacked: true, 
                    beginAtZero: true,
                    grid: { color: 'rgba(156, 163, 175, 0.2)' },
                    ticks: { font: { family: 'Mali', size: 10 } }
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    fetchList().then(d=>{ if (d.data) render(d.data); });
    
    // Add Event Listeners
    document.getElementById('best-search').addEventListener('input', filterAndRender);
    document.getElementById('best-grade-filter').addEventListener('change', filterAndRender);
    document.getElementById('best-refresh').addEventListener('click', () => {
        const btn = document.getElementById('best-refresh');
        btn.querySelector('i').classList.add('fa-spin');
        fetchList().then(d=>{ 
            if (d.data) {
                render(d.data);
                filterAndRender();
            }
            setTimeout(() => btn.querySelector('i').classList.remove('fa-spin'), 600);
        });
    });
});
</script>

<style>
.loader {
    border: 3px solid rgba(245, 158, 11, 0.2);
    border-top: 3px solid #f59e0b;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.font-mali { font-family: 'Mali', sans-serif; }
</style>


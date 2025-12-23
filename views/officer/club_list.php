<!-- Header Section with Stats Bar -->
<div class="mb-8 animate__animated animate__fadeIn">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                <i class="fas fa-list-check text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-800 dark:text-white tracking-tight">รายการชุมนุม</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">จัดการและตรวจสอบข้อมูลชุมนุมทั้งหมดในระบบ</p>
            </div>
        </div>
        
        <div class="flex gap-2">
            <button onclick="loadData()" class="p-3 rounded-xl bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all active:scale-95">
                <i class="fas fa-sync-alt" id="refresh-icon"></i>
            </button>
            <a href="index.php" class="flex items-center gap-2 px-5 py-3 rounded-xl bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all active:scale-95 font-bold">
                <i class="fas fa-arrow-left text-xs"></i>
                กลับหน้าหลัก
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="stats-container">
        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm">
            <div class="text-[10px] uppercase font-black text-blue-500 mb-1 tracking-wider">ชุมนุมทั้งหมด</div>
            <div class="text-2xl font-black text-gray-800 dark:text-white" id="stat-total-clubs">-</div>
        </div>
        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm">
            <div class="text-[10px] uppercase font-black text-emerald-500 mb-1 tracking-wider">สมัครแล้ว</div>
            <div class="text-2xl font-black text-gray-800 dark:text-white" id="stat-total-members">-</div>
        </div>
        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm">
            <div class="text-[10px] uppercase font-black text-amber-500 mb-1 tracking-wider">ที่รับทั้งหมด</div>
            <div class="text-2xl font-black text-gray-800 dark:text-white" id="stat-total-capacity">-</div>
        </div>
        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm">
            <div class="text-[10px] uppercase font-black text-rose-500 mb-1 tracking-wider">ชุมนุมที่เต็ม</div>
            <div class="text-2xl font-black text-gray-800 dark:text-white" id="stat-full-clubs">-</div>
        </div>
    </div>
</div>

<!-- Search & Filter Controls -->
<div class="glass rounded-3xl p-6 mb-8 border border-white/50 dark:border-white/10 shadow-xl shadow-blue-500/5 animate__animated animate__fadeInUp">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Search -->
        <div class="flex-1">
            <label class="block text-sm font-black text-gray-700 dark:text-gray-300 mb-2 ml-1">ค้นหาชุมนุม</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-blue-400 group-focus-within:text-blue-600 transition-colors"></i>
                </div>
                <input type="text" id="club-search" placeholder="พิมพ์ชื่อชุมนุม หรือครูที่ปรึกษา..." 
                       class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white dark:bg-slate-900 border-2 border-blue-50 dark:border-slate-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:border-blue-400 dark:focus:border-blue-500 transition-all font-bold placeholder:font-normal shadow-sm">
            </div>
        </div>
        
        <!-- Filter -->
        <div class="lg:w-1/2">
            <div class="flex items-center justify-between mb-2 ml-1">
                <label class="block text-sm font-black text-gray-700 dark:text-gray-300">กรองระดับชั้น</label>
                <div class="flex gap-2">
                    <button id="select-all-grades" class="text-[10px] font-black text-blue-600 uppercase hover:underline">เลือกทั้งหมด</button>
                    <button id="clear-all-grades" class="text-[10px] font-black text-rose-600 uppercase hover:underline">ล้างออก</button>
                </div>
            </div>
            <div id="grade-checkboxes" class="flex flex-wrap gap-2 p-1">
                <!-- Grade items will be injected here -->
            </div>
        </div>
    </div>
</div>

<!-- Loading State -->
<div id="loading-state" class="py-20 text-center">
    <div class="inline-block relative w-20 h-20">
        <div class="absolute top-0 left-0 w-full h-full border-4 border-blue-100 dark:border-slate-800 rounded-full"></div>
        <div class="absolute top-0 left-0 w-full h-full border-4 border-blue-600 rounded-full border-t-transparent animate-spin"></div>
    </div>
    <p class="mt-4 text-gray-500 dark:text-gray-400 font-bold animate-pulse">กำลังดึงข้อมูลจากเซิร์ฟเวอร์...</p>
</div>

<!-- Empty State -->
<div id="empty-state" class="hidden py-20 text-center glass rounded-3xl border-2 border-dashed border-gray-200 dark:border-slate-800">
    <div class="w-24 h-24 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-folder-open text-gray-300 dark:text-gray-600 text-4xl"></i>
    </div>
    <h3 class="text-xl font-black text-gray-800 dark:text-white mb-2">ไม่พบข้อมูลชุมนุม</h3>
    <p class="text-gray-500 dark:text-gray-400">ลองเปลี่ยนการค้นหา หรือรีเฟรชข้อมูลใหม่อีกครั้ง</p>
    <button onclick="loadData()" class="mt-6 px-6 py-3 rounded-xl bg-blue-600 text-white font-bold shadow-lg shadow-blue-500/30 active:scale-95 transition-all">
        ลองอีกครั้ง
    </button>
</div>

<!-- Mobile Cards View (Hidden on Tablet/Desktop) -->
<div id="club-cards" class="grid grid-cols-1 gap-4 md:hidden"></div>

<!-- Desktop Table View (Hidden on Mobile) -->
<div id="club-table-container" class="hidden md:block glass rounded-3xl overflow-hidden border border-white/40 dark:border-white/5 shadow-2xl animate__animated animate__fadeInUp">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gradient-to-r from-blue-600/90 to-indigo-700/90 text-white">
                    <th class="py-5 px-6 font-black text-sm uppercase tracking-wider text-center w-24">รหัส</th>
                    <th class="py-5 px-6 font-black text-sm uppercase tracking-wider">ชื่อชุมนุม / รายละเอียด</th>
                    <th class="py-5 px-6 font-black text-sm uppercase tracking-wider">ครูที่ปรึกษา</th>
                    <th class="py-5 px-6 font-black text-sm uppercase tracking-wider text-center">ระดับชั้น</th>
                    <th class="py-5 px-6 font-black text-sm uppercase tracking-wider text-center">สมาชิก / ที่รับ</th>
                    <th class="py-5 px-6 font-black text-sm uppercase tracking-wider text-center w-32">จัดการ</th>
                </tr>
            </thead>
            <tbody id="club-table-body" class="divide-y divide-gray-100 dark:divide-gray-800">
                <!-- Rows will be injected here -->
            </tbody>
        </table>
    </div>
</div>

<script>
let allClubs = [];
let gradeStats = {};

// Formatting utilities
const formatGrades = (str) => {
    if (!str) return '';
    return str.split(',').map(g => 
        `<span class="inline-block px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 text-[10px] font-black border border-blue-100 dark:border-blue-800/50 mr-1 mb-1 shadow-sm font-mono">${g.trim()}</span>`
    ).join('');
};

// Render Functions
function renderCard(club) {
    const current = parseInt(club.current_members_count || 0);
    const max = parseInt(club.max_members || 0);
    const percent = max > 0 ? Math.round((current / max) * 100) : 0;
    const isFull = percent >= 100;
    
    return `
        <div class="club-card bg-white dark:bg-slate-800 rounded-3xl shadow-lg shadow-gray-200/50 dark:shadow-black/20 overflow-hidden border border-gray-100/50 dark:border-gray-700 transition-all hover:shadow-xl hover:-translate-y-1 group" 
             data-name="${(club.club_name + ' ' + (club.advisor_teacher_name || '')).toLowerCase()}" 
             data-grades="${club.grade_levels}">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br ${isFull ? 'from-rose-500 to-red-600 shadow-rose-500/20' : 'from-blue-500 to-indigo-600 shadow-blue-500/20'} flex items-center justify-center text-white shadow-lg flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="fas ${isFull ? 'fa-lock' : 'fa-users-cog'} text-lg"></i>
                    </div>
                    ${isFull ? '<span class="px-2.5 py-1 rounded-full bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-rose-500/30">FULL</span>' : ''}
                </div>
                
                <h3 class="font-black text-gray-800 dark:text-white text-lg leading-tight mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">${club.club_name}</h3>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-6 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center text-[10px] text-gray-500 dark:text-gray-400">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 truncate">${club.advisor_teacher_name || club.advisor_teacher}</span>
                </div>
                
                <div class="mb-4">
                    ${formatGrades(club.grade_levels)}
                </div>
                
                <div class="p-4 rounded-2xl bg-gray-50 dark:bg-slate-900/50 border border-gray-100 dark:border-gray-800">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest tracking-tighter">Capacity</span>
                        <span class="text-sm font-black ${isFull ? 'text-rose-500' : 'text-blue-600'}">${current} / ${max}</span>
                    </div>
                    <div class="h-2.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full rounded-full transition-all duration-1000 ${isFull ? 'bg-gradient-to-r from-rose-500 to-red-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]' : 'bg-gradient-to-r from-blue-500 to-indigo-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]'}" style="width: ${percent}%"></div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 dark:bg-slate-900/30 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <span class="text-[11px] font-black font-mono text-gray-400 uppercase">ID ${club.club_id}</span>
                <a href="print_club.php?club_id=${club.club_id}" target="_blank" 
                   class="p-2.5 rounded-xl bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all active:scale-95 group/btn">
                    <i class="fas fa-print group-hover/btn:scale-110"></i>
                </a>
            </div>
        </div>`;
}

function renderTableRow(club) {
    const current = parseInt(club.current_members_count || 0);
    const max = parseInt(club.max_members || 0);
    const percent = max > 0 ? Math.round((current / max) * 100) : 0;
    const isFull = percent >= 100;
    
    return `
        <tr class="club-card hover:bg-blue-50/30 dark:hover:bg-blue-900/5 transition-colors group" 
            data-name="${(club.club_name + ' ' + (club.advisor_teacher_name || '')).toLowerCase()}" 
            data-grades="${club.grade_levels}">
            <td class="py-5 px-6 text-center font-black font-mono text-gray-400 text-xs">${club.club_id}</td>
            <td class="py-5 px-6">
                <div class="font-black text-gray-800 dark:text-white group-hover:text-blue-600 transition-colors uppercase tracking-tight">${club.club_name}</div>
                <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 line-clamp-1 italic">${club.description || 'ไม่มีรายละเอียด'}</div>
            </td>
            <td class="py-5 px-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-300 text-xs font-black shadow-sm">
                        ${(club.advisor_teacher_name || 'T').charAt(0)}
                    </div>
                    <span class="text-sm font-bold text-gray-600 dark:text-gray-300">${club.advisor_teacher_name || club.advisor_teacher}</span>
                </div>
            </td>
            <td class="py-5 px-6 text-center">
                <div class="flex flex-wrap justify-center gap-1">
                    ${formatGrades(club.grade_levels)}
                </div>
            </td>
            <td class="py-5 px-6">
                <div class="flex flex-col items-center">
                    <div class="text-xs font-black mb-1.5 ${isFull ? 'text-rose-600 dark:text-rose-400' : 'text-blue-600 dark:text-blue-400'}">
                        ${current} / ${max} ${isFull ? '<i class="fas fa-lock ml-1"></i>' : ''}
                    </div>
                    <div class="w-24 h-1.5 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full rounded-full transition-all duration-1000 ${isFull ? 'bg-rose-500' : 'bg-blue-500'}" style="width: ${percent}%"></div>
                    </div>
                </div>
            </td>
            <td class="py-5 px-6 text-center">
                <a href="print_club.php?club_id=${club.club_id}" target="_blank" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all active:scale-95 font-black text-[10px] uppercase tracking-widest">
                    <i class="fas fa-print"></i>
                    PRINT
                </a>
            </td>
        </tr>`;
}

// Stats & UI Logic
function updateStats(data) {
    const total = data.length;
    let registered = 0;
    let capacity = 0;
    let full = 0;
    
    data.forEach(c => {
        const cur = parseInt(c.current_members_count || 0);
        const max = parseInt(c.max_members || 0);
        registered += cur;
        capacity += max;
        if (cur >= max && max > 0) full++;
    });
    
    animateNumber('stat-total-clubs', total);
    animateNumber('stat-total-members', registered);
    animateNumber('stat-total-capacity', capacity);
    animateNumber('stat-full-clubs', full);
}

function animateNumber(id, val) {
    const el = document.getElementById(id);
    let start = 0;
    const end = parseInt(val);
    const duration = 1000;
    const step = end / (duration / 16);
    
    const count = () => {
        start += step;
        if (start < end) {
            el.innerText = Math.floor(start).toLocaleString();
            requestAnimationFrame(count);
        } else {
            el.innerText = end.toLocaleString();
        }
    };
    count();
}

function renderGradeFilter(grades) {
    const container = document.getElementById('grade-checkboxes');
    const sorted = Object.keys(grades).sort();
    container.innerHTML = sorted.map(g => `
        <label class="group/grad flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 cursor-pointer shadow-sm transition-all has-[:checked]:bg-blue-600 has-[:checked]:text-white has-[:checked]:border-blue-600 has-[:checked]:shadow-blue-500/20">
            <input type="checkbox" class="grade-filter-checkbox sr-only" value="${g}" checked>
            <span class="font-black text-sm tracking-tighter">${g}</span>
            <span class="text-[9px] bg-gray-100 dark:bg-slate-800 text-gray-500 group-has-[:checked]/grad:bg-white/20 group-has-[:checked]/grad:text-white px-1.5 rounded-lg shadow-inner font-mono">${grades[g]}</span>
        </label>
    `).join('');
}

function filterData() {
    const query = document.getElementById('club-search').value.toLowerCase();
    const checked = Array.from(document.querySelectorAll('.grade-filter-checkbox:checked')).map(cb => cb.value);
    
    let visibleCount = 0;
    document.querySelectorAll('.club-card').forEach(card => {
        const name = card.dataset.name;
        const grades = card.dataset.grades;
        const matchName = !query || name.includes(query);
        const matchGrade = checked.length === 0 || checked.some(g => grades.includes(g));
        
        if (matchName && matchGrade) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    document.getElementById('empty-state').classList.toggle('hidden', visibleCount > 0);
}

// Data Loading
async function loadData() {
    const loading = document.getElementById('loading-state');
    const cards = document.getElementById('club-cards');
    const tableContainer = document.getElementById('club-table-container');
    const empty = document.getElementById('empty-state');
    const icon = document.getElementById('refresh-icon');

    // Reset visibility
    loading.classList.remove('hidden');
    cards.classList.add('hidden');
    tableContainer.classList.add('hidden');
    empty.classList.add('hidden');
    icon.classList.add('fa-spin');

    try {
        const res = await fetch('../controllers/ClubController.php?action=list');
        const data = await res.json();
        console.log('Clubs API Response:', data);
        
        const clubData = data.data || data; // Flexible: check for data property or use whole response if it's an array
        
        if (Array.isArray(clubData)) {
            allClubs = clubData;
            
            // Stats & Filters
            updateStats(allClubs);
            let gradeCount = {};
            allClubs.forEach(club => {
                (club.grade_levels || '').split(',').forEach(g => {
                    g = g.trim();
                    if (g) gradeCount[g] = (gradeCount[g] || 0) + 1;
                });
            });
            renderGradeFilter(gradeCount);
            
            // Render Content
            cards.innerHTML = allClubs.map(c => renderCard(c)).join('');
            document.getElementById('club-table-body').innerHTML = allClubs.map(c => renderTableRow(c)).join('');
            
            // Show result
            loading.classList.add('hidden');
            if (allClubs.length > 0) {
                cards.classList.remove('hidden');
                tableContainer.classList.remove('hidden');
            } else {
                empty.classList.remove('hidden');
            }
        } else {
            console.error('Invalid data format:', data);
            throw new Error('API Error: Invalid format');
        }
    } catch (error) {
        console.error('Fetch error:', error);
        loading.classList.add('hidden');
        empty.classList.remove('hidden');
    } finally {
        icon.classList.remove('fa-spin');
    }
}

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    loadData();
    
    // Search Event
    document.getElementById('club-search').addEventListener('input', filterData);
    
    // Grade Filter Event Delegate
    document.getElementById('grade-checkboxes').addEventListener('change', filterData);

    // Filter Buttons
    document.getElementById('select-all-grades').addEventListener('click', () => {
        document.querySelectorAll('.grade-filter-checkbox').forEach(cb => cb.checked = true);
        filterData();
    });
    
    document.getElementById('clear-all-grades').addEventListener('click', () => {
        document.querySelectorAll('.grade-filter-checkbox').forEach(cb => cb.checked = false);
        filterData();
    });
});
</script>

<style>
/* Custom Scrollbar for desktop */
.overflow-x-auto::-webkit-scrollbar { height: 6px; }
.overflow-x-auto::-webkit-scrollbar-track { background: transparent; }
.overflow-x-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.dark .overflow-x-auto::-webkit-scrollbar-thumb { background: #334155; }

/* Line Clamp */
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;  
    overflow: hidden;
}

/* Animations shadow */
.shadow-blue-500\/5 { box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.05); }

/* Animation tweaks */
.animate__animated { animation-duration: 0.6s; }
</style>

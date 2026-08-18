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
                    <th class="py-5 px-6 font-black text-sm uppercase tracking-wider text-center">ภาคเรียน/ปีการศึกษา</th>
                    <th class="py-5 px-6 font-black text-sm uppercase tracking-wider text-center">สมาชิก / ที่รับ</th>
                    <th class="py-5 px-6 font-black text-sm uppercase tracking-wider text-center w-48">จัดการ</th>
                </tr>
            </thead>
            <tbody id="club-table-body" class="divide-y divide-gray-100 dark:divide-gray-800">
                <!-- Rows will be injected here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Manage Members -->
<div id="members-modal" class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm hidden p-0 md:p-4">
    <div class="bg-white dark:bg-slate-900 w-full md:max-w-3xl md:rounded-3xl rounded-t-3xl shadow-2xl max-h-[90vh] overflow-y-auto animate-slide-up flex flex-col">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 flex justify-between items-center text-white z-20">
            <div>
                <h3 id="members-modal-title" class="text-xl font-black">จัดการสมาชิก</h3>
                <p id="members-modal-subtitle" class="text-blue-200 text-sm">รายชื่อนักเรียนในชุมนุม</p>
            </div>
            <button onclick="closeMembersModal()" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-all active:scale-95">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <!-- Add Member Box with Autocomplete -->
            <div class="mb-6 p-4 rounded-2xl bg-blue-50/70 dark:bg-slate-800/70 border border-blue-100 dark:border-slate-700 relative">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-black text-blue-700 dark:text-blue-300 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-user-plus text-blue-600"></i>
                        เพิ่มนักเรียนเข้าชุมนุม
                    </label>
                    <span class="text-[10px] text-gray-400 font-bold">พิมพ์ชื่อ นามสกุล หรือ เลขประจำตัว</span>
                </div>
                
                <div class="relative" id="autocomplete-container">
                    <div class="relative flex items-center">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-400">
                            <i class="fas fa-search text-sm"></i>
                        </div>
                        <input type="text" id="add-student-search" 
                               placeholder="ค้นหาด้วยชื่อ, นามสกุล หรือ เลขประจำตัวนักเรียน..." 
                               autocomplete="off"
                               class="w-full pl-10 pr-10 py-3 rounded-xl bg-white dark:bg-slate-900 border-2 border-blue-200 dark:border-slate-700 text-gray-800 dark:text-white font-bold text-sm focus:border-blue-500 focus:outline-none transition-all shadow-sm">
                        <button id="btn-clear-search" type="button" onclick="clearStudentSearch()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hidden">
                            <i class="fas fa-times-circle text-sm"></i>
                        </button>
                        <div id="search-spinner" class="absolute inset-y-0 right-0 pr-3.5 flex items-center hidden">
                            <div class="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                    </div>

                    <!-- Autocomplete Dropdown List -->
                    <div id="autocomplete-results" class="absolute left-0 right-0 top-full mt-1.5 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-700 max-h-72 overflow-y-auto z-50 hidden divide-y divide-gray-100 dark:divide-slate-800">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div id="members-loading" class="text-center py-12">
                <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-gray-500 dark:text-gray-400 font-bold">กำลังโหลดรายชื่อสมาชิก...</p>
            </div>
            
            <!-- Table View -->
            <div id="members-table-container" class="hidden overflow-x-auto rounded-2xl border border-gray-100 dark:border-slate-800">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-800/50 text-gray-700 dark:text-gray-200 text-xs font-black uppercase border-b border-gray-100 dark:border-slate-800">
                            <th class="py-4 px-4 text-center w-12">#</th>
                            <th class="py-4 px-4 text-center w-24">รหัสประจำตัว</th>
                            <th class="py-4 px-4">ชื่อ-นามสกุล</th>
                            <th class="py-4 px-4 text-center w-20">ระดับชั้น</th>
                            <th class="py-4 px-4 text-center w-36">วันที่สมัคร</th>
                            <th class="py-4 px-4 text-center w-20">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="modal-members-body" class="divide-y divide-gray-100 dark:divide-slate-850">
                        <!-- Rows injected here -->
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div id="members-empty" class="hidden text-center py-16">
                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-slash text-gray-300 dark:text-gray-500 text-2xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-bold">ยังไม่มีสมาชิกในชุมนุมนี้</p>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="p-4 bg-gray-50 dark:bg-slate-900/30 border-t border-gray-100 dark:border-slate-800 text-right">
            <button onclick="closeMembersModal()" class="px-5 py-3 rounded-xl font-black text-gray-500 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 transition-all active:scale-95">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>
</div>

<script>
let allClubs = [];
let gradeStats = {};
let currentClubId = '';

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
    const escapedClubName = (club.club_name || '').replace(/'/g, "\\'");
    
    return `
        <div class="club-card bg-white dark:bg-slate-800 rounded-3xl shadow-lg shadow-gray-200/50 dark:shadow-black/20 overflow-hidden border border-gray-100/50 dark:border-gray-700 transition-all hover:shadow-xl hover:-translate-y-1 group" 
             data-name="${(club.club_name + ' ' + (club.advisor_teacher_name || '')).toLowerCase()}" 
             data-grades="${club.grade_levels}">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br ${isFull ? 'from-rose-500 to-red-600 shadow-rose-500/20' : 'from-blue-500 to-indigo-600 shadow-blue-500/20'} flex items-center justify-center text-white shadow-lg flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="fas ${isFull ? 'fa-lock' : 'fa-users-cog'} text-lg"></i>
                    </div>
                    <div class="flex flex-col items-end gap-1.5">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 text-[10px] font-black border border-blue-100 dark:border-blue-800/50">
                            <i class="far fa-calendar-alt text-[9px]"></i> ${club.term}/${club.year}
                        </span>
                        ${isFull ? '<span class="px-2.5 py-1 rounded-full bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-rose-500/30">FULL</span>' : ''}
                    </div>
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
            
            <div class="px-6 py-4 bg-gray-50 dark:bg-slate-900/30 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between gap-2">
                <span class="text-[11px] font-black font-mono text-gray-400 uppercase">ID ${club.club_id}</span>
                <div class="flex items-center gap-2">
                    <button onclick="manageMembers('${club.club_id}', '${escapedClubName}', '${club.term}', '${club.year}')"
                       class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 hover:shadow-lg transition-all active:scale-95 font-black text-[10px] uppercase tracking-wider">
                        <i class="fas fa-users-cog"></i>
                        สมาชิก
                    </button>
                    <a href="print_club.php?club_id=${club.club_id}" target="_blank" 
                       class="p-2.5 rounded-xl bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all active:scale-95 group/btn">
                        <i class="fas fa-print group-hover/btn:scale-110"></i>
                    </a>
                </div>
            </div>
        </div>`;
}

function renderTableRow(club) {
    const current = parseInt(club.current_members_count || 0);
    const max = parseInt(club.max_members || 0);
    const percent = max > 0 ? Math.round((current / max) * 100) : 0;
    const isFull = percent >= 100;
    const escapedClubName = (club.club_name || '').replace(/'/g, "\\'");
    
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
            <td class="py-5 px-6 text-center">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-black border border-blue-100/50 dark:border-blue-900/30 shadow-sm">
                    <i class="far fa-calendar-alt text-[10px]"></i>
                    ${club.term}/${club.year}
                </span>
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
                <div class="flex items-center justify-center gap-2">
                    <button onclick="manageMembers('${club.club_id}', '${escapedClubName}', '${club.term}', '${club.year}')"
                       class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 hover:shadow-lg transition-all active:scale-95 font-black text-[10px] uppercase tracking-wider">
                        <i class="fas fa-users-cog"></i>
                        สมาชิก
                    </button>
                    <a href="print_club.php?club_id=${club.club_id}" target="_blank" 
                       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all active:scale-95 font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-print"></i>
                        PRINT
                    </a>
                </div>
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

// Manage members dialog functions
async function manageMembers(clubId, clubName, term, year) {
    currentClubId = clubId;
    document.getElementById('members-modal-title').textContent = `จัดการสมาชิก - ${clubName}`;
    document.getElementById('members-modal-subtitle').textContent = `ภาคเรียนที่ ${term || '-'} ปีการศึกษา ${year || '-'}`;
    
    clearStudentSearch();

    const modal = document.getElementById('members-modal');
    modal.classList.remove('hidden');
    
    const loading = document.getElementById('members-loading');
    const tableContainer = document.getElementById('members-table-container');
    const emptyState = document.getElementById('members-empty');
    
    loading.classList.remove('hidden');
    tableContainer.classList.add('hidden');
    emptyState.classList.add('hidden');
    
    try {
        const res = await fetch(`../controllers/ClubController.php?action=members&club_id=${clubId}`);
        const data = await res.json();
        
        if (data.success && data.members && data.members.length > 0) {
            const tbody = document.getElementById('modal-members-body');
            tbody.innerHTML = data.members.map((stu, idx) => `
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="py-3.5 px-4 text-center font-bold text-gray-400 text-xs">${idx + 1}</td>
                    <td class="py-3.5 px-4 text-center font-mono font-bold text-blue-600 dark:text-blue-400 text-xs">${stu.student_id}</td>
                    <td class="py-3.5 px-4 font-bold text-gray-800 dark:text-white">${stu.name}</td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300 text-[10px] font-bold">${stu.class_name || '-'}</span>
                    </td>
                    <td class="py-3.5 px-4 text-center text-[11px] text-gray-400">${stu.created_at || '-'}</td>
                    <td class="py-3.5 px-4 text-center">
                        <button onclick="deleteMember('${stu.student_id}', '${clubId}', '${stu.name.replace(/'/g, "\\'")}')" 
                                class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/20 text-rose-600 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all active:scale-95 mx-auto" title="ลบออกจากชุมนุม">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            
            loading.classList.add('hidden');
            tableContainer.classList.remove('hidden');
        } else {
            loading.classList.add('hidden');
            emptyState.classList.remove('hidden');
        }
    } catch (error) {
        console.error(error);
        loading.classList.add('hidden');
        emptyState.classList.remove('hidden');
    }
}

function closeMembersModal() {
    clearStudentSearch();
    document.getElementById('members-modal').classList.add('hidden');
}

function deleteMember(studentId, clubId, studentName) {
    Swal.fire({
        title: 'ยืนยันการลบนักเรียน?',
        text: `คุณต้องการลบ ${studentName} ออกจากชุมนุมใช่หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'ใช่, ลบออก',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            popup: 'rounded-3xl dark:bg-slate-900',
            title: 'font-black text-gray-800 dark:text-white',
            htmlContainer: 'font-bold text-gray-600 dark:text-gray-400'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../controllers/ClubController.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'delete_member',
                    student_id: studentId,
                    club_id: clubId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'ลบสำเร็จ!', showConfirmButton: false, timer: 1200, customClass: { popup: 'rounded-3xl dark:bg-slate-900' } });
                    // Refresh members list and main stats
                    manageMembers(clubId, document.getElementById('members-modal-title').textContent.replace('จัดการสมาชิก - ', ''), '', '');
                    loadData();
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: data.message, customClass: { popup: 'rounded-3xl dark:bg-slate-900' } });
                }
            });
        }
    });
}

// Student Search Autocomplete & Add Member Logic
let searchTimeout = null;

function clearStudentSearch() {
    const input = document.getElementById('add-student-search');
    const results = document.getElementById('autocomplete-results');
    const clearBtn = document.getElementById('btn-clear-search');
    const spinner = document.getElementById('search-spinner');
    
    if (input) input.value = '';
    if (results) {
        results.innerHTML = '';
        results.classList.add('hidden');
    }
    if (clearBtn) clearBtn.classList.add('hidden');
    if (spinner) spinner.classList.add('hidden');
}

function initStudentAutocomplete() {
    const input = document.getElementById('add-student-search');
    const results = document.getElementById('autocomplete-results');
    const clearBtn = document.getElementById('btn-clear-search');
    const spinner = document.getElementById('search-spinner');
    
    if (!input) return;

    input.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(searchTimeout);

        if (query.length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
            results.classList.add('hidden');
            results.innerHTML = '';
            spinner.classList.add('hidden');
            return;
        }

        spinner.classList.remove('hidden');

        searchTimeout = setTimeout(async () => {
            try {
                const res = await fetch(`../controllers/ClubController.php?action=search_students&q=${encodeURIComponent(query)}`);
                const data = await res.json();
                spinner.classList.add('hidden');

                if (data.success && data.students && data.students.length > 0) {
                    results.innerHTML = data.students.map(s => {
                        const isInCurrent = s.registered_club_id == currentClubId;
                        const isInOther = s.registered_club_id && s.registered_club_id != currentClubId;
                        const escapedName = s.fullname.replace(/'/g, "\\'");
                        const escapedClub = (s.registered_club_name || '').replace(/'/g, "\\'");

                        let badgeHtml = '';
                        let actionBtnHtml = '';

                        if (isInCurrent) {
                            badgeHtml = `<span class="badge-status badge-current"><i class="fas fa-check-circle"></i> อยู่ในชุมนุมนี้แล้ว</span>`;
                            actionBtnHtml = `<span class="badge-status badge-member">เป็นสมาชิกแล้ว</span>`;
                        } else if (isInOther) {
                            badgeHtml = `<span class="badge-status badge-transfer"><i class="fas fa-exchange-alt"></i> อยู่ชุมนุม: ${s.registered_club_name}</span>`;
                            actionBtnHtml = `
                                <button onclick="addStudent('${s.student_id}', '${escapedName}', '${escapedClub}')" 
                                        class="btn-action-transfer">
                                    <i class="fas fa-exchange-alt"></i> ย้ายเข้า
                                </button>
                            `;
                        } else {
                            badgeHtml = `<span class="badge-status badge-new"><i class="fas fa-info-circle"></i> ยังไม่มีชุมนุม</span>`;
                            actionBtnHtml = `
                                <button onclick="addStudent('${s.student_id}', '${escapedName}', '')" 
                                        class="btn-action-add">
                                    <i class="fas fa-plus"></i> เพิ่มเข้า
                                </button>
                            `;
                        }

                        return `
                            <div class="autocomplete-item">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="avatar-badge">
                                        ${s.number ? s.number : s.student_id.slice(-2)}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-black text-gray-800 dark:text-white text-sm truncate">${s.fullname}</span>
                                            <span class="font-mono text-xs text-blue-600 dark:text-blue-400 font-bold">(${s.student_id})</span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-bold">${s.class_name}</span>
                                            ${badgeHtml}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    ${actionBtnHtml}
                                </div>
                            </div>
                        `;
                    }).join('');
                    results.classList.remove('hidden');
                } else {
                    results.innerHTML = `
                        <div class="p-6 text-center text-gray-400 dark:text-gray-500 font-bold text-sm">
                            <i class="fas fa-search mb-1 text-lg opacity-40"></i>
                            <p>ไม่พบข้อมูลนักเรียนที่ค้นหา</p>
                        </div>
                    `;
                    results.classList.remove('hidden');
                }
            } catch (err) {
                console.error(err);
                spinner.classList.add('hidden');
            }
        }, 250);
    });

    // Close autocomplete when clicking outside
    document.addEventListener('click', function(e) {
        const container = document.getElementById('autocomplete-container');
        if (container && !container.contains(e.target)) {
            results.classList.add('hidden');
        }
    });
}

function addStudent(studentId, fullname, currentRegisteredClub) {
    let confirmTitle = 'เพิ่มนักเรียนเข้าชุมนุม?';
    let confirmText = `ต้องการเพิ่ม ${fullname} เข้าชุมนุมนี้ใช่หรือไม่?`;
    let confirmBtnText = 'ใช่, เพิ่มเข้าชุมนุม';
    let confirmBtnColor = '#059669';

    if (currentRegisteredClub) {
        confirmTitle = 'ยืนยันการย้ายชุมนุม?';
        confirmText = `${fullname} ปัจจุบันสังกัด "${currentRegisteredClub}" คุณต้องการย้ายมายังชุมนุมนี้ใช่หรือไม่?`;
        confirmBtnText = 'ใช่, ย้ายชุมนุม';
        confirmBtnColor = '#f59e0b';
    }

    Swal.fire({
        title: confirmTitle,
        text: confirmText,
        icon: currentRegisteredClub ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: confirmBtnColor,
        confirmButtonText: confirmBtnText,
        cancelButtonText: 'ยกเลิก',
        customClass: {
            popup: 'rounded-3xl dark:bg-slate-900',
            title: 'font-black text-gray-800 dark:text-white',
            htmlContainer: 'font-bold text-gray-600 dark:text-gray-400'
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch('../controllers/ClubController.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'add_member',
                        student_id: studentId,
                        club_id: currentClubId
                    })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: data.message || 'เพิ่มสำเร็จ!',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: { popup: 'rounded-3xl dark:bg-slate-900' }
                    });
                    clearStudentSearch();
                    // Refresh modal list
                    const modalTitle = document.getElementById('members-modal-title').textContent.replace('จัดการสมาชิก - ', '');
                    manageMembers(currentClubId, modalTitle, '', '');
                    // Refresh main list stats
                    loadData();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถดำเนินการได้',
                        text: data.message || 'เกิดข้อผิดพลาด',
                        customClass: { popup: 'rounded-3xl dark:bg-slate-900' }
                    });
                }
            } catch (e) {
                console.error(e);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้',
                    customClass: { popup: 'rounded-3xl dark:bg-slate-900' }
                });
            }
        }
    });
}

// Close modal when clicking outside
document.getElementById('members-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMembersModal();
    }
});

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    loadData();
    initStudentAutocomplete();
    
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

/* Autocomplete Badges */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.2rem 0.65rem;
    border-radius: 9999px;
    font-size: 10px;
    font-weight: 800;
}
.badge-current {
    background-color: #dbeafe;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}
.dark .badge-current {
    background-color: rgba(30, 58, 138, 0.4);
    color: #93c5fd;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.badge-transfer {
    background-color: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}
.dark .badge-transfer {
    background-color: rgba(120, 53, 15, 0.4);
    color: #fcd34d;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.badge-new {
    background-color: #d1fae5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.dark .badge-new {
    background-color: rgba(6, 78, 59, 0.4);
    color: #6ee7b7;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.badge-member {
    background-color: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
    font-size: 11px;
}
.dark .badge-member {
    background-color: #1e293b;
    color: #94a3b8;
    border: 1px solid #334155;
}

/* Autocomplete Action Buttons */
.btn-action-add {
    background: linear-gradient(135deg, #059669, #047857);
    color: #ffffff !important;
    font-weight: 700;
    font-size: 12px;
    padding: 0.4rem 0.85rem;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
}
.btn-action-add:hover {
    background: linear-gradient(135deg, #047857, #065f46);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
}
.btn-action-add:active {
    transform: scale(0.95);
}

.btn-action-transfer {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #ffffff !important;
    font-weight: 700;
    font-size: 12px;
    padding: 0.4rem 0.85rem;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
}
.btn-action-transfer:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
}
.btn-action-transfer:active {
    transform: scale(0.95);
}

/* Autocomplete Item Container Hover in Dark/Light Mode */
.autocomplete-item {
    padding: 0.875rem;
    transition: background-color 0.15s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}
.autocomplete-item:hover {
    background-color: rgba(59, 130, 246, 0.08);
}
.dark .autocomplete-item:hover {
    background-color: rgba(51, 65, 85, 0.5);
}

.avatar-badge {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    background-color: #dbeafe;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 0.75rem;
    flex-shrink: 0;
}
.dark .avatar-badge {
    background-color: rgba(30, 58, 138, 0.4);
    color: #93c5fd;
}
</style>

<!-- Header Section -->
<div class="mb-6 animate__animated animate__fadeIn">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/30 shrink-0">
                <i class="fas fa-star text-xl"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl md:text-2xl font-black text-gray-800 dark:text-white">Best For Teen</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/50">
                        ปีการศึกษา <?= $current_year ?>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/50">
                        <?= htmlspecialchars($stu_grade) ?>
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">เลือกกิจกรรมพัฒนาศักยภาพผู้เรียน (เลือกได้ 1 กิจกรรม)</p>
            </div>
        </div>
    </div>
</div>

<!-- Registration Status Alert (Time Window) -->
<?php if ($message): ?>
<div class="glass rounded-3xl p-5 mb-6 border-l-4 <?= $registration_open ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/20' : (strpos($alert_class, 'yellow') !== false ? 'border-amber-500 bg-amber-50/60 dark:bg-amber-950/20' : 'border-rose-500 bg-rose-50/60 dark:bg-rose-950/20') ?> shadow-sm animate__animated animate__fadeIn">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 <?= $registration_open ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : (strpos($alert_class, 'yellow') !== false ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'bg-rose-500 text-white shadow-lg shadow-rose-500/20') ?>">
            <?php if ($registration_open): ?>
                <i class="fas fa-check-circle text-xl"></i>
            <?php elseif (strpos($alert_class, 'yellow') !== false): ?>
                <i class="fas fa-clock text-xl"></i>
            <?php else: ?>
                <i class="fas fa-times-circle text-xl"></i>
            <?php endif; ?>
        </div>
        <div class="flex-1">
            <h3 class="font-black text-sm <?= $registration_open ? 'text-emerald-800 dark:text-emerald-300' : (strpos($alert_class, 'yellow') !== false ? 'text-amber-800 dark:text-amber-300' : 'text-rose-800 dark:text-rose-300') ?>">
                <?= $registration_open ? 'เปิดรับสมัคร' : 'สถานะการรับสมัคร' ?>
            </h3>
            <p class="text-xs font-bold mt-0.5 <?= $registration_open ? 'text-emerald-700 dark:text-emerald-400' : (strpos($alert_class, 'yellow') !== false ? 'text-amber-700 dark:text-amber-400' : 'text-rose-700 dark:text-rose-400') ?>"><?= $message ?></p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Registration Status Box (Student's Choice) -->
<div id="status-box" class="mb-6"></div>

<!-- Search & Filter Bar -->
<div class="glass rounded-3xl p-4 md:p-5 mb-6 border border-white/50 dark:border-white/10 shadow-sm space-y-3">
    <div class="flex flex-col md:flex-row md:items-center gap-3">
        <!-- Search Input -->
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="activity-search" placeholder="ค้นหากิจกรรม (ชื่อ, ระดับชั้น, รายละเอียด)..." 
                   class="w-full pl-11 pr-10 py-3.5 rounded-2xl bg-white dark:bg-slate-900 border-2 border-gray-100 dark:border-slate-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition-all font-bold text-sm placeholder:text-gray-400 placeholder:font-normal shadow-sm">
            <button type="button" id="btn-clear-search" onclick="clearSearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>

        <!-- Grade Filter Chips -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 shrink-0">
            <button type="button" class="grade-filter-btn px-3.5 py-2.5 rounded-xl text-xs font-black bg-amber-500 text-white shadow-sm transition-all" data-grade="all">ทั้งหมด</button>
            <button type="button" class="grade-filter-btn px-3.5 py-2.5 rounded-xl text-xs font-black bg-white/70 dark:bg-slate-800/70 border border-gray-200/60 dark:border-slate-700/60 text-gray-600 dark:text-gray-300 hover:bg-amber-100 hover:text-amber-700 dark:hover:bg-slate-700 transition-all" data-grade="<?= htmlspecialchars($stu_grade) ?>">เฉพาะ <?= htmlspecialchars($stu_grade) ?></button>
        </div>
    </div>
</div>

<!-- Mobile Cards View -->
<div id="activity-cards" class="space-y-4 md:hidden">
    <div class="text-center py-16">
        <div class="w-12 h-12 border-4 border-amber-200 border-t-amber-600 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-gray-500 font-bold">กำลังโหลดกิจกรรม...</p>
    </div>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block glass rounded-3xl overflow-hidden mb-8 border border-white/40 dark:border-white/10 shadow-xl">
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-4 px-6 flex items-center justify-between text-white">
        <h3 class="text-base font-black flex items-center gap-2">
            <i class="fas fa-list"></i>
            <span>รายการกิจกรรม Best For Teen</span>
        </h3>
        <span class="text-xs font-bold opacity-90">ปีการศึกษา <?= $current_year ?></span>
    </div>
    <table id="best-table" class="w-full">
        <thead>
            <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-700 text-xs font-black text-gray-600 dark:text-gray-300">
                <th class="py-4 px-4 text-center w-16">#</th>
                <th class="py-4 px-6 text-left">ชื่อกิจกรรม</th>
                <th class="py-4 px-4 text-center">ระดับชั้นที่รับ</th>
                <th class="py-4 px-6 text-center w-48">สมาชิก / จำนวนที่รับ</th>
                <th class="py-4 px-6 text-center w-36">การสมัคร</th>
            </tr>
        </thead>
        <tbody id="tbody" class="divide-y divide-gray-100 dark:divide-slate-800/60">
            <tr>
                <td colspan="5" class="py-16 text-center text-gray-400 font-bold">
                    <div class="w-10 h-10 border-4 border-amber-200 border-t-amber-600 rounded-full animate-spin mx-auto mb-3"></div>
                    กำลังโหลดข้อมูล...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Note -->
<div class="glass rounded-2xl p-4 border-l-4 border-amber-500 flex items-center gap-3">
    <i class="fas fa-info-circle text-amber-500 text-lg"></i>
    <div class="text-xs font-bold text-gray-600 dark:text-gray-300">
        นักเรียนสามารถเลือกสมัครกิจกรรม Best For Teen ได้ <b>1 กิจกรรมต่อปีการศึกษา</b> และสามารถยกเลิก/เปลี่ยนกิจกรรมได้ตลอดช่วงเวลาที่เปิดรับสมัคร
    </div>
</div>

<script>
const registrationOpen = <?= $registration_open ? 'true' : 'false' ?>;
const myGrade = '<?= htmlspecialchars($stu_grade) ?>';
let activitiesData = [];
let statusData = { registered: false };
let selectedGradeFilter = 'all';

function isGradeAllowed(gradeLevelsStr) {
    if (!gradeLevelsStr) return false;
    const allowed = gradeLevelsStr.split(',').map(g => g.trim());
    return allowed.includes(myGrade) || allowed.includes('ทั้งหมด');
}

// Render Mobile Card
function renderActivityCard(a) {
    const current = parseInt(a.current_members_count || 0);
    const max = parseInt(a.max_members || 0);
    const percent = max > 0 ? Math.round((current / max) * 100) : 0;
    const isFull = percent >= 100;
    const allowed = isGradeAllowed(a.grade_levels);
    const isMyActivity = statusData.registered && statusData.data && parseInt(statusData.data.activity_id) === parseInt(a.id);
    
    const grades = (a.grade_levels || '').split(',').map(g => {
        const isMine = g.trim() === myGrade;
        return `<span class="px-2 py-0.5 rounded-lg text-xs font-black ${isMine ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300'}">${g.trim()}</span>`;
    }).join(' ');

    let btnClass = 'w-full py-3 rounded-xl font-black text-white transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-sm';
    let btnText = 'สมัครกิจกรรมนี้';
    let btnIcon = 'fa-check-circle';
    let disabled = false;

    if (isMyActivity) {
        btnClass += ' bg-emerald-500 shadow-md shadow-emerald-500/20';
        btnText = 'กิจกรรมที่คุณเลือก ✓';
        btnIcon = 'fa-check';
        disabled = true;
    } else if (!allowed) {
        btnClass += ' bg-gray-300 dark:bg-slate-700 text-gray-500 dark:text-gray-400 cursor-not-allowed';
        btnText = 'ไม่ตรงระดับชั้น (' + myGrade + ')';
        btnIcon = 'fa-ban';
        disabled = true;
    } else if (!registrationOpen) {
        btnClass += ' bg-gray-400 dark:bg-slate-700 cursor-not-allowed';
        btnText = 'ปิดรับสมัคร';
        btnIcon = 'fa-clock';
        disabled = true;
    } else if (isFull) {
        btnClass += ' bg-rose-500 cursor-not-allowed';
        btnText = 'กิจกรรมเต็มแล้ว';
        btnIcon = 'fa-lock';
        disabled = true;
    } else if (statusData.registered) {
        btnClass += ' bg-gray-300 dark:bg-slate-700 text-gray-500 dark:text-gray-400 cursor-not-allowed';
        btnText = 'สมัครกิจกรรมอื่นแล้ว';
        btnIcon = 'fa-ban';
        disabled = true;
    } else {
        btnClass += ' bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg shadow-amber-500/30';
    }

    return `
        <div class="activity-card glass rounded-3xl shadow-lg overflow-hidden border ${isMyActivity ? 'border-emerald-500 dark:border-emerald-500 ring-2 ring-emerald-500/30' : 'border-white/40 dark:border-white/10'}" 
             data-name="${(a.name || '').toLowerCase()}" 
             data-grades="${(a.grade_levels || '').toLowerCase()}"
             data-allowed="${allowed ? '1' : '0'}">
            <div class="p-5 pb-3">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br ${isMyActivity ? 'from-emerald-500 to-teal-600' : (isFull ? 'from-rose-500 to-red-600' : 'from-amber-500 to-orange-600')} flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas ${isMyActivity ? 'fa-check' : (isFull ? 'fa-lock' : 'fa-star')} text-lg"></i>
                    </div>
                    <div class="flex-1 w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-gray-800 dark:text-white text-base leading-tight truncate">${a.name || ''}</h3>
                            ${isFull ? '<span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black shrink-0">เต็ม</span>' : ''}
                        </div>
                        ${a.description ? `<p class="text-xs text-gray-400 dark:text-gray-500 line-clamp-1 mt-0.5">${a.description}</p>` : ''}
                        <div class="flex gap-1 mt-2 flex-wrap">
                            ${grades}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="mx-5 mb-4 p-3 rounded-2xl bg-black/5 dark:bg-white/5">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase">ผู้สมัคร</span>
                    <span class="text-xs font-black ${isFull ? 'text-rose-500' : 'text-amber-600 dark:text-amber-400'}">${current} / ${max} คน (${percent}%)</span>
                </div>
                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-1000 ${isFull ? 'bg-rose-500' : 'bg-gradient-to-r from-amber-500 to-orange-500'}" style="width: ${percent}%"></div>
                </div>
            </div>
            
            <!-- Apply Button -->
            <div class="px-5 py-3.5 bg-black/[0.02] dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800">
                <button class="apply-btn ${btnClass}" data-id="${a.id}" data-name="${a.name}" ${disabled ? 'disabled' : ''}>
                    <i class="fas ${btnIcon}"></i>
                    <span>${btnText}</span>
                </button>
            </div>
        </div>`;
}

// Render Desktop Table
function renderDesktopTable(data) {
    const tbody = document.getElementById('tbody');
    if (!tbody) return;

    if (!data || data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="py-16 text-center text-gray-400 font-bold">
                    <i class="fas fa-folder-open text-3xl mb-3 block text-gray-300 dark:text-gray-600"></i>
                    ไม่พบกิจกรรมที่เปิดรับสมัคร
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = data.map((a, idx) => {
        const current = parseInt(a.current_members_count || 0);
        const max = parseInt(a.max_members || 0);
        const percent = max > 0 ? Math.round((current / max) * 100) : 0;
        const isFull = percent >= 100;
        const allowed = isGradeAllowed(a.grade_levels);
        const isMyActivity = statusData.registered && statusData.data && parseInt(statusData.data.activity_id) === parseInt(a.id);
        
        const grades = (a.grade_levels || '').split(',').map(g => {
            const isMine = g.trim() === myGrade;
            return `<span class="px-2 py-0.5 rounded-lg text-xs font-black ${isMine ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300'}">${g.trim()}</span>`;
        }).join(' ');

        let btnClass = 'px-4 py-2.5 rounded-xl font-black text-xs transition-all active:scale-95 flex items-center justify-center gap-1.5 w-full';
        let btnText = 'สมัคร';
        let btnIcon = 'fa-check';
        let disabled = false;

        if (isMyActivity) {
            btnClass += ' bg-emerald-500 text-white shadow-sm shadow-emerald-500/20';
            btnText = 'เลือกแล้ว ✓';
            disabled = true;
        } else if (!allowed) {
            btnClass += ' bg-gray-200 dark:bg-slate-700 text-gray-400 cursor-not-allowed';
            btnText = 'ไม่ตรงระดับชั้น';
            btnIcon = 'fa-ban';
            disabled = true;
        } else if (!registrationOpen) {
            btnClass += ' bg-gray-300 dark:bg-slate-700 text-gray-500 cursor-not-allowed';
            btnText = 'ปิดรับสมัคร';
            btnIcon = 'fa-clock';
            disabled = true;
        } else if (isFull) {
            btnClass += ' bg-rose-500/20 text-rose-500 cursor-not-allowed';
            btnText = 'เต็มแล้ว';
            btnIcon = 'fa-lock';
            disabled = true;
        } else if (statusData.registered) {
            btnClass += ' bg-gray-200 dark:bg-slate-700 text-gray-400 cursor-not-allowed';
            btnText = 'สมัครแล้ว';
            btnIcon = 'fa-ban';
            disabled = true;
        } else {
            btnClass += ' bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-500/20';
        }

        return `
            <tr class="activity-row hover:bg-amber-50/40 dark:hover:bg-slate-700/40 transition-colors ${isMyActivity ? 'bg-emerald-50/30 dark:bg-emerald-950/20' : ''}" 
                data-name="${(a.name || '').toLowerCase()}"
                data-grades="${(a.grade_levels || '').toLowerCase()}"
                data-allowed="${allowed ? '1' : '0'}">
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
                            <div class="h-full rounded-full transition-all duration-1000 ${isFull ? 'bg-rose-500' : 'bg-gradient-to-r from-amber-500 to-orange-500'}" style="width: ${percent}%"></div>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6 text-center">
                    <button class="apply-btn ${btnClass}" data-id="${a.id}" data-name="${a.name}" ${disabled ? 'disabled' : ''}>
                        <i class="fas ${btnIcon}"></i>
                        <span>${btnText}</span>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

// Render All Mobile Cards
function renderMobileCards(data) {
    const container = document.getElementById('activity-cards');
    if (!container) return;
    
    if (!data || data.length === 0) {
        container.innerHTML = `
            <div class="text-center py-16 glass rounded-3xl border border-white/40 dark:border-white/10">
                <i class="fas fa-folder-open text-gray-300 dark:text-gray-600 text-3xl mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400 font-bold">ไม่พบกิจกรรมที่เปิดรับสมัคร</p>
            </div>`;
        return;
    }

    container.innerHTML = data.map(a => renderActivityCard(a)).join('');
}

// Render Status Box
function renderStatus(data) {
    const box = document.getElementById('status-box');
    if (!box) return;

    if (data.registered && data.data) {
        const act = data.data;
        const regDate = act.created_at ? new Date(act.created_at).toLocaleString('th-TH') : '';
        box.innerHTML = `
            <div class="glass rounded-3xl p-6 border-l-4 border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/20 shadow-md animate__animated animate__fadeIn">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 shrink-0">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase">ลงทะเบียนเรียบร้อย</span>
                                ${regDate ? `<span class="text-xs text-gray-400"><i class="far fa-clock mr-1"></i>${regDate}</span>` : ''}
                            </div>
                            <h4 class="font-black text-gray-800 dark:text-white text-xl mt-1">${act.name || 'กิจกรรม Best'}</h4>
                            ${act.description ? `<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${act.description}</p>` : ''}
                        </div>
                    </div>
                    
                    ${registrationOpen ? `
                    <button onclick="cancelRegistration()" class="px-5 py-3 rounded-2xl bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white font-black text-xs transition-all border border-rose-200 dark:border-rose-800 active:scale-95 shrink-0 flex items-center gap-2 justify-center">
                        <i class="fas fa-times"></i>
                        <span>ยกเลิกการสมัคร</span>
                    </button>
                    ` : ''}
                </div>
            </div>`;
    } else {
        box.innerHTML = `
            <div class="glass rounded-3xl p-5 border-l-4 border-amber-500 bg-amber-50/60 dark:bg-amber-950/20 shadow-sm animate__animated animate__fadeIn">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/20 shrink-0">
                        <i class="fas fa-hand-pointer text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-amber-800 dark:text-amber-300 text-base">เลือกกิจกรรมที่สนใจ</h4>
                        <p class="text-amber-700 dark:text-amber-400 text-xs mt-0.5">คุณยังไม่ได้ลงทะเบียนกิจกรรม Best For Teen สำหรับปีการศึกษานี้ (เลือกได้ 1 กิจกรรม)</p>
                    </div>
                </div>
            </div>`;
    }
}

function clearSearch() {
    const el = document.getElementById('activity-search');
    el.value = '';
    document.getElementById('btn-clear-search').classList.add('hidden');
    applyFilters();
}

function applyFilters() {
    const q = document.getElementById('activity-search').value.trim().toLowerCase();
    const clearBtn = document.getElementById('btn-clear-search');
    if (clearBtn) {
        if (q) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }

    const matchFn = (name, grades, isAllowed) => {
        const matchesQ = !q || name.includes(q) || grades.includes(q);
        const matchesGrade = (selectedGradeFilter === 'all') || (isAllowed === '1');
        return matchesQ && matchesGrade;
    };

    document.querySelectorAll('.activity-card').forEach(c => {
        const name = c.dataset.name || '';
        const grades = c.dataset.grades || '';
        const allowed = c.dataset.allowed || '0';
        c.style.display = matchFn(name, grades, allowed) ? '' : 'none';
    });

    document.querySelectorAll('.activity-row').forEach(r => {
        const name = r.dataset.name || '';
        const grades = r.dataset.grades || '';
        const allowed = r.dataset.allowed || '0';
        r.style.display = matchFn(name, grades, allowed) ? '' : 'none';
    });
}

// Load Data
async function loadData() {
    try {
        const [statusRes, listRes] = await Promise.all([
            fetch('../controllers/BestActivityController.php?action=my_status'),
            fetch('../controllers/BestActivityController.php?action=list')
        ]);
        
        const status = await statusRes.json();
        const list = await listRes.json();
        
        if (status.success) {
            statusData = status;
            renderStatus(status);
        }
        
        if (list.success) {
            activitiesData = list.data || [];
            renderDesktopTable(activitiesData);
            renderMobileCards(activitiesData);
            applyFilters();
        }
    } catch (error) {
        console.error('Error loading data:', error);
        document.getElementById('activity-cards').innerHTML = `
            <div class="text-center py-16 glass rounded-3xl">
                <i class="fas fa-exclamation-triangle text-amber-500 text-3xl mb-4"></i>
                <p class="text-gray-500 font-bold">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>
                <button onclick="loadData()" class="mt-4 px-5 py-2.5 bg-amber-500 text-white rounded-xl font-bold shadow-md">ลองใหม่อีกครั้ง</button>
            </div>`;
    }
}

// Cancel Registration
async function cancelRegistration() {
    const actName = statusData.data ? statusData.data.name : '';
    Swal.fire({
        title: 'ยืนยันการยกเลิก?',
        text: `คุณต้องการยกเลิกการสมัครกิจกรรม "${actName}" หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f43f5e',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash mr-1"></i> ยืนยันยกเลิก',
        cancelButtonText: 'ปิด'
    }).then(async (res) => {
        if (res.isConfirmed) {
            Swal.fire({ title: 'กำลังยกเลิก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            try {
                const fd = new FormData();
                fd.append('action', 'cancel');
                const response = await fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd });
                const result = await response.json();
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'ยกเลิกสำเร็จ', text: result.message, timer: 2000, showConfirmButton: false });
                    loadData();
                } else {
                    Swal.fire({ icon: 'error', title: 'ไม่สามารถยกเลิกได้', text: result.message });
                }
            } catch(e) {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้' });
            }
        }
    });
}

// Search input
document.getElementById('activity-search').addEventListener('input', applyFilters);

// Grade filter buttons
document.querySelectorAll('.grade-filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        selectedGradeFilter = this.dataset.grade;
        document.querySelectorAll('.grade-filter-btn').forEach(b => {
            b.className = 'grade-filter-btn px-3.5 py-2.5 rounded-xl text-xs font-black bg-white/70 dark:bg-slate-800/70 border border-gray-200/60 dark:border-slate-700/60 text-gray-600 dark:text-gray-300 hover:bg-amber-100 hover:text-amber-700 dark:hover:bg-slate-700 transition-all';
        });
        this.className = 'grade-filter-btn px-3.5 py-2.5 rounded-xl text-xs font-black bg-amber-500 text-white shadow-sm transition-all';
        applyFilters();
    });
});

// Apply Button Handler
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.apply-btn');
    if (!btn || btn.disabled) return;
    
    const activityId = btn.dataset.id;
    const actName = btn.dataset.name || 'กิจกรรมนี้';
    
    Swal.fire({
        title: 'ยืนยันการสมัคร',
        text: `คุณต้องการสมัคร "${actName}" หรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        confirmButtonText: '<i class="fas fa-check mr-1"></i> สมัครเลย',
        cancelButtonText: 'ยกเลิก'
    }).then(async (result) => {
        if (result.isConfirmed) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> กำลังสมัคร...';
            
            try {
                const fd = new FormData();
                fd.append('action', 'register');
                fd.append('activity_id', activityId);
                
                const res = await fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สมัครสำเร็จ! 🎉',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    loadData();
                } else {
                    Swal.fire({ icon: 'error', title: 'ไม่สามารถสมัครได้', text: data.message });
                    loadData();
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้' });
                loadData();
            }
        }
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', loadData);
</script>

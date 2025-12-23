<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
            <i class="fas fa-star text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-black text-gray-800 dark:text-white">Best For Teen 2025</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">เลือกกิจกรรมพัฒนาศักยภาพ</p>
        </div>
    </div>
</div>

<!-- Registration Status Alert -->
<?php if ($message): ?>
<div class="glass rounded-2xl p-5 mb-6 border-l-4 <?= $registration_open ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : (strpos($alert_class, 'yellow') !== false ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-rose-500 bg-rose-50 dark:bg-rose-900/20') ?>">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 <?= $registration_open ? 'bg-emerald-200 dark:bg-emerald-800' : (strpos($alert_class, 'yellow') !== false ? 'bg-amber-200 dark:bg-amber-800' : 'bg-rose-200 dark:bg-rose-800') ?>">
            <?php if ($registration_open): ?>
                <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400 text-xl"></i>
            <?php elseif (strpos($alert_class, 'yellow') !== false): ?>
                <i class="fas fa-clock text-amber-600 dark:text-amber-400 text-xl"></i>
            <?php else: ?>
                <i class="fas fa-times-circle text-rose-600 dark:text-rose-400 text-xl"></i>
            <?php endif; ?>
        </div>
        <p class="font-bold <?= $registration_open ? 'text-emerald-700 dark:text-emerald-300' : (strpos($alert_class, 'yellow') !== false ? 'text-amber-700 dark:text-amber-300' : 'text-rose-700 dark:text-rose-300') ?>"><?= $message ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Status Box -->
<div id="status-box" class="mb-6"></div>

<!-- Search Box -->
<div class="glass rounded-2xl p-4 mb-6">
    <div class="relative">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-amber-400"></i>
        <input type="text" id="activity-search" placeholder="ค้นหากิจกรรม..." 
               class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white dark:bg-slate-800 border-2 border-amber-100 dark:border-amber-900/50 text-gray-700 dark:text-gray-200 focus:outline-none focus:border-amber-400 transition-all font-bold placeholder:text-gray-400 placeholder:font-normal">
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
<div class="hidden md:block glass rounded-2xl overflow-hidden">
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-4">
        <h3 class="text-lg font-black text-white flex items-center gap-2">
            <i class="fas fa-list"></i>
            รายการกิจกรรมที่เปิดรับสมัคร
        </h3>
    </div>
    <table id="best-table" class="w-full">
        <thead>
            <tr class="bg-gray-50 dark:bg-slate-800">
                <th class="py-4 px-4 text-center font-black text-gray-700 dark:text-gray-300 w-16">#</th>
                <th class="py-4 px-4 text-left font-black text-gray-700 dark:text-gray-300">ชื่อกิจกรรม</th>
                <th class="py-4 px-4 text-center font-black text-gray-700 dark:text-gray-300">ระดับชั้น</th>
                <th class="py-4 px-4 text-center font-black text-gray-700 dark:text-gray-300">จำนวนที่รับ</th>
                <th class="py-4 px-4 text-center font-black text-gray-700 dark:text-gray-300 w-32">สมัคร</th>
            </tr>
        </thead>
        <tbody id="tbody" class="divide-y divide-gray-100 dark:divide-gray-800"></tbody>
    </table>
</div>

<!-- Note -->
<div class="mt-6 glass rounded-2xl p-4 border-l-4 border-amber-500">
    <p class="text-amber-700 dark:text-amber-300 font-bold flex items-center gap-2 text-sm">
        <i class="fas fa-info-circle text-amber-500"></i>
        เลือกได้เพียง 1 กิจกรรมต่อปีการศึกษา
    </p>
</div>

<script>
const registrationOpen = <?= $registration_open ? 'true' : 'false' ?>;
let activitiesData = [];
let statusData = { registered: false };

// Render Mobile Card
function renderActivityCard(a) {
    const current = parseInt(a.current_members_count || 0);
    const max = parseInt(a.max_members || 0);
    const percent = max > 0 ? Math.round((current / max) * 100) : 0;
    const isFull = percent >= 100;
    const disabled = isFull || statusData.registered || !registrationOpen;
    
    const grades = (a.grade_levels || '').split(',').map(g => 
        `<span class="px-2 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-300 text-xs font-black">${g.trim()}</span>`
    ).join(' ');

    let btnClass = 'w-full py-3 rounded-xl font-black text-white transition-all active:scale-[0.98] flex items-center justify-center gap-2';
    let btnText = 'สมัครกิจกรรมนี้';
    let btnIcon = 'fa-check-circle';
    
    if (!registrationOpen) {
        btnClass += ' bg-gray-400 cursor-not-allowed';
        btnText = 'ปิดรับสมัคร';
        btnIcon = 'fa-ban';
    } else if (isFull) {
        btnClass += ' bg-rose-500 cursor-not-allowed';
        btnText = 'เต็มแล้ว';
        btnIcon = 'fa-lock';
    } else if (statusData.registered) {
        btnClass += ' bg-emerald-500 cursor-not-allowed';
        btnText = 'สมัครแล้ว';
        btnIcon = 'fa-check';
    } else {
        btnClass += ' bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg shadow-amber-500/30';
    }

    return `
        <div class="activity-card bg-white dark:bg-slate-800 rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/20 overflow-hidden border border-gray-100 dark:border-gray-700" data-name="${(a.name || '').toLowerCase()}">
            <div class="p-4 pb-3 overflow-hidden">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br ${isFull ? 'from-rose-500 to-red-600' : 'from-amber-500 to-orange-600'} flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas ${isFull ? 'fa-lock' : 'fa-star'} text-lg"></i>
                    </div>
                    <div class="flex-1 w-0">
                        <h3 class="font-black text-gray-800 dark:text-white text-base leading-tight whitespace-nowrap overflow-hidden text-ellipsis">${a.name || ''}</h3>
                        <div class="flex gap-1 mt-2 flex-wrap">
                            ${grades}
                        </div>
                    </div>
                    ${isFull ? '<span class="flex-shrink-0 px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black">เต็ม</span>' : ''}
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="mx-4 mb-4 p-3 rounded-xl bg-gray-50 dark:bg-slate-900/50">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-black text-gray-500 dark:text-gray-400 uppercase">ผู้สมัคร</span>
                    <span class="text-sm font-black ${isFull ? 'text-rose-500' : 'text-amber-600'}">${current} / ${max} คน</span>
                </div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all ${isFull ? 'bg-gradient-to-r from-rose-500 to-red-500' : 'bg-gradient-to-r from-amber-500 to-orange-500'}" style="width: ${percent}%"></div>
                </div>
            </div>
            
            <!-- Apply Button -->
            <div class="px-4 py-3 bg-gray-50 dark:bg-slate-900/30 border-t border-gray-100 dark:border-gray-700">
                <button class="apply-btn ${btnClass}" data-id="${a.id}" ${disabled ? 'disabled' : ''}>
                    <i class="fas ${btnIcon}"></i>
                    ${btnText}
                </button>
            </div>
        </div>`;
}

// Render All Mobile Cards
function renderMobileCards(data) {
    const container = document.getElementById('activity-cards');
    
    if (!data || data.length === 0) {
        container.innerHTML = `
            <div class="text-center py-16 glass rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-folder-open text-gray-300 dark:text-gray-500 text-2xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-bold">ไม่พบกิจกรรม</p>
            </div>`;
        return;
    }

    container.innerHTML = data.map(a => renderActivityCard(a)).join('');
}

// Render Status Box
function renderStatus(data) {
    const box = document.getElementById('status-box');
    if (data.registered) {
        box.innerHTML = `
            <div class="glass rounded-2xl p-5 border-l-4 border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-200 dark:bg-emerald-800 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-emerald-700 dark:text-emerald-300 text-lg">สมัครสำเร็จแล้ว! 🎉</h4>
                        <p class="text-emerald-600 dark:text-emerald-400 text-sm">คุณได้สมัครกิจกรรม <b>${data.data?.name || ''}</b> แล้ว</p>
                    </div>
                </div>
            </div>`;
    } else {
        box.innerHTML = `
            <div class="glass rounded-2xl p-5 border-l-4 border-amber-500 bg-amber-50 dark:bg-amber-900/20">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-200 dark:bg-amber-800 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-hand-pointer text-amber-600 dark:text-amber-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-amber-700 dark:text-amber-300 text-lg">เลือกกิจกรรมที่สนใจ</h4>
                        <p class="text-amber-600 dark:text-amber-400 text-sm">คุณยังไม่ได้สมัครกิจกรรม Best (เลือกได้ 1 รายการ)</p>
                    </div>
                </div>
            </div>`;
    }
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
            renderMobileCards(activitiesData);
        }
    } catch (error) {
        console.error('Error loading data:', error);
        document.getElementById('activity-cards').innerHTML = `
            <div class="text-center py-16 glass rounded-2xl">
                <i class="fas fa-exclamation-triangle text-amber-500 text-3xl mb-4"></i>
                <p class="text-gray-500 font-bold">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>
                <button onclick="loadData()" class="mt-4 px-4 py-2 bg-amber-500 text-white rounded-xl font-bold">ลองใหม่</button>
            </div>`;
    }
}

// Search
document.getElementById('activity-search').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.activity-card').forEach(card => {
        card.style.display = card.dataset.name.includes(query) ? '' : 'none';
    });
});

// Apply Button Handler
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.apply-btn');
    if (!btn || btn.disabled) return;
    
    const activityId = btn.dataset.id;
    
    Swal.fire({
        title: 'ยืนยันการสมัคร',
        text: 'คุณต้องการสมัครกิจกรรมนี้หรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        confirmButtonText: '<i class="fas fa-check mr-2"></i>สมัครเลย',
        cancelButtonText: 'ยกเลิก'
    }).then(async (result) => {
        if (result.isConfirmed) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังสมัคร...';
            
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
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: data.message });
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle"></i> สมัครกิจกรรมนี้';
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้' });
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> สมัครกิจกรรมนี้';
            }
        }
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', loadData);
</script>

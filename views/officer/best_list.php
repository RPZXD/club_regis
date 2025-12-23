<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                <i class="fas fa-star text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-gray-800 dark:text-white">Best For Teen 2025</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">จัดการกิจกรรม Best</p>
            </div>
        </div>
        <button id="btn-new" class="hidden md:flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-5 py-3 rounded-xl shadow-lg transition-all active:scale-95">
            <i class="fas fa-plus"></i>
            <span>เพิ่มกิจกรรม</span>
        </button>
    </div>
    <!-- Mobile Add Button -->
    <button id="btn-new-mobile" class="md:hidden w-full mt-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
        <i class="fas fa-plus"></i>
        <span>เพิ่มกิจกรรม</span>
    </button>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="glass rounded-2xl p-4">
        <div class="text-[10px] font-bold text-blue-500 uppercase">จำนวนกิจกรรม</div>
        <div id="card-activities" class="text-2xl font-black text-gray-800 dark:text-white">0</div>
    </div>
    <div class="glass rounded-2xl p-4">
        <div class="text-[10px] font-bold text-emerald-500 uppercase">ที่รับทั้งหมด</div>
        <div id="card-capacity" class="text-2xl font-black text-gray-800 dark:text-white">0</div>
    </div>
    <div class="glass rounded-2xl p-4">
        <div class="text-[10px] font-bold text-violet-500 uppercase">สมัครแล้ว</div>
        <div id="card-registered" class="text-2xl font-black text-gray-800 dark:text-white">0</div>
    </div>
    <div class="glass rounded-2xl p-4">
        <div class="text-[10px] font-bold text-amber-500 uppercase">อัตราการเต็ม</div>
        <div id="card-fill" class="text-2xl font-black text-gray-800 dark:text-white">0%</div>
    </div>
</div>

<!-- Chart -->
<div class="glass rounded-2xl p-4 mb-6">
    <canvas id="best-chart" height="150"></canvas>
</div>

<!-- Search -->
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
    <table id="best-table" class="w-full">
        <thead>
            <tr class="bg-gradient-to-r from-amber-500 to-orange-600 text-white">
                <th class="py-4 px-4 text-center font-black w-16">#</th>
                <th class="py-4 px-4 text-left font-black">ชื่อกิจกรรม</th>
                <th class="py-4 px-4 text-center font-black">ระดับชั้น</th>
                <th class="py-4 px-4 text-center font-black">สมาชิก</th>
                <th class="py-4 px-4 text-center font-black w-32">จัดการ</th>
            </tr>
        </thead>
        <tbody id="best-body" class="divide-y divide-gray-100 dark:divide-gray-800"></tbody>
    </table>
</div>

<!-- Modal Create/Edit -->
<div id="modal" class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm hidden p-0 md:p-4">
    <div class="bg-white dark:bg-slate-900 w-full md:max-w-lg md:rounded-3xl rounded-t-3xl shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-5 flex justify-between items-center text-white z-10">
            <h3 id="modal-title" class="text-xl font-black">เพิ่มกิจกรรม</h3>
            <button id="btn-cancel" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-all">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form id="best-form" class="p-6 space-y-5">
            <input type="hidden" id="activity_id">
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-2 text-sm">ชื่อกิจกรรม</label>
                <input type="text" id="name" required class="w-full bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3.5 focus:border-amber-500 focus:outline-none transition-all font-bold dark:text-white">
            </div>
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-2 text-sm">รายละเอียด</label>
                <textarea id="description" rows="3" class="w-full bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3.5 focus:border-amber-500 focus:outline-none transition-all dark:text-white"></textarea>
            </div>
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-3 text-sm">ระดับชั้น</label>
                <div class="grid grid-cols-3 gap-2">
                    <?php foreach(['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'] as $g): ?>
                    <label class="relative cursor-pointer">
                        <input type="checkbox" class="grade-opt peer sr-only" value="<?= $g ?>">
                        <div class="w-full py-3 text-center rounded-xl border-2 border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 font-black text-gray-500 dark:text-gray-400 transition-all peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white active:scale-95">
                            <?= $g ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-2 text-sm">จำนวนที่รับ</label>
                <input type="number" id="max_members" min="1" required class="w-full bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3.5 focus:border-amber-500 focus:outline-none transition-all font-bold dark:text-white text-center text-xl">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" id="btn-cancel-2" class="flex-1 py-4 rounded-xl font-black text-gray-500 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 transition-all active:scale-[0.98]">
                    ยกเลิก
                </button>
                <button type="submit" class="flex-1 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-[0.98]">
                    บันทึก
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Members Modal -->
<div id="members-modal" class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm hidden p-0 md:p-4">
    <div class="bg-white dark:bg-slate-900 w-full md:max-w-2xl md:rounded-3xl rounded-t-3xl shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-5 flex justify-between items-center text-white z-10">
            <h3 class="text-xl font-black">จัดการสมาชิก</h3>
            <button id="btn-close-members" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-all">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="p-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex gap-2">
                <input type="text" id="member-student-id" placeholder="รหัสนักเรียน" class="flex-1 bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 font-bold dark:text-white">
                <button id="btn-add-member" class="px-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition-all active:scale-95">
                    <i class="fas fa-plus mr-1"></i> เพิ่ม
                </button>
            </div>
        </div>
        <div id="members-list" class="p-4 space-y-2"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let activitiesData = [];
let currentActivityId = null;
let bestChart = null;

function showToast(msg, type = 'info') {
    Swal.fire({ toast: true, position: 'top-end', icon: type, title: msg, showConfirmButton: false, timer: 2000 });
}

function renderCard(a) {
    const current = parseInt(a.current_members_count || 0);
    const max = parseInt(a.max_members || 0);
    const percent = max > 0 ? Math.round((current / max) * 100) : 0;
    const isFull = percent >= 100;
    
    const grades = (a.grade_levels || '').split(',').map(g => 
        `<span class="px-2 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-300 text-xs font-black">${g.trim()}</span>`
    ).join(' ');

    return `
        <div class="activity-card bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700" data-name="${(a.name || '').toLowerCase()}" data-id="${a.id}">
            <div class="p-4 pb-3">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br ${isFull ? 'from-rose-500 to-red-600' : 'from-amber-500 to-orange-600'} flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas ${isFull ? 'fa-lock' : 'fa-star'} text-lg"></i>
                    </div>
                    <div class="flex-1 w-0">
                        <h3 class="font-black text-gray-800 dark:text-white text-base whitespace-nowrap overflow-hidden text-ellipsis">${a.name}</h3>
                        <div class="flex gap-1 mt-2 flex-wrap">${grades}</div>
                    </div>
                    ${isFull ? '<span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black">เต็ม</span>' : ''}
                </div>
            </div>
            <div class="mx-4 mb-4 p-3 rounded-xl bg-gray-50 dark:bg-slate-900/50">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-black text-gray-500 uppercase">สมาชิก</span>
                    <span class="text-sm font-black ${isFull ? 'text-rose-500' : 'text-amber-600'}">${current} / ${max}</span>
                </div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full ${isFull ? 'bg-gradient-to-r from-rose-500 to-red-500' : 'bg-gradient-to-r from-amber-500 to-orange-500'}" style="width: ${percent}%"></div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 dark:bg-slate-900/30 border-t border-gray-100 dark:border-gray-700 flex flex-wrap gap-2">
                <button class="edit-btn flex-1 px-3 py-2 rounded-xl bg-indigo-500 text-white font-bold text-sm active:scale-95 transition-all" data-id="${a.id}">
                    <i class="fas fa-edit mr-1"></i> แก้ไข
                </button>
                <button class="members-btn flex-1 px-3 py-2 rounded-xl bg-blue-500 text-white font-bold text-sm active:scale-95 transition-all" data-id="${a.id}">
                    <i class="fas fa-users mr-1"></i> สมาชิก
                </button>
                <a href="print_best.php?id=${a.id}" target="_blank" class="flex-1 px-3 py-2 rounded-xl bg-emerald-500 text-white font-bold text-sm text-center active:scale-95 transition-all">
                    <i class="fas fa-print mr-1"></i> พิมพ์
                </a>
                <button class="delete-btn px-3 py-2 rounded-xl bg-rose-500 text-white font-bold text-sm active:scale-95 transition-all" data-id="${a.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>`;
}

function renderMobileCards(data) {
    const container = document.getElementById('activity-cards');
    if (!data || data.length === 0) {
        container.innerHTML = '<div class="text-center py-16 glass rounded-2xl"><i class="fas fa-folder-open text-gray-300 text-3xl mb-4"></i><p class="text-gray-500 font-bold">ไม่พบกิจกรรม</p></div>';
        return;
    }
    container.innerHTML = data.map(a => renderCard(a)).join('');
}

function updateSummary(data) {
    const total = data.length;
    const capacity = data.reduce((s, a) => s + parseInt(a.max_members || 0), 0);
    const registered = data.reduce((s, a) => s + parseInt(a.current_members_count || 0), 0);
    const fill = capacity > 0 ? Math.round((registered / capacity) * 100) : 0;
    document.getElementById('card-activities').textContent = total;
    document.getElementById('card-capacity').textContent = capacity;
    document.getElementById('card-registered').textContent = registered;
    document.getElementById('card-fill').textContent = fill + '%';
}

function renderChart(data) {
    const top = data.sort((a, b) => parseInt(b.max_members || 0) - parseInt(a.max_members || 0)).slice(0, 8);
    const ctx = document.getElementById('best-chart').getContext('2d');
    if (bestChart) bestChart.destroy();
    bestChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: top.map(a => a.name.length > 15 ? a.name.substring(0, 15) + '...' : a.name),
            datasets: [
                { label: 'สมัครแล้ว', data: top.map(a => parseInt(a.current_members_count || 0)), backgroundColor: 'rgba(245, 158, 11, 0.8)' },
                { label: 'คงเหลือ', data: top.map(a => Math.max(0, parseInt(a.max_members || 0) - parseInt(a.current_members_count || 0))), backgroundColor: 'rgba(229, 231, 235, 0.8)' }
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } } }
    });
}

async function loadData() {
    try {
        const res = await fetch('../controllers/BestActivityController.php?action=list');
        const data = await res.json();
        if (data.success) {
            activitiesData = data.data || [];
            renderMobileCards(activitiesData);
            updateSummary(activitiesData);
            renderChart([...activitiesData]);
        }
    } catch (e) {
        showToast('โหลดข้อมูลไม่สำเร็จ', 'error');
    }
}

async function loadMembers(id) {
    try {
        const res = await fetch(`../controllers/BestActivityController.php?action=members&id=${id}`);
        const data = await res.json();
        const list = document.getElementById('members-list');
        if (data.success && data.members && data.members.length > 0) {
            list.innerHTML = data.members.map(m => `
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-800 rounded-xl">
                    <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center text-white font-bold">${m.name.charAt(0)}</div>
                    <div class="flex-1">
                        <div class="font-bold text-gray-800 dark:text-white">${m.name}</div>
                        <div class="text-xs text-gray-400">${m.student_id} • ${m.class_name}</div>
                    </div>
                    <button class="remove-member-btn w-10 h-10 rounded-xl bg-rose-500 text-white active:scale-95 transition-all" data-sid="${m.student_id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<div class="text-center py-8 text-gray-400"><i class="fas fa-users-slash text-2xl mb-2"></i><p>ยังไม่มีสมาชิก</p></div>';
        }
    } catch (e) {
        showToast('โหลดสมาชิกไม่สำเร็จ', 'error');
    }
}

// Event listeners
document.getElementById('activity-search').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.activity-card').forEach(c => c.style.display = c.dataset.name.includes(q) ? '' : 'none');
});

['btn-new', 'btn-new-mobile'].forEach(id => {
    document.getElementById(id).addEventListener('click', () => {
        document.getElementById('activity_id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('description').value = '';
        document.querySelectorAll('.grade-opt').forEach(cb => cb.checked = false);
        document.getElementById('max_members').value = '';
        document.getElementById('modal-title').textContent = 'เพิ่มกิจกรรม';
        document.getElementById('modal').classList.remove('hidden');
    });
});

['btn-cancel', 'btn-cancel-2'].forEach(id => {
    document.getElementById(id).addEventListener('click', () => document.getElementById('modal').classList.add('hidden'));
});

document.getElementById('btn-close-members').addEventListener('click', () => document.getElementById('members-modal').classList.add('hidden'));

document.addEventListener('click', function(e) {
    const editBtn = e.target.closest('.edit-btn');
    if (editBtn) {
        const id = editBtn.dataset.id;
        const a = activitiesData.find(x => x.id == id);
        if (a) {
            document.getElementById('activity_id').value = a.id;
            document.getElementById('name').value = a.name;
            document.getElementById('description').value = a.description || '';
            const sel = (a.grade_levels || '').split(',').map(s => s.trim());
            document.querySelectorAll('.grade-opt').forEach(cb => cb.checked = sel.includes(cb.value));
            document.getElementById('max_members').value = a.max_members;
            document.getElementById('modal-title').textContent = 'แก้ไขกิจกรรม';
            document.getElementById('modal').classList.remove('hidden');
        }
    }
    
    const membersBtn = e.target.closest('.members-btn');
    if (membersBtn) {
        currentActivityId = membersBtn.dataset.id;
        loadMembers(currentActivityId);
        document.getElementById('members-modal').classList.remove('hidden');
    }
    
    const deleteBtn = e.target.closest('.delete-btn');
    if (deleteBtn) {
        Swal.fire({ title: 'ลบกิจกรรม?', icon: 'warning', showCancelButton: true, confirmButtonText: 'ลบ', cancelButtonText: 'ยกเลิก' }).then(r => {
            if (r.isConfirmed) {
                const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', deleteBtn.dataset.id);
                fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd }).then(r => r.json()).then(d => {
                    if (d.success) { showToast('ลบสำเร็จ', 'success'); loadData(); } else { showToast(d.message || 'ลบไม่สำเร็จ', 'error'); }
                });
            }
        });
    }
    
    const rmBtn = e.target.closest('.remove-member-btn');
    if (rmBtn) {
        const sid = rmBtn.dataset.sid;
        Swal.fire({ title: 'ลบสมาชิก?', icon: 'warning', showCancelButton: true, confirmButtonText: 'ลบ', cancelButtonText: 'ยกเลิก' }).then(r => {
            if (r.isConfirmed) {
                const fd = new FormData(); fd.append('action', 'remove_member'); fd.append('id', currentActivityId); fd.append('student_id', sid);
                fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd }).then(r => r.json()).then(d => {
                    if (d.success) { showToast('ลบสมาชิกสำเร็จ', 'success'); loadMembers(currentActivityId); loadData(); } else { showToast(d.message || 'ลบไม่สำเร็จ', 'error'); }
                });
            }
        });
    }
});

document.getElementById('btn-add-member').addEventListener('click', async function() {
    const sid = document.getElementById('member-student-id').value.trim();
    if (!sid) { showToast('กรุณากรอกรหัสนักเรียน', 'warning'); return; }
    const fd = new FormData(); fd.append('action', 'add_member'); fd.append('id', currentActivityId); fd.append('student_id', sid);
    const res = await fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd });
    const d = await res.json();
    if (d.success) { showToast('เพิ่มสมาชิกสำเร็จ', 'success'); document.getElementById('member-student-id').value = ''; loadMembers(currentActivityId); loadData(); } else { showToast(d.message || 'เพิ่มไม่สำเร็จ', 'error'); }
});

document.getElementById('best-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = document.getElementById('activity_id').value;
    const grades = Array.from(document.querySelectorAll('.grade-opt:checked')).map(cb => cb.value);
    if (!grades.length) { showToast('เลือกระดับชั้นอย่างน้อย 1', 'warning'); return; }
    const fd = new FormData();
    fd.append('action', id ? 'update' : 'create');
    if (id) fd.append('id', id);
    fd.append('name', document.getElementById('name').value);
    fd.append('description', document.getElementById('description').value);
    fd.append('grade_levels', grades.join(','));
    fd.append('max_members', document.getElementById('max_members').value);
    const res = await fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd });
    const d = await res.json();
    if (d.success) { showToast(id ? 'แก้ไขสำเร็จ' : 'เพิ่มสำเร็จ', 'success'); document.getElementById('modal').classList.add('hidden'); loadData(); } else { showToast(d.message || 'บันทึกไม่สำเร็จ', 'error'); }
});

document.addEventListener('DOMContentLoaded', loadData);
</script>

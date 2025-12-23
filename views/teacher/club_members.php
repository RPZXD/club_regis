<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
            <i class="fas fa-users text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-black text-gray-800 dark:text-white">จัดการสมาชิก</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">ตรวจสอบและจัดการรายชื่อนักเรียน</p>
        </div>
    </div>
</div>

<!-- Club Selector -->
<div class="glass rounded-2xl p-4 mb-6">
    <label class="block font-black text-gray-700 dark:text-gray-200 mb-2 text-sm">เลือกชุมนุมที่ต้องการจัดการ</label>
    <div class="flex gap-2">
        <select id="club_id" class="flex-1 bg-white dark:bg-slate-800 border-2 border-indigo-100 dark:border-indigo-900/50 rounded-xl px-4 py-3.5 font-bold text-indigo-600 dark:text-indigo-400 focus:outline-none focus:border-indigo-400 transition-all">
            <option value="">-- กรุณาเลือกชุมนุม --</option>
        </select>
        <button id="print-btn" class="w-14 h-14 rounded-xl bg-amber-500 hover:bg-amber-600 text-white shadow-lg shadow-amber-500/30 flex items-center justify-center transition-all active:scale-95">
            <i class="fas fa-print text-lg"></i>
        </button>
    </div>
</div>

<!-- Stats Bar -->
<div id="member-stats" class="hidden mb-6">
    <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4">
        <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-2xl px-5 py-3 shadow-lg shadow-indigo-500/20">
            <div class="text-[10px] font-bold uppercase tracking-wider opacity-80">สมาชิกทั้งหมด</div>
            <div id="total-members" class="text-2xl font-black">0</div>
        </div>
        <div id="club-info-badge" class="flex-shrink-0 bg-white dark:bg-slate-800 rounded-2xl px-5 py-3 shadow border border-gray-100 dark:border-gray-700 hidden">
            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">ชุมนุม</div>
            <div id="selected-club-name" class="text-lg font-black text-gray-800 dark:text-white truncate max-w-[150px]">-</div>
        </div>
    </div>
</div>

<!-- Mobile Cards View -->
<div id="member-cards" class="space-y-3 md:hidden">
    <div class="text-center py-16 glass rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
        <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-hand-pointer text-gray-300 dark:text-gray-500 text-2xl"></i>
        </div>
        <p class="text-gray-500 dark:text-gray-400 font-bold">กรุณาเลือกชุมนุม</p>
        <p class="text-xs text-gray-400 mt-1">เพื่อแสดงรายชื่อสมาชิก</p>
    </div>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block glass rounded-2xl overflow-hidden">
    <table id="members-table" class="w-full">
        <thead>
            <tr class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                <th class="py-4 px-4 text-center font-black w-16">#</th>
                <th class="py-4 px-4 text-left font-black">รหัส</th>
                <th class="py-4 px-4 text-left font-black">ชื่อ-นามสกุล</th>
                <th class="py-4 px-4 text-center font-black">ชั้น</th>
                <th class="py-4 px-4 text-center font-black">วันที่สมัคร</th>
                <th class="py-4 px-4 text-center font-black w-20">ลบ</th>
            </tr>
        </thead>
        <tbody id="members-table-body" class="divide-y divide-gray-100 dark:divide-gray-800">
        </tbody>
    </table>
</div>

<script>
let membersData = [];
let clubsData = [];

document.addEventListener('DOMContentLoaded', function() {
    // Load clubs
    fetch('../controllers/ClubController.php?action=list_by_advisor')
        .then(res => res.json())
        .then(data => {
            if (data.data) {
                clubsData = data.data;
                const select = document.getElementById('club_id');
                data.data.forEach(club => {
                    const opt = document.createElement('option');
                    opt.value = club.club_id;
                    opt.textContent = club.club_name;
                    select.appendChild(opt);
                });
            }
        });

    // Initialize DataTable (Desktop only)
    const membersTable = $('#members-table').DataTable({
        ordering: true,
        order: [[0, "asc"]],
        dom: 'tp',
        pageLength: 20,
        language: {
            zeroRecords: "เลือกชุมนุมเพื่อแสดงรายชื่อสมาชิก",
            paginate: { next: '→', previous: '←' }
        }
    });

    // Render Mobile Card
    function renderMemberCard(stu, idx, clubId) {
        return `
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/20 overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="p-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-lg shadow-lg flex-shrink-0">
                            ${idx + 1}
                        </div>
                        <div class="flex-1 w-0">
                            <h4 class="font-black text-gray-800 dark:text-white text-base whitespace-nowrap overflow-hidden text-ellipsis">${stu.name}</h4>
                            <div class="flex items-center gap-3 mt-1 text-sm">
                                <span class="text-indigo-600 dark:text-indigo-400 font-mono font-bold">${stu.student_id}</span>
                                <span class="px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold">${stu.class_name || '-'}</span>
                            </div>
                        </div>
                        <button class="delete-member-btn w-12 h-12 rounded-xl bg-rose-500 text-white shadow-lg shadow-rose-500/30 flex items-center justify-center active:scale-95 transition-all flex-shrink-0" 
                                data-student-id="${stu.student_id}" data-club-id="${clubId}">
                            <i class="fas fa-user-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="px-4 py-2 bg-gray-50 dark:bg-slate-900/30 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <span class="text-[11px] text-gray-400"><i class="far fa-clock mr-1"></i>${stu.created_at || '-'}</span>
                </div>
            </div>`;
    }

    // Render All Mobile Cards
    function renderMobileCards(data, clubId) {
        const container = document.getElementById('member-cards');
        
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="text-center py-16 glass rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-slash text-gray-300 dark:text-gray-500 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-bold">ยังไม่มีสมาชิก</p>
                    <p class="text-xs text-gray-400 mt-1">ชุมนุมนี้ยังไม่มีนักเรียนสมัคร</p>
                </div>`;
            return;
        }

        container.innerHTML = data.map((stu, idx) => renderMemberCard(stu, idx, clubId)).join('');
    }

    // Handle Club Selection
    document.getElementById('club_id').addEventListener('change', function() {
        const clubId = this.value;
        membersTable.clear().draw();
        
        if (!clubId) {
            document.getElementById('member-stats').classList.add('hidden');
            document.getElementById('member-cards').innerHTML = `
                <div class="text-center py-16 glass rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hand-pointer text-gray-300 dark:text-gray-500 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-bold">กรุณาเลือกชุมนุม</p>
                    <p class="text-xs text-gray-400 mt-1">เพื่อแสดงรายชื่อสมาชิก</p>
                </div>`;
            return;
        }

        // Show loading
        document.getElementById('member-cards').innerHTML = `
            <div class="text-center py-16">
                <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-gray-500 font-bold">กำลังโหลด...</p>
            </div>`;

        // Find club name
        const selectedClub = clubsData.find(c => c.club_id == clubId);
        if (selectedClub) {
            document.getElementById('selected-club-name').textContent = selectedClub.club_name;
            document.getElementById('club-info-badge').classList.remove('hidden');
        }

        // Fetch members
        fetch('../controllers/ClubController.php?action=list_by_advisor')
            .then(res => res.json())
            .then(data => {
                return fetch(`../controllers/ClubController.php?action=members&club_id=${clubId}&term=${data.term}&year=${data.year}`);
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.members) {
                    membersData = data.members;
                    document.getElementById('total-members').textContent = data.members.length;
                    document.getElementById('member-stats').classList.remove('hidden');

                    // Update Mobile Cards
                    renderMobileCards(data.members, clubId);

                    // Update Desktop Table
                    const rows = data.members.map((stu, idx) => [
                        `<span class="font-bold text-slate-400">${idx + 1}</span>`,
                        `<span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">${stu.student_id}</span>`,
                        `<span class="font-bold text-slate-800 dark:text-white">${stu.name}</span>`,
                        `<span class="px-2 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold">${stu.class_name || '-'}</span>`,
                        `<span class="text-xs text-slate-400">${stu.created_at || '-'}</span>`,
                        `<button class="delete-member-btn w-9 h-9 rounded-lg bg-rose-100 text-rose-600 hover:bg-rose-500 hover:text-white transition-all" data-student-id="${stu.student_id}" data-club-id="${clubId}"><i class="fas fa-trash"></i></button>`
                    ]);
                    membersTable.rows.add(rows).draw();
                } else {
                    renderMobileCards([], clubId);
                }
            });
    });

    // Handle Member Deletion (Works for both mobile and desktop)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.delete-member-btn');
        if (!btn) return;

        const studentId = btn.getAttribute('data-student-id');
        const clubId = btn.getAttribute('data-club-id');

        Swal.fire({
            title: 'ลบนักเรียนออก?',
            text: "นักเรียนจะถูกนำออกจากชุมนุมทันที",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: '<i class="fas fa-user-minus mr-2"></i>ลบออก',
            cancelButtonText: 'ยกเลิก'
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
                        Swal.fire({ icon: 'success', title: 'ลบสำเร็จ!', showConfirmButton: false, timer: 1200 });
                        document.getElementById('club_id').dispatchEvent(new Event('change'));
                    } else {
                        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: data.message });
                    }
                });
            }
        });
    });

    // Print Button
    document.getElementById('print-btn').addEventListener('click', function() {
        const clubId = document.getElementById('club_id').value;
        if (!clubId) {
            Swal.fire({ icon: 'warning', title: 'กรุณาเลือกชุมนุมก่อน', text: 'คุณต้องเลือกชุมนุมก่อนจึงจะพิมพ์รายชื่อได้' });
            return;
        }
        window.open('print_club.php?club_id=' + encodeURIComponent(clubId), '_blank');
    });
});
</script>

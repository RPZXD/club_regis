<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                <i class="fas fa-layer-group text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-gray-800 dark:text-white">รายการชุมนุม</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">จัดการชุมนุมของคุณ</p>
            </div>
        </div>
    </div>
    
    <!-- Create Button - Full Width on Mobile -->
    <button id="create-club-btn" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-indigo-500/30 transition-all active:scale-[0.98] flex items-center justify-center gap-3 text-lg">
        <i class="fas fa-plus-circle"></i>
        <span>สร้างชุมนุมใหม่</span>
    </button>
</div>

<!-- Search & Filter - Compact Design -->
<div class="glass rounded-2xl p-4 mb-6 space-y-3">
    <!-- Search Input -->
    <div class="relative">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
        <input type="text" id="club-search" placeholder="ค้นหาชื่อชุมนุม..." 
               class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white dark:bg-slate-800 border-2 border-indigo-100 dark:border-indigo-900/50 text-gray-700 dark:text-gray-200 focus:outline-none focus:border-indigo-400 transition-all font-bold placeholder:text-gray-400 placeholder:font-normal">
    </div>
    <!-- Filter Row -->
    <div class="flex gap-2">
        <select id="grade-filter" class="flex-1 bg-white dark:bg-slate-800 border-2 border-indigo-100 dark:border-indigo-900/50 rounded-xl px-4 py-3 focus:outline-none focus:border-indigo-400 transition-all font-bold text-gray-700 dark:text-gray-200">
            <option value="">🎓 ทุกระดับชั้น</option>
            <option value="ม.1">ม.1</option>
            <option value="ม.2">ม.2</option>
            <option value="ม.3">ม.3</option>
            <option value="ม.4">ม.4</option>
            <option value="ม.5">ม.5</option>
            <option value="ม.6">ม.6</option>
        </select>
        <button id="refresh-btn" class="w-14 h-14 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-200 transition-all flex items-center justify-center active:scale-95">
            <i class="fas fa-sync-alt text-lg"></i>
        </button>
    </div>
</div>

<!-- Stats Summary -->
<div id="stats-bar" class="flex gap-3 mb-6 overflow-x-auto pb-2 -mx-4 px-4">
    <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-2xl px-5 py-3 shadow-lg shadow-indigo-500/20">
        <div class="text-[10px] font-bold uppercase tracking-wider opacity-80">ชุมนุมทั้งหมด</div>
        <div id="total-clubs" class="text-2xl font-black">-</div>
    </div>
    <div class="flex-shrink-0 bg-emerald-500 text-white rounded-2xl px-5 py-3 shadow-lg shadow-emerald-500/20">
        <div class="text-[10px] font-bold uppercase tracking-wider opacity-80">เปิดรับสมัคร</div>
        <div id="open-clubs" class="text-2xl font-black">-</div>
    </div>
    <div class="flex-shrink-0 bg-rose-500 text-white rounded-2xl px-5 py-3 shadow-lg shadow-rose-500/20">
        <div class="text-[10px] font-bold uppercase tracking-wider opacity-80">เต็มแล้ว</div>
        <div id="full-clubs" class="text-2xl font-black">-</div>
    </div>
</div>

<!-- Mobile Cards View -->
<div id="club-cards" class="space-y-4 md:hidden">
    <!-- Loading State -->
    <div class="text-center py-16">
        <div class="w-16 h-16 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-gray-500 dark:text-gray-400 font-bold">กำลังโหลดข้อมูล...</p>
    </div>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block glass rounded-2xl overflow-hidden">
    <table id="club-table" class="w-full">
        <thead>
            <tr class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                <th class="py-4 px-6 text-left font-black">ชื่อชุมนุม</th>
                <th class="py-4 px-4 text-left font-black">ที่ปรึกษา</th>
                <th class="py-4 px-4 text-center font-black">ระดับชั้น</th>
                <th class="py-4 px-4 text-center font-black">สมาชิก</th>
                <th class="py-4 px-4 text-center font-black">จัดการ</th>
            </tr>
        </thead>
        <tbody id="club-table-body" class="divide-y divide-gray-100 dark:divide-gray-800">
        </tbody>
    </table>
</div>

<!-- Modal Create/Edit -->
<div id="club-modal" class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm hidden p-0 md:p-4">
    <div class="bg-white dark:bg-slate-900 w-full md:max-w-lg md:rounded-3xl rounded-t-3xl shadow-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5 flex justify-between items-center text-white z-10">
            <div>
                <h3 id="modal-title" class="text-xl font-black">สร้างชุมนุมใหม่</h3>
                <p id="modal-subtitle" class="text-indigo-200 text-sm">กรอกข้อมูลให้ครบถ้วน</p>
            </div>
            <button class="close-modal-btn w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-all active:scale-95">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <!-- Modal Form -->
        <form id="club-form" class="p-6 space-y-5">
            <input type="hidden" id="club_id" name="club_id">
            
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-2 text-sm">ชื่อชุมนุม</label>
                <input type="text" name="club_name" required 
                       class="w-full bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3.5 focus:border-indigo-500 focus:outline-none transition-all font-bold dark:text-white"
                       placeholder="เช่น ชุมนุมภาษาอังกฤษ">
            </div>
            
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-2 text-sm">รายละเอียด</label>
                <textarea name="description" required rows="3" 
                          class="w-full bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3.5 focus:border-indigo-500 focus:outline-none transition-all dark:text-white"
                          placeholder="อธิบายกิจกรรมของชุมนุม..."></textarea>
            </div>
            
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-3 text-sm">ระดับชั้นที่เปิดรับ</label>
                <div class="grid grid-cols-3 gap-2">
                    <?php foreach(['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'] as $g): ?>
                    <label class="grade-checkbox relative cursor-pointer">
                        <input type="checkbox" name="grade_levels[]" value="<?= $g ?>" class="peer sr-only">
                        <div class="w-full py-3 text-center rounded-xl border-2 border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 font-black text-gray-500 dark:text-gray-400 transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-500 peer-checked:text-white active:scale-95">
                            <?= $g ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-2 text-sm">จำนวนที่รับสมัคร (คน)</label>
                <input type="number" name="max_members" required min="1" 
                       class="w-full bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3.5 focus:border-indigo-500 focus:outline-none transition-all font-bold dark:text-white text-center text-xl"
                       placeholder="25">
            </div>
            
            <input type="hidden" name="advisor_teacher" value="<?php echo $_SESSION['username']; ?>">
            
            <div class="flex gap-3 pt-4">
                <button type="button" class="close-modal-btn flex-1 py-4 rounded-xl font-black text-gray-500 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 transition-all active:scale-[0.98]">
                    ยกเลิก
                </button>
                <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-black py-4 rounded-xl shadow-lg shadow-indigo-500/30 transition-all active:scale-[0.98]">
                    บันทึก
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    const currentUser = "<?php echo $_SESSION['username']; ?>";
    let allData = [];

    // Render Mobile Card
    function renderCard(row) {
        const current = parseInt(row.current_members_count || 0);
        const max = parseInt(row.max_members || 0);
        const percent = max > 0 ? Math.min(100, Math.round((current / max) * 100)) : 0;
        const isFull = current >= max;
        const isAdvisor = row.advisor_teacher == currentUser;
        
        const grades = (row.grade_levels || '').split(',').filter(g => g.trim()).map(g => 
            `<span class="inline-block px-2.5 py-1 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 text-xs font-black">${g.trim()}</span>`
        ).join(' ');

        return `
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-black/20 overflow-hidden border border-gray-100 dark:border-gray-700">
                <!-- Card Header -->
                <div class="p-4 pb-3 overflow-hidden">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br ${isFull ? 'from-rose-500 to-red-600' : 'from-indigo-500 to-purple-600'} flex items-center justify-center text-white shadow-lg flex-shrink-0">
                            <i class="fas ${isFull ? 'fa-lock' : 'fa-users'} text-lg"></i>
                        </div>
                        <div class="flex-1 w-0">
                            <h3 class="font-black text-gray-800 dark:text-white text-base leading-tight whitespace-nowrap overflow-hidden text-ellipsis" title="${row.club_name}">${row.club_name}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 whitespace-nowrap overflow-hidden text-ellipsis">
                                <i class="fas fa-user-tie text-xs mr-1"></i>${row.advisor_teacher_name || row.advisor_teacher}
                            </p>
                        </div>
                        ${isFull ? '<span class="flex-shrink-0 px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black ml-1">เต็ม</span>' : ''}
                    </div>
                </div>
                
                <!-- Grade Badges -->
                <div class="px-4 pb-3 flex flex-wrap gap-1.5">
                    ${grades}
                </div>
                
                <!-- Progress Section -->
                <div class="mx-4 mb-4 p-3 rounded-xl bg-gray-50 dark:bg-slate-900/50">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wide">ความจุสมาชิก</span>
                        <span class="text-sm font-black ${isFull ? 'text-rose-500' : 'text-indigo-600 dark:text-indigo-400'}">${current} / ${max} คน</span>
                    </div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700 ${isFull ? 'bg-gradient-to-r from-rose-500 to-red-500' : 'bg-gradient-to-r from-indigo-500 to-purple-500'}" style="width: ${percent}%"></div>
                    </div>
                    <div class="text-right mt-1">
                        <span class="text-[10px] font-bold ${isFull ? 'text-rose-400' : 'text-gray-400'}">${percent}%</span>
                    </div>
                </div>
                
                <!-- Card Footer -->
                <div class="px-4 py-3 bg-gray-50 dark:bg-slate-900/30 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <span class="text-[11px] font-mono text-gray-400 dark:text-gray-500">ID: ${row.club_id}</span>
                    ${isAdvisor ? `
                        <div class="flex gap-2">
                            <button class="edit-btn px-4 py-2 rounded-xl bg-amber-500 text-white font-bold text-sm shadow-md shadow-amber-500/20 active:scale-95 transition-all flex items-center gap-2" data-id="${row.club_id}">
                                <i class="fas fa-edit"></i> แก้ไข
                            </button>
                            <button class="delete-btn w-10 h-10 rounded-xl bg-rose-500 text-white shadow-md shadow-rose-500/20 active:scale-95 transition-all flex items-center justify-center" data-id="${row.club_id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    ` : '<span class="text-xs text-gray-400 italic">ไม่มีสิทธิ์จัดการ</span>'}
                </div>
            </div>`;
    }

    // Render All Cards
    function renderMobileCards(data) {
        const container = $('#club-cards');
        
        // Update stats
        const total = data.length;
        const full = data.filter(r => parseInt(r.current_members_count || 0) >= parseInt(r.max_members || 0)).length;
        $('#total-clubs').text(total);
        $('#open-clubs').text(total - full);
        $('#full-clubs').text(full);

        if (!data || data.length === 0) {
            container.html(`
                <div class="text-center py-16 bg-white dark:bg-slate-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-folder-open text-gray-300 dark:text-gray-500 text-3xl"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-black text-lg">ไม่พบข้อมูลชุมนุม</p>
                    <p class="text-sm text-gray-400 mt-2">ลองเปลี่ยนคำค้นหาหรือตัวกรอง</p>
                </div>`);
            return;
        }

        container.html(data.map(row => renderCard(row)).join(''));
    }

    // DataTable Configuration
    const table = $('#club-table').DataTable({
        ajax: {
            url: "../controllers/ClubController.php?action=list",
            dataSrc: function(json) {
                allData = json.data || [];
                renderMobileCards(allData);
                return allData;
            }
        },
        ordering: true,
        order: [[0, "asc"]],
        dom: 'tp',
        pageLength: 15,
        language: {
            zeroRecords: "ไม่พบข้อมูล",
            paginate: { next: '→', previous: '←' }
        },
        columns: [
            { 
                data: "club_name",
                className: "max-w-[200px]",
                render: (d, t, row) => `<div class="font-black text-gray-800 dark:text-white truncate max-w-[180px]" title="${d}">${d}</div><div class="text-xs text-gray-400">ID: ${row.club_id}</div>`
            },
            { 
                data: "advisor_teacher_name",
                className: "max-w-[150px]",
                render: d => `<span class="truncate block max-w-[130px]" title="${d}">${d}</span>`
            },
            { 
                data: "grade_levels",
                className: "text-center",
                render: d => (d || '').split(',').map(g => `<span class="inline-block px-2 py-0.5 rounded bg-indigo-100 text-indigo-600 text-[10px] font-bold mr-1">${g.trim()}</span>`).join('')
            },
            { 
                data: null,
                className: "text-center",
                render: (d, t, row) => {
                    const c = parseInt(row.current_members_count || 0);
                    const m = parseInt(row.max_members || 0);
                    return `<span class="font-bold ${c >= m ? 'text-rose-500' : 'text-indigo-600'}">${c}/${m}</span>`;
                }
            },
            { 
                data: null,
                className: "text-center",
                render: (d, t, row) => {
                    if (row.advisor_teacher == currentUser) {
                        return `<button class="edit-btn text-amber-500 hover:text-amber-600 mx-1" data-id="${row.club_id}"><i class="fas fa-edit"></i></button>
                                <button class="delete-btn text-rose-500 hover:text-rose-600 mx-1" data-id="${row.club_id}"><i class="fas fa-trash"></i></button>`;
                    }
                    return '-';
                }
            }
        ]
    });

    // Search
    $('#club-search').on('input', function() { 
        table.search(this.value).draw();
        // Also filter mobile cards
        const val = this.value.toLowerCase();
        const grade = $('#grade-filter').val();
        const filtered = allData.filter(r => {
            const matchName = r.club_name.toLowerCase().includes(val);
            const matchGrade = !grade || (r.grade_levels || '').includes(grade);
            return matchName && matchGrade;
        });
        renderMobileCards(filtered);
    });

    // Grade Filter
    $('#grade-filter').on('change', function() { 
        table.column(2).search(this.value).draw();
        const val = $('#club-search').val().toLowerCase();
        const grade = this.value;
        const filtered = allData.filter(r => {
            const matchName = r.club_name.toLowerCase().includes(val);
            const matchGrade = !grade || (r.grade_levels || '').includes(grade);
            return matchName && matchGrade;
        });
        renderMobileCards(filtered);
    });

    // Refresh
    $('#refresh-btn').on('click', function() {
        $(this).find('i').addClass('fa-spin');
        table.ajax.reload(() => {
            setTimeout(() => $('#refresh-btn i').removeClass('fa-spin'), 500);
        });
    });

    // Modal
    $('#create-club-btn').on('click', function() {
        $('#modal-title').text('สร้างชุมนุมใหม่');
        $('#modal-subtitle').text('กรอกข้อมูลให้ครบถ้วน');
        $('#club-form')[0].reset();
        $('#club_id').val('');
        $('#club-modal').removeClass('hidden');
    });

    $('.close-modal-btn').on('click', () => $('#club-modal').addClass('hidden'));
    $('#club-modal').on('click', function(e) {
        if (e.target === this) $(this).addClass('hidden');
    });

    // Edit
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const row = allData.find(r => r.club_id == id);
        if (row) {
            $('#modal-title').text('แก้ไขชุมนุม');
            $('#modal-subtitle').text(row.club_name);
            $('#club_id').val(row.club_id);
            $('input[name="club_name"]').val(row.club_name);
            $('textarea[name="description"]').val(row.description);
            $('input[name="max_members"]').val(row.max_members);
            $('input[name="grade_levels[]"]').prop('checked', false);
            (row.grade_levels || '').split(',').forEach(g => {
                $(`input[name="grade_levels[]"][value="${g.trim()}"]`).prop('checked', true);
            });
            $('#club-modal').removeClass('hidden');
        }
    });

    // Submit
    $('#club-form').on('submit', function(e) {
        e.preventDefault();
        const action = $('#club_id').val() ? 'update' : 'create';
        const grades = $('input[name="grade_levels[]"]:checked').map(function() { return this.value; }).get().join(',');
        let data = $(this).serializeArray().filter(i => i.name !== 'grade_levels[]');
        data.push({ name: 'grade_levels', value: grades });

        $.post(`../controllers/ClubController.php?action=${action}`, $.param(data), function(res) {
            if (res.success) {
                $('#club-modal').addClass('hidden');
                table.ajax.reload();
                Swal.fire({ icon: 'success', title: 'สำเร็จ!', showConfirmButton: false, timer: 1500 });
            } else {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message });
            }
        }, 'json');
    });

    // Delete
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'ลบชุมนุมนี้?',
            text: "หากมีสมาชิกอยู่จะไม่สามารถลบได้",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'ลบเลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controllers/ClubController.php?action=delete', { club_id: id }, function(res) {
                    if (res.success) {
                        table.ajax.reload();
                        Swal.fire({ icon: 'success', title: 'ลบสำเร็จ!' });
                    } else {
                        Swal.fire({ icon: 'error', title: 'ไม่สามารถลบได้', text: res.message });
                    }
                }, 'json');
            }
        });
    });
});
</script>

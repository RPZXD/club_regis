<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

// Initialize tables for admin interface
require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../models/BestActivity.php';
use App\DatabaseClub;
use App\Models\BestActivity;

$db = new DatabaseClub();
$pdo = $db->getPDO();
$bestModel = new BestActivity($pdo, true); // Enable auto table initialization for admin

require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h5 class="m-0">Best For Teen 2025 - จัดการกิจกรรม</h5>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header bg-blue-600 text-white font-semibold text-lg ">
                        <span>รายการกิจกรรม</span>
                        <button id="btn-new" class="px-3 py-1 bg-green-600 hover:bg-green-700 rounded text-white ">เพิ่มกิจกรรม</button>
                    </div>
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div id="summary" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                            <div class="p-3 rounded bg-blue-50 border border-blue-200">
                                <div class="text-xs text-blue-600">จำนวนกิจกรรม</div>
                                <div id="card-activities" class="text-2xl font-bold text-blue-800">0</div>
                            </div>
                            <div class="p-3 rounded bg-green-50 border border-green-200">
                                <div class="text-xs text-green-600">ที่รับทั้งหมด</div>
                                <div id="card-capacity" class="text-2xl font-bold text-green-800">0</div>
                            </div>
                            <div class="p-3 rounded bg-indigo-50 border border-indigo-200">
                                <div class="text-xs text-indigo-600">สมัครแล้ว</div>
                                <div id="card-registered" class="text-2xl font-bold text-indigo-800">0</div>
                            </div>
                            <div class="p-3 rounded bg-yellow-50 border border-yellow-200">
                                <div class="text-xs text-yellow-600">อัตราการเต็ม</div>
                                <div id="card-fill" class="text-2xl font-bold text-yellow-800">0%</div>
                            </div>
                        </div>

                        <!-- Chart -->
                        <div class="mb-6">
                            <canvas id="best-chart" height="120"></canvas>
                        </div>

                        <div class="overflow-x-auto">
                            <table id="best-table" class="display min-w-full bg-white border border-gray-200 rounded shadow">
                                <thead>
                                    <tr class="bg-blue-500 text-white font-semibold">
                                        <th class="py-2 px-4 text-center border-b">รหัส</th>
                                        <th class="py-2 px-4 text-center border-b">ชื่อกิจกรรม</th>
                                        <th class="py-2 px-4 text-center border-b">ระดับชั้น</th>
                                        <th class="py-2 px-4 text-center border-b">จำนวนที่รับ</th>
                                        <th class="py-2 px-4 text-center border-b">ปี</th>
                                        <th class="py-2 px-4 text-center border-b">สมาชิก</th>
                                           <th class="py-2 px-4 text-center border-b">พิมพ์รายชื่อ</th>
                                        <th class="py-2 px-4 text-center border-b">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="best-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div id="modal" class="hidden fixed inset-0 bg-transparent flex items-center justify-center p-4">
                    <div class="bg-white rounded shadow-xl max-w-lg w-full p-4">
                        <h3 id="modal-title" class="text-lg font-semibold mb-3">เพิ่ม/แก้ไขกิจกรรม</h3>
                        <form id="best-form">
                            <input type="hidden" id="activity_id">
                            <div class="mb-2">
                                <label class="block text-sm">ชื่อกิจกรรม</label>
                                <input type="text" id="name" class="w-full border rounded px-2 py-1" required>
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm">รายละเอียด</label>
                                <textarea id="description" class="w-full border rounded px-2 py-1"></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm mb-1">ระดับชั้น</label>
                                <div id="grade-checkboxes" class="flex flex-wrap gap-2">
                                    <label class="inline-flex items-center gap-1 bg-blue-50 px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" class="grade-opt" value="ม.1"> ม.1
                                    </label>
                                    <label class="inline-flex items-center gap-1 bg-blue-50 px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" class="grade-opt" value="ม.2"> ม.2
                                    </label>
                                    <label class="inline-flex items-center gap-1 bg-blue-50 px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" class="grade-opt" value="ม.3"> ม.3
                                    </label>
                                    <label class="inline-flex items-center gap-1 bg-blue-50 px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" class="grade-opt" value="ม.4"> ม.4
                                    </label>
                                    <label class="inline-flex items-center gap-1 bg-blue-50 px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" class="grade-opt" value="ม.5"> ม.5
                                    </label>
                                    <label class="inline-flex items-center gap-1 bg-blue-50 px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" class="grade-opt" value="ม.6"> ม.6
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">เลือกได้มากกว่า 1 ระดับชั้น</p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm">จำนวนที่รับ</label>
                                <input type="number" id="max_members" min="1" class="w-full border rounded px-2 py-1" required>
                            </div>
                            <div class="flex gap-2 justify-end">
                                <button type="button" id="btn-cancel" class="px-3 py-1 bg-gray-300 hover:bg-gray-400 rounded">ยกเลิก</button>
                                <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded">บันทึก</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Members Modal -->
                <div id="members-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-auto flex items-start justify-center p-4">
                    <div class="bg-white rounded shadow-xl max-w-3xl w-full my-8" style="max-height: calc(100vh - 4rem);">
                        <div class="p-4 border-b">
                            <h3 class="text-lg font-semibold">จัดการสมาชิก</h3>
                        </div>
                        <div class="p-4 border-b">
                            <div class="flex gap-2 items-end">
                                <div>
                                    <label class="block text-sm">รหัสนักเรียน</label>
                                    <input type="text" id="member-student-id" class="border rounded px-2 py-1">
                                </div>
                                <button id="btn-add-member" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded">เพิ่มสมาชิก</button>
                                <button id="btn-close-members" class="ml-auto px-3 py-1 bg-gray-300 hover:bg-gray-400 rounded">ปิด</button>
                            </div>
                        </div>
                        <div class="overflow-auto" style="max-height: calc(100vh - 12rem);">
                            <table id="members-table" class="min-w-full border">
                                <thead class="sticky top-0 bg-white">
                                    <tr class="bg-gray-100">
                                        <th class="p-2 border">รหัส</th>
                                        <th class="p-2 border">ชื่อ</th>
                                        <th class="p-2 border">ห้อง</th>
                                        <th class="p-2 border">เมื่อ</th>
                                        <th class="p-2 border">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="members-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
    <?php require_once('../footer.php');?>
</div>
<?php require_once('script.php');?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
// Enhanced performance for admin interface
let table; 
let currentActivityId = null; 
let bestChart = null;
let dataCache = {
  list: null,
  lastUpdated: 0,
  cacheTimeout: 30000 // 30 seconds
};

function showToast(message, type = 'info') {
    const icon = ['success','error','warning','info','question'].includes(type) ? type : 'info';
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
}

// Enhanced fetch with caching
async function fetchList(useCache = true) {
    if (useCache && dataCache.list && (Date.now() - dataCache.lastUpdated) < dataCache.cacheTimeout) {
        return dataCache.list;
    }
    
    try {
        const response = await fetch('../controllers/BestActivityController.php?action=list', {
            headers: { 'Cache-Control': 'max-age=30' }
        });
        const data = await response.json();
        dataCache.list = data;
        dataCache.lastUpdated = Date.now();
        return data;
    } catch (error) {
        console.error('Error fetching list:', error);
        showToast('ไม่สามารถโหลดข้อมูลได้', 'error');
        return { success: false, data: [] };
    }
}

// Optimized render function with batch processing
function render(list) {
    if (!table) {
        table = $('#best-table').DataTable({
            ordering: true,
            order: [[0,'asc']],
            language: { 
                search: 'ค้นหา:',
                emptyTable: "ไม่พบข้อมูลกิจกรรม",
                loadingRecords: "กำลังโหลด...",
                processing: "กำลังประมวลผล..."
            },
            processing: false,
            deferRender: true, // Optimize rendering
            pageLength: 25,
            columnDefs: [
                { targets: [6, 7], orderable: false } // Disable sorting for action columns
            ]
        });
    }
    
    table.clear();
    
    // Batch process all rows
    const tableData = list.map(a => {
        const current = parseInt(a.current_members_count || 0);
        const max = parseInt(a.max_members || 0);
        const percent = max > 0 ? Math.round((current / max) * 100) : 0;
        
        let barColor = 'bg-green-400';
        if (percent >= 100) barColor = 'bg-red-500';
        else if (percent >= 70) barColor = 'bg-yellow-400';
        
        const progress = `
            <div class="w-36 mx-auto">
                <div class="relative h-5 bg-gray-200 rounded-full overflow-hidden">
                    <div class="absolute left-0 top-0 h-5 ${barColor} transition-all duration-300" style="width:${percent}%"></div>
                    <div class="absolute w-full text-xs text-center top-0 left-0 h-5 leading-5 font-semibold">${current} / ${max}</div>
                </div>
            </div>`;
            
        const printBtn = `
            <a href="print_best.php?id=${a.id}" target="_blank" class="inline-block px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                <i class="fas fa-print mr-1"></i>พิมพ์
            </a>`;
            
        const actions = `
            <div class="flex gap-1 justify-center">
                <button class="px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors text-sm" onclick="openEdit(${a.id})">
                    <i class="fas fa-edit"></i> แก้ไข
                </button>
                <button class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-sm" onclick="openMembers(${a.id})">
                    <i class="fas fa-users"></i> สมาชิก
                </button>
                <button class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm" onclick="removeActivity(${a.id})">
                    <i class="fas fa-trash"></i> ลบ
                </button>
            </div>`;
            
        return [
            a.id,
            `<span class="font-semibold text-blue-700">${a.name}</span>`,
            `<span class="px-2 py-1 bg-gray-100 rounded text-sm">${a.grade_levels}</span>`,
            progress,
            a.year,
            `<span class="font-bold ${current > 0 ? 'text-blue-600' : 'text-gray-400'}">${current}</span>`,
            printBtn,
            actions
        ];
    });

    // Batch add all rows
    table.rows.add(tableData).draw(false);

    // Update summary and chart
    updateSummary(list);
    renderChart(list);
}

function updateSummary(list) {
    const totalActivities = list.length;
    const totalCapacity = list.reduce((s,a)=> s + (parseInt(a.max_members||0)), 0);
    const totalCurrent = list.reduce((s,a)=> s + (parseInt(a.current_members_count||0)), 0);
    const fill = totalCapacity > 0 ? Math.round((totalCurrent/totalCapacity)*100) : 0;
    document.getElementById('card-activities').textContent = totalActivities;
    document.getElementById('card-capacity').textContent = totalCapacity;
    document.getElementById('card-registered').textContent = totalCurrent;
    document.getElementById('card-fill').textContent = fill + '%';
}

function renderChart(list) {
    const ordered = [...list].sort((a,b)=> (parseInt(b.max_members||0) - parseInt(a.max_members||0)));
    const top = ordered.slice(0, 10);
    const labels = top.map(a=> a.name || ('กิจกรรม #' + a.id));
    const current = top.map(a=> parseInt(a.current_members_count||0));
    const capacity = top.map(a=> parseInt(a.max_members||0));
    const remaining = capacity.map((c, i)=> Math.max(0, c - current[i]));
    const ctx = document.getElementById('best-chart').getContext('2d');
    if (bestChart) { bestChart.destroy(); }
    bestChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'สมัครแล้ว',
                    data: current,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                },
                {
                    label: 'คงเหลือ',
                    data: remaining,
                    backgroundColor: 'rgba(201, 203, 207, 0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            }
        }
    });
}

function openCreate() {
    document.getElementById('activity_id').value = '';
    document.getElementById('name').value = '';
    document.getElementById('description').value = '';
    document.querySelectorAll('.grade-opt').forEach(cb => cb.checked = false);
    document.getElementById('max_members').value = '';
    document.getElementById('modal-title').textContent = 'เพิ่มกิจกรรม';
    document.getElementById('modal').classList.remove('hidden');
}
function openEdit(id) {
    currentActivityId = id;
    fetch('../controllers/BestActivityController.php?action=list').then(r=>r.json()).then(d=>{
        const a = d.data.find(x=>parseInt(x.id)===parseInt(id));
        if (!a) return;
        document.getElementById('activity_id').value = a.id;
        document.getElementById('name').value = a.name;
        document.getElementById('description').value = a.description||'';
        // restore grade checkboxes
        const selected = (a.grade_levels || '').split(/[ ,\/]+/).map(s=>s.trim()).filter(Boolean);
        document.querySelectorAll('.grade-opt').forEach(cb => {
            cb.checked = selected.includes(cb.value);
        });
        document.getElementById('max_members').value = a.max_members;
        document.getElementById('modal-title').textContent = 'แก้ไขกิจกรรม';
        document.getElementById('modal').classList.remove('hidden');
    });
}

function removeActivity(id) {
    Swal.fire({
        title: 'ลบกิจกรรมนี้หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((res) => {
        if (!res.isConfirmed) return;
        const fd = new FormData(); fd.append('action','delete'); fd.append('id', id);
        fetch('../controllers/BestActivityController.php', { method:'POST', body: fd })
          .then(r=>r.json()).then(d=>{ if (d.success) { showToast('ลบสำเร็จ','success'); load(); } else { showToast(d.message||'ลบไม่สำเร็จ','error'); } });
    });
}

function openMembers(id) {
    currentActivityId = id;
    loadMembers();
    document.getElementById('members-modal').classList.remove('hidden');
}

// Enhanced member loading with single optimized query
async function loadMembers() {
    try {
        const response = await fetch(`../controllers/BestActivityController.php?action=members&id=${currentActivityId}`);
        const data = await response.json();
        
        if (!data.success) {
            showToast('ไม่สามารถโหลดรายชื่อสมาชิกได้', 'error');
            return;
        }
        
        const body = document.getElementById('members-body');
        body.innerHTML = '';
        
        // Batch create rows
        const rows = (data.members || []).map(m => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="p-2 border font-mono text-sm">${m.student_id}</td>
                <td class="p-2 border font-semibold">${m.name}</td>
                <td class="p-2 border text-center text-sm">${m.class_name}</td>
                <td class="p-2 border text-center text-xs text-gray-500">${new Date(m.created_at).toLocaleString('th-TH', { timeZone: 'Asia/Bangkok' })}</td>
                <td class="p-2 border text-center">
                    <button class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm" 
                            onclick="removeMember('${m.student_id}')" 
                            title="ลบสมาชิก">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`).join('');
            
        body.innerHTML = rows;
        
        // Update member count display
        const memberCount = data.members ? data.members.length : 0;
        document.querySelector('#members-modal h3').innerHTML = `รายชื่อสมาชิก (${memberCount} คน)`;
        
    } catch (error) {
        console.error('Error loading members:', error);
        showToast('เกิดข้อผิดพลาดในการโหลดรายชื่อสมาชิก', 'error');
    }
}

// Enhanced member removal with confirmation
async function removeMember(student_id) {
    try {
        const result = await Swal.fire({
            title: 'ลบสมาชิก?',
            text: `ต้องการลบสมาชิก ${student_id} หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        });
        
        if (!result.isConfirmed) return;
        
        const fd = new FormData();
        fd.append('action', 'remove_member');
        fd.append('id', currentActivityId);
        fd.append('student_id', student_id);
        
        const response = await fetch('../controllers/BestActivityController.php', { 
            method: 'POST', 
            body: fd 
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('ลบสมาชิกสำเร็จ', 'success');
            await loadMembers();
            
            // Clear cache and reload main list
            dataCache.list = null;
            await load();
        } else {
            showToast(data.message || 'ไม่สามารถลบสมาชิกได้', 'error');
        }
        
    } catch (error) {
        console.error('Error removing member:', error);
        showToast('เกิดข้อผิดพลาดในการลบสมาชิก', 'error');
    }
}

// Enhanced member addition with validation
async function addMember() {
    const student_id = document.getElementById('member-student-id').value.trim();
    if (!student_id) {
        showToast('กรุณากรอกรหัสนักเรียน', 'warning');
        return;
    }
    
    try {
        const fd = new FormData();
        fd.append('action', 'add_member');
        fd.append('id', currentActivityId);
        fd.append('student_id', student_id);
        
        const response = await fetch('../controllers/BestActivityController.php', { 
            method: 'POST', 
            body: fd 
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('เพิ่มสมาชิกสำเร็จ', 'success');
            document.getElementById('member-student-id').value = '';
            await loadMembers();
            
            // Clear cache and reload main list
            dataCache.list = null;
            await load();
        } else {
            showToast(data.message || 'ไม่สามารถเพิ่มสมาชิกได้', 'error');
        }
        
    } catch (error) {
        console.error('Error adding member:', error);
        showToast('เกิดข้อผิดพลาดในการเพิ่มสมาชิก', 'error');
    }
}

// Enhanced load function with loading state
async function load() {
    try {
        // Show loading state
        if (table) {
            table.clear().draw();
            $('#best-table_processing').show();
        }
        
        const data = await fetchList(false); // Force fresh data
        
        if (data.success && data.data) {
            render(data.data);
        } else {
            showToast('ไม่สามารถโหลดข้อมูลได้', 'error');
        }
        
    } catch (error) {
        console.error('Error loading data:', error);
        showToast('เกิดข้อผิดพลาดในการโหลดข้อมูล', 'error');
    } finally {
        if (table) {
            $('#best-table_processing').hide();
        }
    }
}

// events
 document.getElementById('btn-new').onclick = openCreate;
 document.getElementById('btn-cancel').onclick = () => document.getElementById('modal').classList.add('hidden');
 document.getElementById('btn-close-members').onclick = () => document.getElementById('members-modal').classList.add('hidden');
 document.getElementById('btn-add-member').onclick = addMember;
 document.getElementById('best-form').addEventListener('submit', function(e){
    e.preventDefault();
    const id = document.getElementById('activity_id').value;
    const fd = new FormData();
    fd.append('name', document.getElementById('name').value.trim());
    fd.append('description', document.getElementById('description').value.trim());
        const grades = Array.from(document.querySelectorAll('.grade-opt:checked')).map(cb=>cb.value);
        if (!grades.length) { showToast('กรุณาเลือกระดับชั้นอย่างน้อย 1 ระดับ','warning'); return; }
    fd.append('grade_levels', grades.join(','));
    fd.append('max_members', document.getElementById('max_members').value.trim());
    fd.append('action', id ? 'update':'create');
    if (id) fd.append('id', id);
    fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd })
            .then(r=>r.json()).then(d=>{
                if (!d.success) { showToast(d.message||'บันทึกไม่สำเร็จ','error'); }
                else { showToast(id ? 'แก้ไขสำเร็จ' : 'บันทึกสำเร็จ', 'success'); }
                document.getElementById('modal').classList.add('hidden');
                load();
            });
 });

 document.addEventListener('DOMContentLoaded', load);
</script>
</body>
</html>

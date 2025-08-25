<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'นักเรียน') {
    header('Location: ../login.php');
    exit;
}
$user = $_SESSION['user'];
$stu_grade = 'ม.' . ($user['Stu_major'] ?? '');

require_once('../classes/DatabaseClub.php');
require_once('../models/TermPee.php');

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0 text-blue-700 font-bold">สมัคร Best For Teen 2025</h1>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="bg-white rounded-lg shadow-lg p-6 border border-blue-200">
                    <!-- Registration Status Alert -->
                    <?php
                    // อ่านการตั้งค่าการสมัคร Best
                    $best_setting_file = '../best_regis_setting.json';
                    $registration_open = true;
                    $message = '';
                    $alert_class = 'bg-green-50 border-green-200 text-green-800';
                    
                    if (file_exists($best_setting_file)) {
                        $best_setting = json_decode(file_get_contents($best_setting_file), true);
                        if (isset($best_setting[$stu_grade])) {
                            $regis_start = $best_setting[$stu_grade]['regis_start'] ?? '';
                            $regis_end = $best_setting[$stu_grade]['regis_end'] ?? '';
                            
                            if ($regis_start && $regis_end) {
                                $now = new DateTime();
                                $start = new DateTime($regis_start);
                                $end = new DateTime($regis_end);
                                
                                if ($now < $start) {
                                    $registration_open = false;
                                    $message = 'การสมัครกิจกรรม Best สำหรับ ' . $stu_grade . ' จะเปิดในวันที่ ' . $start->format('d/m/Y เวลา H:i น.');
                                    $alert_class = 'bg-yellow-50 border-yellow-200 text-yellow-800';
                                } elseif ($now > $end) {
                                    $registration_open = false;
                                    $message = 'หมดเวลาการสมัครกิจกรรม Best สำหรับ ' . $stu_grade . ' แล้ว (ปิดรับสมัครเมื่อ ' . $end->format('d/m/Y เวลา H:i น.') . ')';
                                    $alert_class = 'bg-red-50 border-red-200 text-red-800';
                                } else {
                                    $message = '🎉 กำลังเปิดรับสมัครกิจกรรม Best สำหรับ ' . $stu_grade . ' (ปิดรับสมัครวันที่ ' . $end->format('d/m/Y เวลา H:i น.') . ')';
                                    $alert_class = 'bg-green-50 border-green-200 text-green-800';
                                }
                            }
                        }
                    }
                    
                    if ($message): ?>
                    <div class="mb-4 p-4 border rounded-lg <?php echo $alert_class; ?>">
                        <div class="flex items-center">
                            <div class="mr-3">
                                <?php if ($registration_open): ?>
                                    <span class="text-2xl">✅</span>
                                <?php elseif (strpos($alert_class, 'yellow') !== false): ?>
                                    <span class="text-2xl">⏰</span>
                                <?php else: ?>
                                    <span class="text-2xl">❌</span>
                                <?php endif; ?>
                            </div>
                            <p class="font-medium"><?php echo $message; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div id="status-box" class="mb-4"></div>
                    <div class="overflow-x-auto">
                        <table id="best-table" class="min-w-full border border-gray-200 rounded-lg shadow-sm bg-indigo-50">
                            <thead>
                                <tr class="bg-gradient-to-r from-indigo-200 to-indigo-100">
                                    <th class="py-3 px-4 text-center">#</th>
                                    <th class="py-3 px-4">กิจกรรม</th>
                                    <th class="py-3 px-4 text-center">ระดับชั้น</th>
                                    <th class="py-3 px-4 text-center">จำนวนที่รับ</th>
                                    <th class="py-3 px-4 text-center">สมัคร</th>
                                </tr>
                            </thead>
                            <tbody id="tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php');?>
</div>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let table;

function showToast(message, type = 'info') {
  const icon = ['success','error','warning','info','question'].includes(type) ? type : 'info';
  Swal.fire({ toast: true, position: 'top-end', icon, title: message, showConfirmButton: false, timer: 2000, timerProgressBar: true });
}

function loadStatus() {
  return fetch('../controllers/BestActivityController.php?action=my_status').then(r=>r.json());
}
function loadList() {
  return fetch('../controllers/BestActivityController.php?action=list').then(r=>r.json());
}

function renderStatus(data) {
  const box = document.getElementById('status-box');
  if (data.registered) {
    box.innerHTML = `<div class=\"p-3 rounded bg-green-50 border border-green-200\">คุณได้สมัครกิจกรรม <b>${data.data.name}</b> แล้ว</div>`;
  } else {
    box.innerHTML = `<div class=\"p-3 rounded bg-yellow-50 border border-yellow-200\">คุณยังไม่ได้สมัครกิจกรรม สามารถเลือกได้ 1 รายการ/ปี</div>`;
  }
}

function render(list, status) {
  if (!table) { table = $('#best-table').DataTable({ language: { search: 'ค้นหา:' } }); }
  table.clear();
  const tbody = document.getElementById('tbody');
  tbody.innerHTML = '';
  
  // Check if registration is open from PHP
  const registrationOpen = <?php echo $registration_open ? 'true' : 'false'; ?>;
  
  list.forEach(a => {
    const current = parseInt(a.current_members_count||0);
    const max = parseInt(a.max_members||0);
    const percent = max>0 ? Math.round((current/max)*100) : 0;
    const grades = (a.grade_levels||'');
    const disabled = percent>=100 || status.registered || !registrationOpen;
    
    let buttonText = 'สมัคร';
    if (!registrationOpen) {
      buttonText = 'ปิดรับสมัคร';
    } else if (percent >= 100) {
      buttonText = 'เต็มแล้ว';
    } else if (status.registered) {
      buttonText = 'สมัครแล้ว';
    }
    
    const row = `
      <tr>
        <td class=\"py-2 px-4 text-center\">${a.id}</td>
        <td class=\"py-2 px-4\">${a.name||''}</td>
        <td class=\"py-2 px-4 text-center\">${grades}</td>
        <td class=\"py-2 px-4 text-center\">${current} / ${max}</td>
        <td class=\"py-2 px-4 text-center\">
          <button class=\"apply btn btn-sm btn-primary\" data-id=\"${a.id}\" ${disabled?'disabled':''}>${buttonText}</button>
        </td>
      </tr>`;
    tbody.insertAdjacentHTML('beforeend', row);
  });
  table.rows.add($(tbody).find('tr')).draw(false);
}

function registerActivity(id) {
  const fd = new FormData();
  fd.append('action','add_member');
  fd.append('id', id);
  fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd })
    .then(r=>r.json()).then(d=>{
      if (!d.success) showToast(d.message||'สมัครไม่สำเร็จ','error');
      else showToast('สมัครสำเร็จ','success');
      init();
    });
}

function init() {
  Promise.all([loadStatus(), loadList()]).then(([status, list])=>{
    renderStatus(status);
    render(list.data||[], status);
    document.querySelectorAll('.apply').forEach(btn=>{
      btn.addEventListener('click', ()=> registerActivity(btn.dataset.id));
    });
  });
}

document.addEventListener('DOMContentLoaded', init);
</script>
<?php require_once('script.php'); ?>
</body>
</html>

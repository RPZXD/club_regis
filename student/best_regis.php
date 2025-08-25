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
let isRegistering = false;
let cachedData = null;
let lastFetch = 0;
const CACHE_DURATION = 30000; // 30 seconds

function showToast(message, type = 'info') {
  const icon = ['success','error','warning','info','question'].includes(type) ? type : 'info';
  Swal.fire({ toast: true, position: 'top-end', icon, title: message, showConfirmButton: false, timer: 2000, timerProgressBar: true });
}

function showLoading() {
    if (!document.getElementById('loading-overlay')) {
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50';
        overlay.innerHTML = '<div class="bg-white p-4 rounded shadow"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><div class="mt-2 text-sm">กำลังโหลด...</div></div>';
        document.body.appendChild(overlay);
    }
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.remove();
}

async function loadStatus() {
  const now = Date.now();
  if (cachedData && cachedData.status && (now - lastFetch < CACHE_DURATION)) {
    return cachedData.status;
  }
  
  try {
    const response = await fetch('../controllers/BestActivityController.php?action=my_status');
    const data = await response.json();
    if (!cachedData) cachedData = {};
    cachedData.status = data;
    return data;
  } catch (error) {
    showToast('เกิดข้อผิดพลาดในการโหลดสถานะ', 'error');
    return { success: false, registered: false };
  }
}

async function loadList() {
  const now = Date.now();
  if (cachedData && cachedData.list && (now - lastFetch < CACHE_DURATION)) {
    return cachedData.list;
  }
  
  try {
    const response = await fetch('../controllers/BestActivityController.php?action=list');
    const data = await response.json();
    if (!cachedData) cachedData = {};
    cachedData.list = data;
    lastFetch = now;
    return data;
  } catch (error) {
    showToast('เกิดข้อผิดพลาดในการโหลดรายการ', 'error');
    return { success: false, data: [] };
  }
}

function renderStatus(data) {
  const box = document.getElementById('status-box');
  if (data.registered) {
    box.innerHTML = `<div class="p-3 rounded bg-green-50 border border-green-200">คุณได้สมัครกิจกรรม <b>${data.data.name}</b> แล้ว</div>`;
  } else {
    box.innerHTML = `<div class="p-3 rounded bg-yellow-50 border border-yellow-200">คุณยังไม่ได้สมัครกิจกรรม สามารถเลือกได้ 1 รายการ/ปี</div>`;
  }
}

function render(list, status) {
  if (!table) { 
    table = $('#best-table').DataTable({ 
      language: { search: 'ค้นหา:' },
      pageLength: 15,
      processing: false,
      deferRender: true,
      drawCallback: function() {
        // Re-attach event listeners after each draw/page change
        attachEventListeners();
      }
    }); 
  }
  
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
    let buttonClass = 'apply btn btn-sm btn-primary';
    
    if (!registrationOpen) {
      buttonText = 'ปิดรับสมัคร';
      buttonClass = 'btn btn-sm btn-secondary';
    } else if (percent >= 100) {
      buttonText = 'เต็มแล้ว';
      buttonClass = 'btn btn-sm btn-danger';
    } else if (status.registered) {
      buttonText = 'สมัครแล้ว';
      buttonClass = 'btn btn-sm btn-success';
    }
    
    // Progress bar
    let barColor = 'bg-green-400';
    if (percent >= 100) barColor = 'bg-red-500';
    else if (percent >= 70) barColor = 'bg-yellow-400';
    
    const progressBar = `
      <div class="w-24 mx-auto">
        <div class="relative h-4 bg-gray-200 rounded-full overflow-hidden">
          <div class="absolute left-0 top-0 h-4 ${barColor}" style="width:${Math.min(percent, 100)}%"></div>
          <div class="absolute w-full text-xs text-center top-0 left-0 h-4 leading-4">${current}/${max}</div>
        </div>
      </div>`;
    
    const row = `
      <tr class="hover:bg-blue-50">
        <td class="py-2 px-4 text-center font-medium">${a.id}</td>
        <td class="py-2 px-4">
          <div class="font-medium text-blue-800">${a.name||''}</div>
          ${a.description ? `<div class="text-sm text-gray-600">${a.description}</div>` : ''}
        </td>
        <td class="py-2 px-4 text-center">
          <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">${grades}</span>
        </td>
        <td class="py-2 px-4 text-center">${progressBar}</td>
        <td class="py-2 px-4 text-center">
          <button class="${buttonClass}" data-id="${a.id}" ${disabled?'disabled':''}>${buttonText}</button>
        </td>
      </tr>`;
    tbody.insertAdjacentHTML('beforeend', row);
  });
  
  table.rows.add($(tbody).find('tr')).draw(false);
}

// Function to attach event listeners using event delegation
function attachEventListeners() {
  // Remove any existing delegated listeners to prevent duplicates
  $(document).off('click.bestRegister', '.apply');
  
  // Use event delegation to handle clicks on apply buttons
  $(document).on('click.bestRegister', '.apply', function(e) {
    e.preventDefault();
    const button = $(this)[0];
    
    if (!button.disabled && !isRegistering) {
      const activityId = button.getAttribute('data-id');
      if (activityId) {
        registerActivity(activityId);
      }
    }
  });
}

async function registerActivity(id) {
  if (isRegistering) return;
  
  // Confirm registration
  const result = await Swal.fire({
    title: 'ยืนยันการสมัคร',
    text: 'คุณต้องการสมัครกิจกรรมนี้ใช่หรือไม่?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'ใช่, สมัคร',
    cancelButtonText: 'ยกเลิก'
  });
  
  if (!result.isConfirmed) return;
  
  isRegistering = true;
  showLoading();
  
  try {
    const fd = new FormData();
    fd.append('action','register');
    fd.append('activity_id', id);
    
    const response = await fetch('../controllers/BestActivityController.php', { 
      method: 'POST', 
      body: fd 
    });
    const data = await response.json();
    
    if (!data.success) {
      showToast(data.message||'สมัครไม่สำเร็จ','error');
    } else {
      showToast('สมัครสำเร็จ! 🎉','success');
      // Clear cache to force refresh
      cachedData = null;
      await init();
    }
  } catch (error) {
    showToast('เกิดข้อผิดพลาดในการสมัคร','error');
  } finally {
    isRegistering = false;
    hideLoading();
  }
}

async function init() {
  showLoading();
  try {
    const [status, list] = await Promise.all([loadStatus(), loadList()]);
    renderStatus(status);
    render(list.data||[], status);
    
    // Event delegation will handle the button clicks automatically
    // No need to manually attach listeners here anymore
    
  } catch (error) {
    showToast('เกิดข้อผิดพลาดในการโหลดข้อมูล','error');
  } finally {
    hideLoading();
  }
}

// Auto refresh every 2 minutes
setInterval(() => {
  cachedData = null;
  init();
}, 120000);

// Initialize event delegation on page load
document.addEventListener('DOMContentLoaded', () => {
  attachEventListeners();
  init();
});
</script>
<?php require_once('script.php'); ?>
</body>
</html>

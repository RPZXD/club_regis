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

// Check registration time settings
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
                <!-- Main Card with Enhanced Design -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl shadow-2xl p-8 border-2 border-blue-200 backdrop-blur-sm">
                    <!-- Enhanced Header -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full mb-4 shadow-lg">
                            <i class="fas fa-star text-white text-2xl"></i>
                        </div>
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent">
                            เลือกกิจกรรม Best For Teen 2025
                        </h2>
                        <p class="text-gray-600 mt-2">สร้างประสบการณ์ใหม่และพัฒนาศักยภาพของคุณ</p>
                    </div>

                    <!-- Registration Status Alert with Enhanced Design -->
                    <?php if ($message): ?>
                    <div class="mb-6 p-6 border-2 rounded-2xl <?php echo $alert_class; ?> shadow-lg transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center">
                            <div class="mr-4 flex-shrink-0">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center <?php echo $registration_open ? 'bg-green-200' : (strpos($alert_class, 'yellow') !== false ? 'bg-yellow-200' : 'bg-red-200'); ?>">
                                    <?php if ($registration_open): ?>
                                        <span class="text-3xl animate-bounce">✅</span>
                                    <?php elseif (strpos($alert_class, 'yellow') !== false): ?>
                                        <span class="text-3xl animate-pulse">⏰</span>
                                    <?php else: ?>
                                        <span class="text-3xl">❌</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-lg"><?php echo $message; ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Enhanced Status Box -->
                    <div id="status-box" class="mb-6"></div>
                    
                    <!-- Enhanced Table Container -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-6">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-list-ul mr-3"></i>
                                รายการกิจกรรมที่เปิดรับสมัคร
                            </h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table id="best-table" class="min-w-full">
                                <thead>
                                    <tr class="bg-gradient-to-r from-gray-100 to-gray-50">
                                        <th class="py-4 px-6 text-center font-bold text-gray-700 border-b-2 border-gray-200">
                                            <i class="fas fa-hashtag mr-2"></i>รหัส
                                        </th>
                                        <th class="py-4 px-6 font-bold text-gray-700 border-b-2 border-gray-200">
                                            <i class="fas fa-star mr-2"></i>ชื่อกิจกรรม
                                        </th>
                                        <th class="py-4 px-6 text-center font-bold text-gray-700 border-b-2 border-gray-200">
                                            <i class="fas fa-graduation-cap mr-2"></i>ระดับชั้น
                                        </th>
                                        <th class="py-4 px-6 text-center font-bold text-gray-700 border-b-2 border-gray-200">
                                            <i class="fas fa-users mr-2"></i>จำนวนที่รับ
                                        </th>
                                        <th class="py-4 px-6 text-center font-bold text-gray-700 border-b-2 border-gray-200">
                                            <i class="fas fa-hand-point-up mr-2"></i>สมัคร
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tbody"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Enhanced Footer -->
                    <div class="mt-8 text-center">
                        <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full text-white shadow-lg">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span class="font-medium">เลือกได้เพียง 1 กิจกรรมต่อปีการศึกษา</span>
                        </div>
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
    box.innerHTML = `
      <div class="p-6 rounded-2xl bg-gradient-to-r from-green-100 to-emerald-100 border-2 border-green-300 shadow-lg transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center">
          <div class="w-16 h-16 bg-green-200 rounded-full flex items-center justify-center mr-4 animate-pulse">
            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
          </div>
          <div>
            <h4 class="text-xl font-bold text-green-800 mb-2">สมัครสำเร็จแล้ว! 🎉</h4>
            <p class="text-green-700">คุณได้สมัครกิจกรรม <span class="font-bold bg-green-200 px-2 py-1 rounded">${data.data.name}</span> แล้ว</p>
          </div>
        </div>
      </div>`;
  } else {
    box.innerHTML = `
      <div class="p-6 rounded-2xl bg-gradient-to-r from-yellow-100 to-amber-100 border-2 border-yellow-300 shadow-lg transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center">
          <div class="w-16 h-16 bg-yellow-200 rounded-full flex items-center justify-center mr-4 animate-bounce">
            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
          </div>
          <div>
            <h4 class="text-xl font-bold text-yellow-800 mb-2">ยังไม่ได้สมัครกิจกรรม</h4>
            <p class="text-yellow-700">เลือกกิจกรรมที่คุณสนใจและสมัครได้เลย! (เลือกได้เพียง 1 รายการต่อปี)</p>
          </div>
        </div>
      </div>`;
  }
}

function render(list, status) {
  if (!table) { 
    table = $('#best-table').DataTable({ 
      language: { 
        search: '<i class="fas fa-search mr-2"></i>ค้นหา:',
        lengthMenu: "แสดง _MENU_ รายการ",
        info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
        paginate: {
          first: "หน้าแรก",
          last: "หน้าสุดท้าย",
          next: "ถัดไป",
          previous: "ก่อนหน้า"
        }
      },
      ordering: false,
      paging: true,
      pageLength: 10,
      info: true,
      dom: '<"flex flex-col md:flex-row justify-between items-center mb-4"<"mb-2 md:mb-0"l><"mb-2 md:mb-0"f>>rtip',
      drawCallback: function() {
        // Add hover effects to table rows
        $('#best-table tbody tr').addClass('hover:bg-blue-50 transition-colors duration-200');
      }
    }); 
  }
  table.clear();
  
  const registrationOpen = <?php echo $registration_open ? 'true' : 'false'; ?>;
  
  list.forEach((a, index) => {
    const current = parseInt(a.current_members_count||0);
    const max = parseInt(a.max_members||0);
    const percent = max>0 ? Math.round((current/max)*100) : 0;
    const grades = (a.grade_levels||'');
    const disabled = percent>=100 || status.registered || !registrationOpen;
    
    let buttonText = 'สมัคร';
    let buttonClass = 'apply px-6 py-3 rounded-full font-bold transition-all duration-300 transform hover:scale-105 shadow-lg';
    let buttonIcon = 'fas fa-hand-point-up';
    
    if (!registrationOpen) {
      buttonText = 'ปิดรับสมัคร';
      buttonClass += ' bg-gray-400 text-white cursor-not-allowed';
      buttonIcon = 'fas fa-ban';
    } else if (percent >= 100) {
      buttonText = 'เต็มแล้ว';
      buttonClass += ' bg-red-500 text-white cursor-not-allowed';
      buttonIcon = 'fas fa-users-slash';
    } else if (status.registered) {
      buttonText = 'สมัครแล้ว';
      buttonClass += ' bg-green-500 text-white cursor-not-allowed';
      buttonIcon = 'fas fa-check';
    } else {
      buttonClass += ' bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 hover:shadow-xl';
    }
    
    // Progress bar with enhanced design
    const progressBarColor = percent >= 100 ? 'bg-red-500' : percent >= 70 ? 'bg-yellow-500' : 'bg-green-500';
    const progressBar = `
      <div class="w-full max-w-xs mx-auto">
        <div class="flex justify-between items-center mb-2">
          <span class="text-sm font-medium text-gray-700">${current}/${max}</span>
          <span class="text-sm font-medium text-gray-700">${percent}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner">
          <div class="${progressBarColor} h-3 rounded-full transition-all duration-500 ease-out shadow-sm" 
               style="width: ${percent}%"></div>
        </div>
      </div>`;
    
    const buttonHtml = disabled ? 
      `<button class="${buttonClass}" disabled>
        <i class="${buttonIcon} mr-2"></i>${buttonText}
       </button>` :
      `<button class="${buttonClass}" data-id="${a.id}">
        <i class="${buttonIcon} mr-2"></i>${buttonText}
       </button>`;
    
    // Enhanced table row with better styling
    table.row.add([
      `<span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 rounded-full font-bold">${a.id}</span>`,
      `<div class="font-semibold text-gray-800 hover:text-blue-600 transition-colors duration-200">
        <i class="fas fa-star text-yellow-500 mr-2"></i>${a.name || ''
       }</div>`,
      `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
        ${grades}
       </span>`,
      progressBar,
      `<div class="text-center">${buttonHtml}</div>`
    ]);
  });
  table.draw(false);
}

function registerActivity(id) {
  Swal.fire({
    title: '<span class="text-blue-600">ยืนยันการสมัคร</span>',
    html: '<div class="text-gray-600"><i class="fas fa-question-circle text-blue-500 mr-2"></i>คุณต้องการสมัครกิจกรรมนี้หรือไม่?</div>',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-check mr-2"></i>สมัคร',
    cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
    confirmButtonColor: '#3B82F6',
    cancelButtonColor: '#6B7280',
    background: '#F8FAFC',
    customClass: {
      popup: 'rounded-2xl shadow-2xl',
      confirmButton: 'rounded-full px-6 py-3 font-bold',
      cancelButton: 'rounded-full px-6 py-3 font-bold'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading
      Swal.fire({
        title: 'กำลังสมัคร...',
        html: '<div class="flex justify-center"><div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div></div>',
        showConfirmButton: false,
        allowOutsideClick: false
      });

      const fd = new FormData();
      fd.append('action','register');
      fd.append('activity_id', id);
      fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd })
        .then(r=>r.json()).then(d=>{
          if (!d.success) {
            showToast(d.message||'สมัครไม่สำเร็จ','error');
          } else {
            Swal.fire({
              title: 'สมัครสำเร็จ! 🎉',
              text: d.message||'ยินดีด้วย! คุณได้สมัครกิจกรรมเรียบร้อยแล้ว',
              icon: 'success',
              confirmButtonText: 'รับทราบ',
              confirmButtonColor: '#10B981',
              customClass: {
                popup: 'rounded-2xl shadow-2xl',
                confirmButton: 'rounded-full px-6 py-3 font-bold'
              }
            });
          }
          init();
        });
    }
  });
}

function init() {
  Promise.all([loadStatus(), loadList()]).then(([status, list])=>{
    renderStatus(status);
    render(list.data||[], status);
  });
}

// Use event delegation to handle button clicks
$(document).on('click', '.apply', function() {
  const activityId = $(this).data('id');
  registerActivity(activityId);
});

document.addEventListener('DOMContentLoaded', init);
</script>

<!-- Add Font Awesome and additional CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
  /* Custom animations and enhancements */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .fade-in-up {
    animation: fadeInUp 0.6s ease-out;
  }
  
  /* DataTables custom styling */
  .dataTables_wrapper .dataTables_filter input {
    border: 2px solid #E5E7EB;
    border-radius: 1rem;
    padding: 0.5rem 1rem;
    margin-left: 0.5rem;
    transition: border-color 0.3s ease;
  }
  
  .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #3B82F6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
  
  .dataTables_wrapper .dataTables_length select {
    border: 2px solid #E5E7EB;
    border-radius: 0.5rem;
    padding: 0.5rem;
    margin: 0 0.5rem;
  }
  
  /* Table row hover effects */
  #best-table tbody tr {
    transition: all 0.3s ease;
  }
  
  #best-table tbody tr:hover {
    background: linear-gradient(90deg, #EBF4FF 0%, #DBEAFE 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  }
  
  /* Progress bar animations */
  @keyframes progressFill {
    from { width: 0%; }
  }
  
  .progress-animate {
    animation: progressFill 1s ease-out;
  }
</style>
<?php require_once('script.php'); ?>
</body>
</html>

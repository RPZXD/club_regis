<?php 
session_start();
// เช็ค session และ role
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}
// Read configuration from JSON file
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

require_once('header.php');

?>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<style>
.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.badge {
    font-size: 0.875em;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.alert-info {
    background-color: #e3f2fd;
    border-color: #2196f3;
    color: #1976d2;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.text-muted.small {
    font-size: 0.875rem;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-success {
    background-color: #28a745;
}

.badge-danger {
    background-color: #dc3545;
}

.badge-secondary {
    background-color: #6c757d;
}

/* Animation for status updates */
.badge {
    transition: all 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .btn-lg {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }
}

/* Timeline styles */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 3px #007bff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px 20px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-weight: 600;
    color: #495057;
}

.timeline-subtitle {
    margin: 0 0 10px 0;
    font-size: 0.875rem;
}

.timeline-details {
    background: #fff;
    padding: 10px 15px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.timeline-details small {
    line-height: 1.5;
    margin: 2px 0;
}
</style>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?php echo $global['nameschool']; ?> <span class="text-blue-600">| เจ้าหน้าที่</span></h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid px-4">
            <!-- Hero Section -->
            <div class="mb-8">
                <div class="bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700 rounded-2xl p-8 text-white shadow-2xl animate__animated animate__fadeInDown">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold mb-2">⚙️ ตั้งค่าวันเวลาสมัครชุมนุม</h2>
                            <p class="text-blue-100 text-lg">จัดการช่วงเวลาเปิด-ปิดรับสมัครสำหรับแต่ละระดับชั้น</p>
                        </div>
                        <div class="hidden md:block">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
                                <div class="text-2xl">📅</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Form -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 animate__animated animate__fadeInUp">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-8 py-6">
                    <h3 class="text-2xl font-bold text-white flex items-center">
                        <span class="mr-3">🎯</span>
                        การตั้งค่าวันเวลา
                    </h3>
                </div>
                <div class="p-8">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-8 rounded-r-lg">
                        <div class="flex items-center">
                            <div class="text-blue-400 mr-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-blue-800 font-medium">กำหนดวันเวลาในการเปิดและปิดรับสมัครชุมนุมสำหรับแต่ละระดับชั้น</p>
                        </div>
                    </div>
                    
                    <form id="settingForm">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <?php
                            // อ่านข้อมูลการตั้งค่าจากไฟล์ JSON
                            $regis_setting = json_decode(file_get_contents('../regis_setting.json'), true);
                            $levels = ['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'];
                            $colors = [
                                'ม.1' => ['from-red-400', 'to-pink-500', 'red'],
                                'ม.2' => ['from-yellow-400', 'to-orange-500', 'orange'], 
                                'ม.3' => ['from-green-400', 'to-emerald-500', 'green'],
                                'ม.4' => ['from-blue-400', 'to-cyan-500', 'blue'],
                                'ม.5' => ['from-purple-400', 'to-violet-500', 'purple'],
                                'ม.6' => ['from-indigo-400', 'to-blue-500', 'indigo']
                            ];
                            
                            foreach ($levels as $index => $level):
                                $regis_start = isset($regis_setting[$level]['regis_start']) ? $regis_setting[$level]['regis_start'] : '';
                                $regis_end = isset($regis_setting[$level]['regis_end']) ? $regis_setting[$level]['regis_end'] : '';
                                
                                // แปลงรูปแบบวันที่สำหรับ input datetime-local
                                $start_datetime = $regis_start ? date('Y-m-d\TH:i', strtotime($regis_start)) : '';
                                $end_datetime = $regis_end ? date('Y-m-d\TH:i', strtotime($regis_end)) : '';
                                $color = $colors[$level];
                            ?>
                            <div class="group hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                <div class="bg-gradient-to-br <?php echo $color[0] . ' ' . $color[1]; ?> rounded-2xl p-1">
                                    <div class="bg-white rounded-xl p-6 h-full">
                                        <div class="flex items-center justify-between mb-6">
                                            <h4 class="text-2xl font-bold text-gray-800 flex items-center">
                                                <span class="w-10 h-10 bg-gradient-to-br <?php echo $color[0] . ' ' . $color[1]; ?> rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                    <?php echo substr($level, -1); ?>
                                                </span>
                                                ระดับชั้น <?php echo $level; ?>
                                            </h4>
                                            <div class="text-2xl">🎓</div>
                                        </div>
                                        
                                        <div class="space-y-6">
                                            <div class="relative">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                    <span class="mr-2">🚀</span>
                                                    วันเวลาเปิดรับสมัคร
                                                </label>
                                                <input type="datetime-local" 
                                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-<?php echo $color[2]; ?>-400 focus:ring-4 focus:ring-<?php echo $color[2]; ?>-100 transition-all duration-300 bg-gray-50 hover:bg-white" 
                                                       id="regis_start_<?php echo $level; ?>" 
                                                       name="regis_start[<?php echo $level; ?>]" 
                                                       value="<?php echo $start_datetime; ?>">
                                            </div>
                                            
                                            <div class="relative">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                    <span class="mr-2">🏁</span>
                                                    วันเวลาปิดรับสมัคร
                                                </label>
                                                <input type="datetime-local" 
                                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-<?php echo $color[2]; ?>-400 focus:ring-4 focus:ring-<?php echo $color[2]; ?>-100 transition-all duration-300 bg-gray-50 hover:bg-white" 
                                                       id="regis_end_<?php echo $level; ?>" 
                                                       name="regis_end[<?php echo $level; ?>]" 
                                                       value="<?php echo $end_datetime; ?>">
                                            </div>
                                            
                                            <div class="bg-gray-50 rounded-xl p-4">
                                                <div class="flex items-center text-sm">
                                                    <div class="mr-2">⏰</div>
                                                    <?php if ($regis_start && $regis_end): ?>
                                                        <?php
                                                        $now = new DateTime();
                                                        $start = new DateTime($regis_start);
                                                        $end = new DateTime($regis_end);
                                                        
                                                        if ($now < $start) {
                                                            echo '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                                    <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                                                                    ยังไม่เปิดรับสมัคร
                                                                  </span>';
                                                        } elseif ($now >= $start && $now <= $end) {
                                                            echo '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 animate-pulse">
                                                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                                                    กำลังเปิดรับสมัคร
                                                                  </span>';
                                                        } else {
                                                            echo '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                                    <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                                                    ปิดรับสมัครแล้ว
                                                                  </span>';
                                                        }
                                                        ?>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                                            ยังไม่ได้ตั้งค่า
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-12">
                            <button type="submit" class="group bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                                <span class="mr-3 group-hover:scale-110 transition-transform duration-300">💾</span>
                                บันทึกการตั้งค่า
                                <span class="ml-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">✨</span>
                            </button>
                            <button type="button" class="group bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white font-bold py-4 px-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center" onclick="resetForm()">
                                <span class="mr-3 group-hover:scale-110 transition-transform duration-300">🔄</span>
                                รีเซ็ต
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Status Overview Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-6">
                    <h3 class="text-2xl font-bold text-white flex items-center">
                        <span class="mr-3">📊</span>
                        สถานะการรับสมัครปัจจุบัน
                    </h3>
                </div>
                <div class="p-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider rounded-l-xl">ระดับชั้น</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">วันเวลาเปิดรับสมัคร</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">วันเวลาปิดรับสมัคร</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">สถานะ</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider rounded-r-xl">เวลาคงเหลือ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php 
                                $statusColors = [
                                    'ม.1' => 'border-l-red-400',
                                    'ม.2' => 'border-l-orange-400', 
                                    'ม.3' => 'border-l-green-400',
                                    'ม.4' => 'border-l-blue-400',
                                    'ม.5' => 'border-l-purple-400',
                                    'ม.6' => 'border-l-indigo-400'
                                ];
                                foreach ($levels as $index => $level): 
                                    $regis_start = isset($regis_setting[$level]['regis_start']) ? $regis_setting[$level]['regis_start'] : '';
                                    $regis_end = isset($regis_setting[$level]['regis_end']) ? $regis_setting[$level]['regis_end'] : '';
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-300 border-l-4 <?php echo $statusColors[$level]; ?> animate__animated animate__fadeInUp" style="animation-delay: <?php echo ($index + 3) * 0.1; ?>s">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br <?php echo $colors[$level][0] . ' ' . $colors[$level][1]; ?> rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                <?php echo substr($level, -1); ?>
                                            </div>
                                            <span class="text-lg font-semibold text-gray-900"><?php echo $level; ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        <?php if ($regis_start): ?>
                                            <div class="flex items-center">
                                                <span class="mr-2">📅</span>
                                                <?php echo date('d/m/Y', strtotime($regis_start)); ?>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-500">
                                                <span class="mr-2">⏰</span>
                                                <?php echo date('H:i', strtotime($regis_start)); ?> น.
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        <?php if ($regis_end): ?>
                                            <div class="flex items-center">
                                                <span class="mr-2">📅</span>
                                                <?php echo date('d/m/Y', strtotime($regis_end)); ?>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-500">
                                                <span class="mr-2">⏰</span>
                                                <?php echo date('H:i', strtotime($regis_end)); ?> น.
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($regis_start && $regis_end): ?>
                                            <?php
                                            $now = new DateTime();
                                            $start = new DateTime($regis_start);
                                            $end = new DateTime($regis_end);
                                            
                                            if ($now < $start) {
                                                echo '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse"></span>
                                                        ยังไม่เปิดรับสมัคร
                                                      </span>';
                                            } elseif ($now >= $start && $now <= $end) {
                                                echo '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                                                        เปิดรับสมัคร
                                                      </span>';
                                            } else {
                                                echo '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200">
                                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                                        ปิดรับสมัครแล้ว
                                                      </span>';
                                            }
                                            ?>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                                ยังไม่ได้ตั้งค่า
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($regis_start && $regis_end): ?>
                                            <div class="text-sm">
                                                <?php
                                                $now = new DateTime();
                                                $start = new DateTime($regis_start);
                                                $end = new DateTime($regis_end);
                                                
                                                if ($now < $start) {
                                                    $diff = $now->diff($start);
                                                    echo '<div class="text-yellow-600 font-medium">' . $diff->days . ' วัน ' . $diff->h . ' ชั่วโมง</div>';
                                                    echo '<div class="text-gray-500 text-xs">(จนเปิดรับสมัคร)</div>';
                                                } elseif ($now >= $start && $now <= $end) {
                                                    $diff = $now->diff($end);
                                                    echo '<div class="text-green-600 font-medium">' . $diff->days . ' วัน ' . $diff->h . ' ชั่วโมง</div>';
                                                    echo '<div class="text-gray-500 text-xs">(จนปิดรับสมัคร)</div>';
                                                } else {
                                                    echo '<div class="text-red-600 font-medium">หมดเวลาแล้ว</div>';
                                                }
                                                ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Activity Logs -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                <div class="bg-gradient-to-r from-violet-500 to-purple-600 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-white flex items-center">
                            <span class="mr-3">📝</span>
                            ประวัติการเปลี่ยนแปลงการตั้งค่า
                        </h3>
                        <button type="button" class="bg-white/20 hover:bg-white/30 text-white font-medium py-2 px-4 rounded-xl transition-all duration-300 flex items-center" onclick="loadSettingLogs()">
                            <span class="mr-2">🔄</span>
                            รีเฟรช
                        </button>
                    </div>
                </div>
                <div class="p-8">
                    <div id="logsContainer">
                        <div class="flex items-center justify-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-purple-200 border-t-purple-600"></div>
                            <span class="ml-4 text-gray-600 font-medium">กำลังโหลด...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('../footer.php');?>
</div>
<!-- ./wrapper -->


<script>
$(document).ready(function() {
    // โหลดประวัติการเปลี่ยนแปลงเมื่อหน้าเว็บโหลดเสร็จ
    loadSettingLogs();
    
    $('#settingForm').on('submit', function(e) {
        e.preventDefault();
        
        // ตรวจสอบข้อมูลก่อนส่ง
        let isValid = true;
        let errorMessages = [];
        
        $('.form-control').each(function() {
            let level = $(this).attr('name').match(/\[(.*?)\]/)[1];
            let fieldType = $(this).attr('name').includes('regis_start') ? 'start' : 'end';
            
            if (fieldType === 'end') {
                let startField = $('input[name="regis_start[' + level + ']"]');
                let endField = $(this);
                
                if (startField.val() && endField.val()) {
                    let startDate = new Date(startField.val());
                    let endDate = new Date(endField.val());
                    
                    if (endDate <= startDate) {
                        isValid = false;
                        errorMessages.push('วันปิดรับสมัครของ ' + level + ' ต้องมากกว่าวันเปิดรับสมัคร');
                    }
                }
            }
        });
        
        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อมูลไม่ถูกต้อง',
                html: errorMessages.join('<br>'),
                confirmButtonText: 'ตกลง'
            });
            return;
        }
        
        // แสดง loading
        Swal.fire({
            title: 'กำลังบันทึก...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // ส่งข้อมูล
        $.ajax({
            url: 'api/save_regis_setting.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ',
                        text: 'การตั้งค่าได้รับการบันทึกเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message || 'ไม่สามารถบันทึกข้อมูลได้',
                        confirmButtonText: 'ตกลง'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    });
});

function resetForm() {
    Swal.fire({
        title: 'ยืนยันการรีเซ็ต',
        text: 'คุณต้องการรีเซ็ตข้อมูลในฟอร์มใช่หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, รีเซ็ต',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('settingForm').reset();
        }
    });
}

// อัปเดตสถานะแบบ real-time ทุก 1 นาที
setInterval(function() {
    location.reload();
}, 60000);

function loadSettingLogs() {
    $('#logsContainer').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลด...</div>');
    
    $.ajax({
        url: 'api/get_setting_logs.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayLogs(response.logs);
            } else {
                $('#logsContainer').html('<div class="alert alert-warning">ไม่สามารถโหลดประวัติได้: ' + response.message + '</div>');
            }
        },
        error: function() {
            $('#logsContainer').html('<div class="alert alert-danger">เกิดข้อผิดพลาดในการโหลดประวัติ</div>');
        }
    });
}

function displayLogs(logs) {
    if (logs.length === 0) {
        $('#logsContainer').html('<div class="alert alert-info">ยังไม่มีประวัติการเปลี่ยนแปลง</div>');
        return;
    }
    
    let html = '<div class="timeline">';
    
    logs.forEach(function(log, index) {
        let date = new Date(log.timestamp);
        let dateStr = date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        html += '<div class="timeline-item">';
        html += '<div class="timeline-marker bg-primary"></div>';
        html += '<div class="timeline-content">';
        html += '<h6 class="timeline-title">การเปลี่ยนแปลงการตั้งค่า</h6>';
        html += '<p class="timeline-subtitle text-muted">โดย: ' + log.user + ' | ' + dateStr + '</p>';
        
        if (log.data) {
            html += '<div class="timeline-details">';
            Object.keys(log.data).forEach(function(level) {
                if (log.data[level].regis_start && log.data[level].regis_end) {
                    let startDate = new Date(log.data[level].regis_start);
                    let endDate = new Date(log.data[level].regis_end);
                    
                    html += '<small class="d-block">';
                    html += '<strong>' + level + ':</strong> ';
                    html += startDate.toLocaleDateString('th-TH') + ' ' + startDate.toLocaleTimeString('th-TH', {hour: '2-digit', minute: '2-digit'});
                    html += ' ถึง ';
                    html += endDate.toLocaleDateString('th-TH') + ' ' + endDate.toLocaleTimeString('th-TH', {hour: '2-digit', minute: '2-digit'});
                    html += '</small>';
                }
            });
            html += '</div>';
        }
        
        html += '</div></div>';
    });
    
    html += '</div>';
    
    $('#logsContainer').html(html);
}
</script>
<?php require_once('script.php');?>
</body>
</html>

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [];
    $levels = ['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'];
    
    foreach ($levels as $level) {
        $start_date = $_POST[$level . '_start_date'] ?? '';
        $start_time = $_POST[$level . '_start_time'] ?? '';
        $end_date = $_POST[$level . '_end_date'] ?? '';
        $end_time = $_POST[$level . '_end_time'] ?? '';
        
        if ($start_date && $start_time && $end_date && $end_time) {
            $settings[$level] = [
                'regis_start' => $start_date . ' ' . $start_time . ':00',
                'regis_end' => $end_date . ' ' . $end_time . ':59'
            ];
        }
    }
    
    if (!empty($settings)) {
        $json = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents('../regis_setting.json', $json)) {
            $success_message = "บันทึกการตั้งค่าเรียบร้อยแล้ว";
        } else {
            $error_message = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
        }
    }
}

// Read current settings
$current_settings = [];
if (file_exists('../regis_setting.json')) {
    $current_settings = json_decode(file_get_contents('../regis_setting.json'), true) ?? [];
}

require_once('header.php');
?>

<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?php echo $global['nameschool']; ?> <span class="text-blue-600">| ตั้งค่าวันเวลาสมัครชุมนุม</span></h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <section class="content">
            <div class="container-fluid">
                
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <!-- คำแนะนำการใช้งาน -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> คำแนะนำการใช้งาน</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> กำหนดวันเวลาเปิดและปิดรับสมัครชุมนุมสำหรับแต่ละระดับชั้น</li>
                            <li><i class="fas fa-check text-success"></i> วันและเวลาจะมีผลทันทีหลังจากบันทึก</li>
                            <li><i class="fas fa-exclamation-triangle text-warning"></i> กรุณาตรวจสอบวันเวลาให้ถูกต้องก่อนบันทึก</li>
                            <li><i class="fas fa-clock text-info"></i> รูปแบบเวลา: 24 ชั่วโมง (เช่น 08:00, 15:30)</li>
                        </ul>
                    </div>
                </div>

                <!-- ฟอร์มตั้งค่า -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cog"></i> ตั้งค่าวันเวลาสมัครชุมนุม</h3>
                    </div>
                    <form method="POST" id="settingForm">
                        <div class="card-body">
                            <?php 
                            $levels = [
                                'ม.1' => 'มัธยมศึกษาปีที่ 1',
                                'ม.2' => 'มัธยมศึกษาปีที่ 2', 
                                'ม.3' => 'มัธยมศึกษาปีที่ 3',
                                'ม.4' => 'มัธยมศึกษาปีที่ 4',
                                'ม.5' => 'มัธยมศึกษาปีที่ 5',
                                'ม.6' => 'มัธยมศึกษาปีที่ 6'
                            ];
                            
                            foreach ($levels as $level_code => $level_name): 
                                $current = $current_settings[$level_code] ?? null;
                                $start_datetime = '';
                                $end_datetime = '';
                                $start_date = '';
                                $start_time = '';
                                $end_date = '';
                                $end_time = '';
                                
                                if ($current) {
                                    if (isset($current['regis_start'])) {
                                        $start_datetime = DateTime::createFromFormat('Y-m-d H:i:s', $current['regis_start']);
                                        if ($start_datetime) {
                                            $start_date = $start_datetime->format('Y-m-d');
                                            $start_time = $start_datetime->format('H:i');
                                        }
                                    }
                                    if (isset($current['regis_end'])) {
                                        $end_datetime = DateTime::createFromFormat('Y-m-d H:i:s', $current['regis_end']);
                                        if ($end_datetime) {
                                            $end_date = $end_datetime->format('Y-m-d');
                                            $end_time = $end_datetime->format('H:i');
                                        }
                                    }
                                }
                            ?>
                            
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card card-outline card-secondary">
                                        <div class="card-header">
                                            <h4 class="card-title font-weight-bold"><?php echo $level_name; ?> (<?php echo $level_code; ?>)</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold text-success">
                                                            <i class="fas fa-play-circle"></i> วันเวลาเปิดรับสมัคร
                                                        </label>
                                                        <div class="row">
                                                            <div class="col-md-7">
                                                                <input type="date" 
                                                                       class="form-control" 
                                                                       name="<?php echo $level_code; ?>_start_date" 
                                                                       value="<?php echo $start_date; ?>"
                                                                       required>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="time" 
                                                                       class="form-control" 
                                                                       name="<?php echo $level_code; ?>_start_time" 
                                                                       value="<?php echo $start_time; ?>"
                                                                       required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold text-danger">
                                                            <i class="fas fa-stop-circle"></i> วันเวลาปิดรับสมัคร
                                                        </label>
                                                        <div class="row">
                                                            <div class="col-md-7">
                                                                <input type="date" 
                                                                       class="form-control" 
                                                                       name="<?php echo $level_code; ?>_end_date" 
                                                                       value="<?php echo $end_date; ?>"
                                                                       required>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="time" 
                                                                       class="form-control" 
                                                                       name="<?php echo $level_code; ?>_end_time" 
                                                                       value="<?php echo $end_time; ?>"
                                                                       required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <?php if ($current): ?>
                                            <div class="alert alert-info">
                                                <small>
                                                    <i class="fas fa-clock"></i> 
                                                    ตั้งค่าปัจจุบัน: 
                                                    เปิดรับสมัคร <?php echo isset($current['regis_start']) ? date('d/m/Y H:i', strtotime($current['regis_start'])) : 'ไม่ได้กำหนด'; ?> - 
                                                    ปิดรับสมัคร <?php echo isset($current['regis_end']) ? date('d/m/Y H:i', strtotime($current['regis_end'])) : 'ไม่ได้กำหนด'; ?>
                                                </small>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> บันทึกการตั้งค่า
                            </button>
                            <button type="reset" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-undo"></i> รีเซ็ต
                            </button>
                        </div>
                    </form>
                </div>

                <!-- แสดงข้อมูลปัจจุบัน -->
                <?php if (!empty($current_settings)): ?>
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> สรุปการตั้งค่าปัจจุบัน</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ระดับชั้น</th>
                                        <th class="text-success">วันเวลาเปิดรับสมัคร</th>
                                        <th class="text-danger">วันเวลาปิดรับสมัคร</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($levels as $level_code => $level_name): 
                                        if (isset($current_settings[$level_code])): 
                                            $setting = $current_settings[$level_code];
                                            $now = new DateTime();
                                            $start = new DateTime($setting['regis_start']);
                                            $end = new DateTime($setting['regis_end']);
                                            
                                            $status = '';
                                            $status_class = '';
                                            if ($now < $start) {
                                                $status = 'ยังไม่เปิดรับสมัคร';
                                                $status_class = 'badge-warning';
                                            } elseif ($now >= $start && $now <= $end) {
                                                $status = 'กำลังเปิดรับสมัคร';
                                                $status_class = 'badge-success';
                                            } else {
                                                $status = 'ปิดรับสมัครแล้ว';
                                                $status_class = 'badge-danger';
                                            }
                                    ?>
                                    <tr>
                                        <td><strong><?php echo $level_name; ?></strong></td>
                                        <td><?php echo $start->format('d/m/Y H:i'); ?></td>
                                        <td><?php echo $end->format('d/m/Y H:i'); ?></td>
                                        <td><span class="badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                                    </tr>
                                    <?php endif; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

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
    // Form validation
    $('#settingForm').on('submit', function(e) {
        let hasError = false;
        let errorMessages = [];
        
        // ตรวจสอบแต่ละระดับชั้น
        <?php foreach (array_keys($levels) as $level_code): ?>
        const <?php echo str_replace('.', '_', $level_code); ?>_start_date = $('input[name="<?php echo $level_code; ?>_start_date"]').val();
        const <?php echo str_replace('.', '_', $level_code); ?>_start_time = $('input[name="<?php echo $level_code; ?>_start_time"]').val();
        const <?php echo str_replace('.', '_', $level_code); ?>_end_date = $('input[name="<?php echo $level_code; ?>_end_date"]').val();
        const <?php echo str_replace('.', '_', $level_code); ?>_end_time = $('input[name="<?php echo $level_code; ?>_end_time"]').val();
        
        if (<?php echo str_replace('.', '_', $level_code); ?>_start_date && <?php echo str_replace('.', '_', $level_code); ?>_start_time && <?php echo str_replace('.', '_', $level_code); ?>_end_date && <?php echo str_replace('.', '_', $level_code); ?>_end_time) {
            const startDateTime = new Date(<?php echo str_replace('.', '_', $level_code); ?>_start_date + ' ' + <?php echo str_replace('.', '_', $level_code); ?>_start_time);
            const endDateTime = new Date(<?php echo str_replace('.', '_', $level_code); ?>_end_date + ' ' + <?php echo str_replace('.', '_', $level_code); ?>_end_time);
            
            if (startDateTime >= endDateTime) {
                hasError = true;
                errorMessages.push('<?php echo $level_name; ?>: วันเวลาเปิดรับสมัครต้องก่อนวันเวลาปิดรับสมัคร');
            }
        }
        <?php endforeach; ?>
        
        if (hasError) {
            e.preventDefault();
            alert('กรุณาตรวจสอบข้อมูล:\n' + errorMessages.join('\n'));
        }
    });
    
    // Auto-dismiss alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>

<?php require_once('script.php');?>
</body>
</html>

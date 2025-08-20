<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'] ?? [];
require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h5 class="m-0">รายงานกิจกรรม Best</h5>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <!-- Header Card with Stats -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-gradient-primary">
                            <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">กิจกรรมทั้งหมด</span>
                                <span class="info-box-number" id="stat-activities">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-gradient-success">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">ผู้สมัครทั้งหมด</span>
                                <span class="info-box-number" id="stat-members">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-gradient-warning">
                            <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">อัตราการเติมเต็ม</span>
                                <span class="info-box-number" id="stat-fill">-%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-gradient-info">
                            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">กิจกรรมเต็ม</span>
                                <span class="info-box-number" id="stat-full">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-chart-bar mr-2"></i>รายงานกิจกรรม Best For Teen</h3>
                    </div>
                    <div class="card-body p-4">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills nav-justified mb-4" id="bestReportTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active rounded-pill mx-1 shadow-sm" id="best-overview-tab" data-toggle="pill" href="#best-overview" role="tab" aria-controls="best-overview" aria-selected="true">
                                <i class="fas fa-chart-pie mr-2"></i>ภาพรวมกิจกรรม
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded-pill mx-1 shadow-sm" id="best-level-tab" data-toggle="pill" href="#best-level" role="tab" aria-controls="best-level" aria-selected="false">
                                <i class="fas fa-layer-group mr-2"></i>รายชั้น
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded-pill mx-1 shadow-sm" id="best-room-tab" data-toggle="pill" href="#best-room" role="tab" aria-controls="best-room" aria-selected="false">
                                <i class="fas fa-door-open mr-2"></i>รายห้อง
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded-pill mx-1 shadow-sm" id="best-activity-tab" data-toggle="pill" href="#best-activity" role="tab" aria-controls="best-activity" aria-selected="false">
                                <i class="fas fa-star mr-2"></i>ตามกิจกรรม
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content mt-3">
                        <!-- OVERVIEW -->
                        <div class="tab-pane fade show active" id="best-overview" role="tabpanel" aria-labelledby="best-overview-tab">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-warning text-dark">
                                    <h5 class="card-title mb-0"><i class="fas fa-chart-pie mr-2"></i>ภาพรวมกิจกรรม Best For Teen</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="table-responsive">
                                                <table id="best-overview-table" class="table table-hover table-striped">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th><i class="fas fa-star text-warning mr-1"></i>กิจกรรม</th>
                                                            <th class="text-center"><i class="fas fa-users text-info mr-1"></i>รับได้</th>
                                                            <th class="text-center"><i class="fas fa-user-check text-success mr-1"></i>ลงแล้ว</th>
                                                            <th class="text-center"><i class="fas fa-percentage text-primary mr-1"></i>%</th>
                                                            <th><i class="fas fa-graduation-cap text-secondary mr-1"></i>ชั้นที่รับ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-4">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="text-muted mb-3">กราฟเปรียบเทียบ</h6>
                                                    <canvas id="best-overview-chart" style="height: 100px;"></canvas>
                                                </div>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- LEVEL -->
                        <div class="tab-pane fade" id="best-level" role="tabpanel" aria-labelledby="best-level-tab">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-success text-white">
                                    <h5 class="card-title mb-0"><i class="fas fa-layer-group mr-2"></i>รายงานตามระดับชั้น</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold"><i class="fas fa-filter mr-2 text-primary"></i>เลือกระดับชั้น:</label>
                                                <select id="level-select" class="form-control form-control-lg shadow-sm">
                                                    <option value="1">มัธยมศึกษาปีที่ 1</option>
                                                    <option value="2">มัธยมศึกษาปีที่ 2</option>
                                                    <option value="3">มัธยมศึกษาปีที่ 3</option>
                                                    <option value="4">มัธยมศึกษาปีที่ 4</option>
                                                    <option value="5">มัธยมศึกษาปีที่ 5</option>
                                                    <option value="6">มัธยมศึกษาปีที่ 6</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="best-level-activity-table" class="table table-hover table-striped">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="text-center" width="10%">#</th>
                                                    <th><i class="fas fa-star text-warning mr-2"></i>กิจกรรม</th>
                                                    <th class="text-center" width="20%"><i class="fas fa-users text-success mr-2"></i>จำนวนผู้สมัคร</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ROOM -->
                        <div class="tab-pane fade" id="best-room" role="tabpanel" aria-labelledby="best-room-tab">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-info text-white">
                                    <h5 class="card-title mb-0"><i class="fas fa-door-open mr-2"></i>รายงานตามห้องเรียน</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card bg-light border-0">
                                                <div class="card-body">
                                                    <div class="form-row align-items-end">
                                                        <div class="form-group col-md-3">
                                                            <label class="font-weight-bold"><i class="fas fa-layer-group mr-2 text-primary"></i>ชั้น:</label>
                                                            <select id="room-level-select" class="form-control shadow-sm"></select>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label class="font-weight-bold"><i class="fas fa-door-closed mr-2 text-secondary"></i>ห้อง:</label>
                                                            <select id="room-room-select" class="form-control shadow-sm"></select>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <button id="room-search" class="btn btn-primary btn-lg shadow">
                                                                <i class="fas fa-search mr-2"></i>แสดงรายชื่อ
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-light">
                                                    <h6 class="card-title mb-0"><i class="fas fa-chart-bar mr-2 text-info"></i>สรุปจำนวนตามห้อง</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="best-room-table" class="table table-hover table-sm">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th><i class="fas fa-door-open mr-1"></i>ห้อง</th>
                                                                    <th class="text-center"><i class="fas fa-users mr-1"></i>จำนวน</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-light">
                                                    <h6 class="card-title mb-0"><i class="fas fa-users mr-2 text-success"></i>รายชื่อนักเรียน</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="best-room-students-table" class="table table-hover table-striped">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th class="text-center" width="8%">#</th>
                                                                    <th width="15%">รหัส</th>
                                                                    <th>ชื่อ-สกุล</th>
                                                                    <th width="15%">ห้อง</th>
                                                                    <th>กิจกรรม</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ACTIVITY REPORT -->
                        <div class="tab-pane fade" id="best-activity" role="tabpanel" aria-labelledby="best-activity-tab">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-secondary text-white">
                                    <h5 class="card-title mb-0"><i class="fas fa-star mr-2"></i>รายงานตามกิจกรรม</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card bg-light border-0">
                                                <div class="card-body">
                                                    <div class="form-row align-items-end">
                                                        <div class="form-group col-md-6">
                                                            <label class="font-weight-bold"><i class="fas fa-star mr-2 text-warning"></i>เลือกกิจกรรม:</label>
                                                            <select id="activity-select" class="form-control form-control-lg shadow-sm">
                                                                <option value="">-- เลือกกิจกรรม --</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <button id="activity-load" class="btn btn-success btn-lg shadow">
                                                                <i class="fas fa-download mr-2"></i>โหลดรายชื่อ
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="card-title mb-0"><i class="fas fa-list mr-2 text-primary"></i>รายชื่อสมาชิกกิจกรรม</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="best-activity-members-table" class="table table-hover table-striped">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="text-center" width="8%">#</th>
                                                            <th width="15%"><i class="fas fa-id-card mr-1"></i>รหัสนักเรียน</th>
                                                            <th><i class="fas fa-user mr-1"></i>ชื่อ-สกุล</th>
                                                            <th width="15%"><i class="fas fa-door-open mr-1"></i>ห้อง</th>
                                                            <th width="20%"><i class="fas fa-clock mr-1"></i>เวลาลงทะเบียน</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php');?>
</div>
<?php require_once('script.php');?>

<script>
// Enhanced tab switching with animations and effects
document.querySelectorAll('#bestReportTabs .nav-link').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active classes with animation
        document.querySelectorAll('#bestReportTabs .nav-link').forEach(t => {
            t.classList.remove('active');
        });
        document.querySelectorAll('.tab-pane').forEach(p => {
            p.classList.remove('show','active');
        });
        
        // Add active to clicked tab with pulse effect
        this.classList.add('active');
        this.style.transform = 'scale(0.95)';
        setTimeout(() => { this.style.transform = 'scale(1)'; }, 100);
        
        // Show target pane with fade effect
        const target = this.getAttribute('href');
        const pane = document.querySelector(target);
        if (pane) {
            pane.classList.add('show','active');
            pane.style.opacity = '0';
            pane.style.transform = 'translateY(10px)';
            setTimeout(() => {
                pane.style.transition = 'all 0.3s ease-in-out';
                pane.style.opacity = '1';
                pane.style.transform = 'translateY(0)';
            }, 50);
        }

        // Initialize tab content with loading effect
        if (target === '#best-overview' && window.initBestOverview) {
            showLoading('Loading overview...'); 
            setTimeout(() => { window.initBestOverview(); hideLoading(); }, 500);
        }
        if (target === '#best-level' && window.initBestLevel) {
            showLoading('Loading level data...'); 
            setTimeout(() => { window.initBestLevel(); hideLoading(); }, 500);
        }
        if (target === '#best-room' && window.initBestRoom) {
            showLoading('Loading room data...'); 
            setTimeout(() => { window.initBestRoom(); hideLoading(); }, 500);
        }
        if (target === '#best-activity' && window.initBestActivity) {
            showLoading('Loading activity data...'); 
            setTimeout(() => { window.initBestActivity(); hideLoading(); }, 500);
        }
    });
});

// Loading overlay functions
function showLoading(message = 'Loading...') {
    let overlay = document.getElementById('loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-content">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <div class="loading-text">${message}</div>
            </div>
        `;
        document.body.appendChild(overlay);
    }
    overlay.querySelector('.loading-text').textContent = message;
    overlay.style.display = 'flex';
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.style.display = 'none';
}

// Initialize with loading effect
document.addEventListener('DOMContentLoaded', () => {
    showLoading('Initializing dashboard...');
    setTimeout(() => {
        if (window.initBestOverview) window.initBestOverview();
        hideLoading();
        // Add entrance animation to stats cards
        document.querySelectorAll('.info-box').forEach((box, i) => {
            box.style.opacity = '0';
            box.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                box.style.transition = 'all 0.5s ease-out';
                box.style.opacity = '1';
                box.style.transform = 'translateY(0)';
            }, i * 100);
        });
    }, 800);
});

// Update stats from overview data
function updateStats(data) {
    if (!data || !data.length) return;
    
    const totalActivities = data.length;
    const totalMembers = data.reduce((sum, a) => sum + (parseInt(a.current_members_count) || 0), 0);
    const totalCapacity = data.reduce((sum, a) => sum + (parseInt(a.max_members) || 0), 0);
    const fillRate = totalCapacity > 0 ? Math.round((totalMembers / totalCapacity) * 100) : 0;
    const fullActivities = data.filter(a => {
        const current = parseInt(a.current_members_count) || 0;
        const max = parseInt(a.max_members) || 0;
        return current >= max && max > 0;
    }).length;

    // Animate counter updates
    animateCounter('stat-activities', totalActivities);
    animateCounter('stat-members', totalMembers);
    animateCounter('stat-fill', fillRate, '%');
    animateCounter('stat-full', fullActivities);
}

function animateCounter(elementId, target, suffix = '') {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    let current = 0;
    const increment = target / 30;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target + suffix;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current) + suffix;
        }
    }, 50);
}
</script>
<style>
/* Enhanced animations and styles */
.tab-pane {
    transition: all 0.3s ease-in-out;
}

.nav-pills .nav-link {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.nav-pills .nav-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.info-box {
    transition: all 0.3s ease;
    border-radius: 10px;
    overflow: hidden;
}

.info-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.1);
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn:active {
    transform: translateY(0);
}

/* Loading overlay */
#loading-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255,255,255,0.9);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-content {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.loading-text {
    color: #6c757d;
    font-weight: 500;
}

/* Pulse animation for stats */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.info-box-number {
    animation: pulse 2s ease-in-out infinite;
}

/* Card header gradients */
.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.bg-gradient-secondary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

/* Table responsive enhancements */
.table-responsive {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

/* Form controls */
.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    border-color: #667eea;
}

/* Animation for DataTables */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
}
</style>
<!-- Per-tab scripts -->
<script src="js/best_overview.js"></script>
<script src="js/best_level_report.js"></script>
<script src="js/best_room_report.js"></script>
<script src="js/best_activity_report.js"></script>
</body>
</html>

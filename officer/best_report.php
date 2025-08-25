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
// Enhanced tab switching with concurrent loading and smart caching
document.querySelectorAll('#bestReportTabs .nav-link').forEach(tab => {
    tab.addEventListener('click', async function(e) {
        e.preventDefault();
        
        // Prevent multiple clicks during loading
        if (this.classList.contains('loading')) return;
        
        // Remove active classes with smooth transition
        document.querySelectorAll('#bestReportTabs .nav-link').forEach(t => {
            t.classList.remove('active');
        });
        document.querySelectorAll('.tab-pane').forEach(p => {
            p.classList.remove('show','active');
        });
        
        // Add active to clicked tab with enhanced visual feedback
        this.classList.add('active', 'loading');
        const originalText = this.innerHTML;
        
        // Visual feedback
        this.style.transform = 'scale(0.95)';
        setTimeout(() => { 
            this.style.transform = 'scale(1)'; 
        }, 100);
        
        // Show target pane immediately for better perceived performance
        const target = this.getAttribute('href');
        const pane = document.querySelector(target);
        if (pane) {
            pane.classList.add('show','active');
            pane.style.opacity = '0';
            pane.style.transform = 'translateY(10px)';
            
            // Use requestAnimationFrame for 60fps smooth animation
            requestAnimationFrame(() => {
                pane.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                pane.style.opacity = '1';
                pane.style.transform = 'translateY(0)';
            });
        }

        try {
            // Concurrent loading with smart timeout
            const loadPromise = new Promise((resolve, reject) => {
                const timeout = setTimeout(() => {
                    reject(new Error('Loading timeout'));
                }, 8000);
                
                const loadTab = () => {
                    if (target === '#best-overview' && window.initBestOverview) {
                        window.initBestOverview();
                    }
                    else if (target === '#best-level' && window.initBestLevel) {
                        window.initBestLevel();
                    }
                    else if (target === '#best-room' && window.initBestRoom) {
                        window.initBestRoom();
                    }
                    else if (target === '#best-activity' && window.initBestActivity) {
                        window.initBestActivity();
                    }
                    
                    clearTimeout(timeout);
                    resolve();
                };
                
                // Start loading immediately
                loadTab();
                
                // Minimum loading time for better UX (prevents flashing)
                setTimeout(resolve, 200);
            });
            
            // Show loading with progress
            showLoadingWithProgress(getLoadingMessage(target));
            
            await loadPromise;
            
        } catch (error) {
            console.error('Tab loading error:', error);
            showToast('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error.message, 'error');
        } finally {
            hideLoading();
            this.classList.remove('loading');
            this.innerHTML = originalText;
        }
    });
});

// Get appropriate loading message
function getLoadingMessage(target) {
    const messages = {
        '#best-overview': 'กำลังโหลดภาพรวมกิจกรรม...',
        '#best-level': 'กำลังโหลดข้อมูลตามระดับชั้น...',
        '#best-room': 'กำลังโหลดข้อมูลรายห้อง...',
        '#best-activity': 'กำลังโหลดข้อมูลรายกิจกรรม...'
    };
    return messages[target] || 'กำลังโหลดข้อมูล...';
}

// Enhanced loading overlay with progress and better animations
function showLoadingWithProgress(message = 'Loading...') {
    let overlay = document.getElementById('loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-content">
                <div class="spinner-container">
                    <div class="main-spinner">
                        <div class="spinner-ring"></div>
                        <div class="spinner-ring"></div>
                        <div class="spinner-ring"></div>
                    </div>
                    <div class="spinner-dots">
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                </div>
                <div class="loading-text">${message}</div>
                <div class="loading-progress">
                    <div class="progress-bar"></div>
                </div>
                <div class="loading-tips">
                    <small class="text-muted">💡 ใช้ Alt+1,2,3,4 สำหรับเปลี่ยนแท็บเร็ว</small>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    }
    
    const loadingText = overlay.querySelector('.loading-text');
    const progressBar = overlay.querySelector('.progress-bar');
    
    loadingText.textContent = message;
    overlay.style.display = 'flex';
    
    // Animate progress bar with realistic timing
    if (progressBar) {
        progressBar.style.width = '0%';
        progressBar.style.transition = 'none';
        
        requestAnimationFrame(() => {
            progressBar.style.transition = 'width 0.3s ease-out';
            progressBar.style.width = '30%';
            
            setTimeout(() => {
                progressBar.style.transition = 'width 2s ease-out';
                progressBar.style.width = '85%';
            }, 100);
        });
    }
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        const progressBar = overlay.querySelector('.progress-bar');
        if (progressBar) {
            // Complete progress bar
            progressBar.style.transition = 'width 0.2s ease-out';
            progressBar.style.width = '100%';
            
            setTimeout(() => {
                // Fade out overlay
                overlay.style.transition = 'opacity 0.3s ease-out';
                overlay.style.opacity = '0';
                
                setTimeout(() => {
                    overlay.style.display = 'none';
                    overlay.style.opacity = '1';
                }, 300);
            }, 200);
        } else {
            overlay.style.display = 'none';
        }
    }
}

// Smart preloading system
let preloadCache = new Map();

function preloadTabData(tabName) {
    if (preloadCache.has(tabName)) {
        return preloadCache.get(tabName);
    }
    
    const promise = new Promise(async (resolve) => {
        try {
            // Simulate preloading based on tab
            switch(tabName) {
                case 'overview':
                    await fetch('api/best_fetch_overview.php', { 
                        method: 'GET',
                        headers: { 'X-Preload': 'true' }
                    });
                    break;
                case 'level':
                    await fetch('api/best_fetch_level_activity.php?level=1', {
                        method: 'GET',
                        headers: { 'X-Preload': 'true' }
                    });
                    break;
            }
            resolve(true);
        } catch (error) {
            console.warn('Preload failed for', tabName, error);
            resolve(false);
        }
    });
    
    preloadCache.set(tabName, promise);
    return promise;
}

// Toast notification with better styling
function showToast(message, type = 'info') {
    if (typeof Swal !== 'undefined') {
        const icons = {
            'success': 'success',
            'error': 'error', 
            'warning': 'warning',
            'info': 'info'
        };
        
        Swal.fire({ 
            toast: true, 
            position: 'top-end', 
            icon: icons[type] || 'info',
            title: message, 
            showConfirmButton: false, 
            timer: type === 'error' ? 5000 : 3000,
            timerProgressBar: true,
            background: '#ffffff',
            color: '#1f2937',
            customClass: {
                popup: 'animated slideInRight'
            }
        });
    }
}

// Enhanced initialization with intelligent preloading
document.addEventListener('DOMContentLoaded', async () => {
    console.log('🚀 Best report dashboard initializing...');
    const startTime = performance.now();
    
    showLoadingWithProgress('เตรียมแดชบอร์ด...');
    
    try {
        // Initialize main overview tab
        if (window.initBestOverview) {
            await new Promise(resolve => {
                window.initBestOverview();
                setTimeout(resolve, 300);
            });
        }
        
        // Start preloading other tabs in the background
        setTimeout(() => {
            preloadTabData('level').then(() => {
                console.log('📊 Level data preloaded');
            });
        }, 1000);
        
        // Staggered entrance animations
        await animateElements();
        
        const loadTime = performance.now() - startTime;
        console.log(`⚡ Dashboard ready in ${Math.round(loadTime)}ms`);
        
        // Show ready notification
        setTimeout(() => {
            showToast('แดชบอร์ดพร้อมใช้งาน! 🎉', 'success');
        }, 500);
        
    } catch (error) {
        console.error('Initialization error:', error);
        showToast('เกิดข้อผิดพลาดในการเตรียมแดชบอร์ด', 'error');
    } finally {
        hideLoading();
    }
});

// Optimized element animations
async function animateElements() {
    // Stats cards animation
    const infoBoxes = document.querySelectorAll('.info-box');
    for (let i = 0; i < infoBoxes.length; i++) {
        const box = infoBoxes[i];
        box.style.opacity = '0';
        box.style.transform = 'translateY(-30px) scale(0.9)';
        
        await new Promise(resolve => setTimeout(resolve, 100));
        
        box.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
        box.style.opacity = '1';
        box.style.transform = 'translateY(0) scale(1)';
    }
    
    // Tab buttons animation
    const tabButtons = document.querySelectorAll('#bestReportTabs .nav-link');
    for (let i = 0; i < tabButtons.length; i++) {
        const tab = tabButtons[i];
        tab.style.opacity = '0';
        tab.style.transform = 'translateX(-30px)';
        
        await new Promise(resolve => setTimeout(resolve, 80));
        
        tab.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        tab.style.opacity = '1';
        tab.style.transform = 'translateX(0)';
    }
}

// Enhanced stats update with smooth counter animations
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

    // Animate counter updates with staggered timing
    setTimeout(() => animateCounter('stat-activities', totalActivities), 100);
    setTimeout(() => animateCounter('stat-members', totalMembers), 200);
    setTimeout(() => animateCounter('stat-fill', fillRate, '%'), 300);
    setTimeout(() => animateCounter('stat-full', fullActivities), 400);
}

// Enhanced counter animation with easing
function animateCounter(elementId, target, suffix = '') {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    let current = 0;
    const duration = 1500;
    const steps = 60;
    const increment = target / steps;
    let step = 0;
    
    const timer = setInterval(() => {
        step++;
        const progress = step / steps;
        
        // Use easing function for smooth animation
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        current = target * easeOutQuart;
        
        if (step >= steps) {
            element.textContent = target + suffix;
            element.style.transform = 'scale(1)';
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current) + suffix;
            // Add subtle scale effect during animation
            const scale = 1 + Math.sin(progress * Math.PI) * 0.1;
            element.style.transform = `scale(${scale})`;
        }
    }, duration / steps);
}

// Add keyboard shortcuts for better UX
document.addEventListener('keydown', (e) => {
    if (e.altKey) {
        switch(e.key) {
            case '1':
                e.preventDefault();
                document.querySelector('[href="#best-overview"]')?.click();
                break;
            case '2':
                e.preventDefault();
                document.querySelector('[href="#best-level"]')?.click();
                break;
            case '3':
                e.preventDefault();
                document.querySelector('[href="#best-room"]')?.click();
                break;
            case '4':
                e.preventDefault();
                document.querySelector('[href="#best-activity"]')?.click();
                break;
        }
    }
});

// Add refresh shortcut
document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'r' && e.shiftKey) {
        e.preventDefault();
        location.reload();
    }
});
</script>
<style>
/* Enhanced animations with hardware acceleration and performance optimizations */
.tab-pane {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: opacity, transform;
    backface-visibility: hidden;
}

.nav-pills .nav-link {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid transparent;
    position: relative;
    overflow: hidden;
    will-change: transform, box-shadow;
    backface-visibility: hidden;
}

.nav-pills .nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.nav-pills .nav-link:hover::before {
    left: 100%;
}

.nav-pills .nav-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.nav-pills .nav-link.loading {
    opacity: 0.7;
    pointer-events: none;
    position: relative;
}

.nav-pills .nav-link.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    right: 10px;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top: 2px solid #fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    transform: translateY(-50%);
}

@keyframes spin {
    0% { transform: translateY(-50%) rotate(0deg); }
    100% { transform: translateY(-50%) rotate(360deg); }
}

.card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform, box-shadow;
    backface-visibility: hidden;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.12);
}

.info-box {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    will-change: transform, box-shadow;
    backface-visibility: hidden;
}

.info-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.1) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.info-box:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

.info-box:hover::before {
    opacity: 1;
}

/* Enhanced loading overlay with multiple spinners */
#loading-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(248, 250, 252, 0.95);
    backdrop-filter: blur(5px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    transition: opacity 0.3s ease;
}

.loading-content {
    text-align: center;
    padding: 3rem 2rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 80px rgba(0,0,0,0.15);
    max-width: 420px;
    width: 90%;
    position: relative;
    overflow: hidden;
}

.loading-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

.spinner-container {
    position: relative;
    margin-bottom: 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.main-spinner {
    position: relative;
    width: 60px;
    height: 60px;
}

.spinner-ring {
    position: absolute;
    width: 60px;
    height: 60px;
    border: 3px solid transparent;
    border-radius: 50%;
    animation: spinRing 2s linear infinite;
}

.spinner-ring:nth-child(1) {
    border-top: 3px solid #667eea;
    animation-delay: 0s;
}

.spinner-ring:nth-child(2) {
    border-right: 3px solid #764ba2;
    animation-delay: -0.5s;
    width: 48px;
    height: 48px;
    top: 6px;
    left: 6px;
}

.spinner-ring:nth-child(3) {
    border-bottom: 3px solid #667eea;
    animation-delay: -1s;
    width: 36px;
    height: 36px;
    top: 12px;
    left: 12px;
}

@keyframes spinRing {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spinner-dots {
    display: flex;
    justify-content: center;
    gap: 6px;
}

.spinner-dots .dot {
    width: 10px;
    height: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    animation: dotPulse 1.4s ease-in-out infinite both;
}

.spinner-dots .dot:nth-child(1) { animation-delay: -0.32s; }
.spinner-dots .dot:nth-child(2) { animation-delay: -0.16s; }
.spinner-dots .dot:nth-child(3) { animation-delay: 0s; }

@keyframes dotPulse {
    0%, 80%, 100% {
        transform: scale(0.8);
        opacity: 0.5;
    }
    40% {
        transform: scale(1.2);
        opacity: 1;
    }
}

.loading-text {
    color: #4a5568;
    font-weight: 600;
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    position: relative;
    z-index: 1;
}

.loading-progress {
    width: 100%;
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 1rem;
    position: relative;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #667eea 100%);
    background-size: 200% 100%;
    border-radius: 3px;
    width: 0%;
    transition: width 0.3s ease;
    animation: progressShine 2s ease-in-out infinite;
}

@keyframes progressShine {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.loading-tips {
    margin-top: 1rem;
    opacity: 0.8;
    font-size: 0.85rem;
    color: #6b7280;
}

/* Enhanced stats animation */
@keyframes statsPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1) rotate(2deg); }
    100% { transform: scale(1); }
}

.info-box-number {
    animation: statsPulse 3s ease-in-out infinite;
    will-change: transform;
    display: inline-block;
}

/* Card header gradients with enhanced shadows */
.bg-gradient-primary { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.bg-gradient-success { 
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
}

.bg-gradient-warning { 
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    box-shadow: 0 6px 20px rgba(240, 147, 251, 0.4);
}

.bg-gradient-info { 
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    box-shadow: 0 6px 20px rgba(79, 172, 254, 0.4);
}

.bg-gradient-secondary { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Table optimizations */
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    position: sticky;
    top: 0;
    z-index: 10;
}

.table-hover tbody tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: background-color, transform, box-shadow;
}

.table-hover tbody tr:hover {
    background: linear-gradient(90deg, rgba(0,123,255,0.08) 0%, rgba(0,123,255,0.12) 100%);
    transform: scale(1.01);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Button enhancements with ripple effect */
.btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    will-change: transform, box-shadow;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255,255,255,0.3);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

.btn:active::before {
    width: 300px;
    height: 300px;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.btn:active {
    transform: translateY(0);
}

/* Form controls with enhanced focus states */
.form-control {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    border-color: #667eea;
    transform: scale(1.02);
}

/* DataTables enhancements */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
    border-color: #667eea;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.dataTables_wrapper .dataTables_filter input {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: scale(1.02);
}

/* Table responsive with better scrolling */
.table-responsive {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    background: white;
    position: relative;
}

.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
}

/* Badge enhancements */
.badge {
    font-weight: 500;
    letter-spacing: 0.5px;
    padding: 0.6em 0.8em;
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.1);
}

/* Performance optimizations */
* {
    box-sizing: border-box;
}

img, video {
    max-width: 100%;
    height: auto;
}

/* Reduced motion for users who prefer it */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    #loading-overlay {
        background: rgba(17, 24, 39, 0.95);
    }
    
    .loading-content {
        background: #374151;
        color: #f9fafb;
    }
    
    .loading-text {
        color: #e5e7eb;
    }
}
</style>
<!-- Per-tab scripts -->
<script src="js/best_overview.js"></script>
<script src="js/best_level_report.js"></script>
<script src="js/best_room_report.js"></script>
<script src="js/best_activity_report.js"></script>
</body>
</html>

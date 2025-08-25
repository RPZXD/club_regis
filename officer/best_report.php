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
// Enhanced tab switching with async loading and better performance
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
        this.style.transform = 'scale(0.95)';
        
        setTimeout(() => { 
            this.style.transform = 'scale(1)'; 
        }, 100);
        
        // Show target pane with optimized fade effect
        const target = this.getAttribute('href');
        const pane = document.querySelector(target);
        if (pane) {
            pane.classList.add('show','active');
            pane.style.opacity = '0';
            pane.style.transform = 'translateY(10px)';
            
            // Use requestAnimationFrame for smoother animation
            requestAnimationFrame(() => {
                pane.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                pane.style.opacity = '1';
                pane.style.transform = 'translateY(0)';
            });
        }

        try {
            // Initialize tab content with optimized async loading
            if (target === '#best-overview' && window.initBestOverview) {
                showLoading('โหลดภาพรวมกิจกรรม...'); 
                await new Promise(resolve => {
                    window.initBestOverview();
                    setTimeout(resolve, 300); // Minimum loading time for better UX
                });
            }
            else if (target === '#best-level' && window.initBestLevel) {
                showLoading('โหลดข้อมูลตามระดับชั้น...'); 
                await new Promise(resolve => {
                    window.initBestLevel();
                    setTimeout(resolve, 300);
                });
            }
            else if (target === '#best-room' && window.initBestRoom) {
                showLoading('โหลดข้อมูลรายห้อง...'); 
                await new Promise(resolve => {
                    window.initBestRoom();
                    setTimeout(resolve, 300);
                });
            }
            else if (target === '#best-activity' && window.initBestActivity) {
                showLoading('โหลดข้อมูลรายกิจกรรม...'); 
                await new Promise(resolve => {
                    window.initBestActivity();
                    setTimeout(resolve, 300);
                });
            }
        } catch (error) {
            console.error('Tab loading error:', error);
            showToast('เกิดข้อผิดพลาดในการโหลดข้อมูล', 'error');
        } finally {
            hideLoading();
            this.classList.remove('loading');
            this.innerHTML = originalText;
        }
    });
});

// Enhanced loading overlay functions with better performance
function showLoading(message = 'Loading...') {
    let overlay = document.getElementById('loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-content">
                <div class="spinner-container">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
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
            </div>
        `;
        document.body.appendChild(overlay);
    }
    overlay.querySelector('.loading-text').textContent = message;
    overlay.style.display = 'flex';
    
    // Animate progress bar
    const progressBar = overlay.querySelector('.progress-bar');
    if (progressBar) {
        progressBar.style.width = '0%';
        requestAnimationFrame(() => {
            progressBar.style.transition = 'width 2s ease-out';
            progressBar.style.width = '90%';
        });
    }
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        const progressBar = overlay.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = '100%';
            setTimeout(() => {
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

// Toast notification function
function showToast(message, type = 'info') {
    if (typeof Swal !== 'undefined') {
        const icon = ['success','error','warning','info','question'].includes(type) ? type : 'info';
        Swal.fire({ 
            toast: true, 
            position: 'top-end', 
            icon, 
            title: message, 
            showConfirmButton: false, 
            timer: 3000, 
            timerProgressBar: true,
            background: '#ffffff',
            color: '#1f2937'
        });
    }
}

// Enhanced initialization with performance monitoring
document.addEventListener('DOMContentLoaded', async () => {
    console.log('🚀 Best report page initializing...');
    const startTime = performance.now();
    
    showLoading('กำลังเตรียมข้อมูลแดชบอร์ด...');
    
    try {
        await new Promise(resolve => setTimeout(resolve, 500)); // Minimum loading time
        
        if (window.initBestOverview) {
            await new Promise(resolve => {
                window.initBestOverview();
                setTimeout(resolve, 100);
            });
        }
        
        // Add staggered entrance animation to stats cards
        const infoBoxes = document.querySelectorAll('.info-box');
        infoBoxes.forEach((box, i) => {
            box.style.opacity = '0';
            box.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                box.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                box.style.opacity = '1';
                box.style.transform = 'translateY(0)';
            }, i * 150 + 200);
        });
        
        // Add entrance animation to tab buttons
        const tabButtons = document.querySelectorAll('#bestReportTabs .nav-link');
        tabButtons.forEach((tab, i) => {
            tab.style.opacity = '0';
            tab.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                tab.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                tab.style.opacity = '1';
                tab.style.transform = 'translateX(0)';
            }, i * 100 + 800);
        });
        
    } catch (error) {
        console.error('Initialization error:', error);
        showToast('เกิดข้อผิดพลาดในการเตรียมข้อมูล', 'error');
    } finally {
        const loadTime = performance.now() - startTime;
        console.log(`⚡ Best report page initialized in ${Math.round(loadTime)}ms`);
        hideLoading();
    }
});

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
/* Enhanced animations and styles with better performance */
.tab-pane {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: opacity, transform;
}

.nav-pills .nav-link {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid transparent;
    position: relative;
    overflow: hidden;
}

.nav-pills .nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
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
}

.card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform, box-shadow;
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

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
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
    transition: width 0.6s, height 0.6s;
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

/* Enhanced loading overlay with better animations */
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
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    max-width: 400px;
    width: 90%;
}

.spinner-container {
    position: relative;
    margin-bottom: 1.5rem;
}

.spinner-dots {
    display: flex;
    justify-content: center;
    gap: 4px;
    margin-top: 1rem;
}

.spinner-dots .dot {
    width: 8px;
    height: 8px;
    background: #667eea;
    border-radius: 50%;
    animation: dotPulse 1.4s ease-in-out infinite both;
}

.spinner-dots .dot:nth-child(1) { animation-delay: -0.32s; }
.spinner-dots .dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes dotPulse {
    0%, 80%, 100% {
        transform: scale(0);
    }
    40% {
        transform: scale(1);
    }
}

.loading-text {
    color: #4a5568;
    font-weight: 500;
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.loading-progress {
    width: 100%;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 2px;
    width: 0%;
    transition: width 0.3s ease;
}

/* Enhanced pulse animation for stats */
@keyframes statsPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.info-box-number {
    animation: statsPulse 3s ease-in-out infinite;
    will-change: transform;
}

/* Card header gradients with better colors */
.bg-gradient-primary { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.bg-gradient-success { 
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
}

.bg-gradient-warning { 
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
}

.bg-gradient-info { 
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
}

.bg-gradient-secondary { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

/* Table responsive enhancements */
.table-responsive {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    background: white;
}

/* Form controls with better styling */
.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    border-color: #667eea;
    transform: scale(1.02);
}

/* Enhanced DataTables styling */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
    border-color: #667eea;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.dataTables_wrapper .dataTables_filter input {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Badge enhancements */
.badge {
    font-weight: 500;
    letter-spacing: 0.5px;
    padding: 0.5em 0.75em;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}
</style>
<!-- Per-tab scripts -->
<script src="js/best_overview.js"></script>
<script src="js/best_level_report.js"></script>
<script src="js/best_room_report.js"></script>
<script src="js/best_activity_report.js"></script>
</body>
</html>

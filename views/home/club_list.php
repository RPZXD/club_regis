<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<!-- Statistics Overview Section -->
<div class="mb-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-purple-500/30 animate-pulse-slow">
            <i class="fas fa-chart-pie text-white text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white">สถิติภาพรวมชุมนุม</h1>
            <p class="text-gray-500 dark:text-gray-400">ข้อมูลสรุปและสถิติการสมัครชุมนุมทั้งหมด</p>
        </div>
    </div>
    
    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- Total Clubs Card -->
        <div class="glass rounded-2xl p-5 card-hover relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="fas fa-users-rectangle text-white text-lg"></i>
                    </div>
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded-lg">ชุมนุม</span>
                </div>
                <div id="stat-total-clubs" class="text-3xl md:text-4xl font-black text-gray-800 dark:text-white mb-1">
                    <div class="w-12 h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">ชุมนุมทั้งหมด</p>
            </div>
        </div>
        
        <!-- Total Students Card -->
        <div class="glass rounded-2xl p-5 card-hover relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-user-graduate text-white text-lg"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 px-2 py-1 rounded-lg">นักเรียน</span>
                </div>
                <div id="stat-total-students" class="text-3xl md:text-4xl font-black text-gray-800 dark:text-white mb-1">
                    <div class="w-12 h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">ลงทะเบียนแล้ว</p>
            </div>
        </div>
        
        <!-- Open Clubs Card -->
        <div class="glass rounded-2xl p-5 card-hover relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-lime-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-lime-600 flex items-center justify-center shadow-lg shadow-green-500/30">
                        <i class="fas fa-door-open text-white text-lg"></i>
                    </div>
                    <span class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/40 px-2 py-1 rounded-lg">เปิดรับ</span>
                </div>
                <div id="stat-open-clubs" class="text-3xl md:text-4xl font-black text-gray-800 dark:text-white mb-1">
                    <div class="w-12 h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">ชุมนุมเปิดรับ</p>
            </div>
        </div>
        
        <!-- Full Clubs Card -->
        <div class="glass rounded-2xl p-5 card-hover relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-rose-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center shadow-lg shadow-red-500/30">
                        <i class="fas fa-door-closed text-white text-lg"></i>
                    </div>
                    <span class="text-xs font-bold text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/40 px-2 py-1 rounded-lg">เต็มแล้ว</span>
                </div>
                <div id="stat-full-clubs" class="text-3xl md:text-4xl font-black text-gray-800 dark:text-white mb-1">
                    <div class="w-12 h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">ชุมนุมเต็ม</p>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Status Donut Chart -->
        <div class="glass rounded-2xl p-6 card-hover">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-chart-pie text-white"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-white">สัดส่วนสถานะชุมนุม</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">เปิดรับ vs เต็มแล้ว</p>
                </div>
            </div>
            <div class="relative" style="height: 260px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        
        <!-- Top Clubs Bar Chart -->
        <div class="glass rounded-2xl p-6 card-hover">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-chart-bar text-white"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-white">ชุมนุมที่มีสมาชิกมากที่สุด</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Top 8 ชุมนุม</p>
                </div>
            </div>
            <div class="relative" style="height: 260px;">
                <canvas id="topClubsChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Grade Stats Chart -->
    <div class="glass rounded-2xl p-6 card-hover mb-8">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg">
                <i class="fas fa-graduation-cap text-white"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">จำนวนนักเรียนตามระดับชั้น</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">สถิติการสมัครแยกตามชั้นเรียน</p>
            </div>
        </div>
        <div class="relative" style="height: 280px;">
            <canvas id="gradeChart"></canvas>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="glass rounded-2xl shadow-xl p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-list-check text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-800 dark:text-white">รายการชุมนุม</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">เลือกดูรายชื่อชุมนุมที่เปิดรับสมัคร</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <label for="grade-filter" class="text-gray-700 dark:text-gray-300 font-medium whitespace-nowrap">ระดับชั้น:</label>
            <select id="grade-filter" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                <option value="">ทั้งหมด</option>
                <option value="ม.1">ม.1</option>
                <option value="ม.2">ม.2</option>
                <option value="ม.3">ม.3</option>
                <option value="ม.4">ม.4</option>
                <option value="ม.5">ม.5</option>
                <option value="ม.6">ม.6</option>
            </select>
        </div>
    </div>
</div>

<!-- Mobile Cards View -->
<div id="club-cards" class="md:hidden grid gap-4 mb-6">
    <!-- Cards will be populated by JavaScript -->
    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
        <div class="loader mx-auto mb-4" style="border: 3px solid rgba(59, 130, 246, 0.2); border-top: 3px solid #3b82f6; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite;"></div>
        กำลังโหลดข้อมูล...
    </div>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block glass rounded-2xl shadow-xl overflow-hidden">
    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="club-table" class="w-full display">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                        <th class="py-3 px-4 text-center font-bold">รหัสชุมนุม</th>
                        <th class="py-3 px-4 text-center font-bold">ชื่อชุมนุม</th>
                        <th class="py-3 px-4 text-center font-bold">รายละเอียด</th>
                        <th class="py-3 px-4 text-center font-bold">ครูที่ปรึกษา</th>
                        <th class="py-3 px-4 text-center font-bold">ระดับชั้น</th>
                        <th class="py-3 px-4 text-center font-bold">จำนวนรับ</th>
                        <th class="py-3 px-4 text-center font-bold">สถานะ</th>
                    </tr>
                </thead>
                <tbody id="club-table-body"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
let clubData = [];
let statusChart = null;
let topClubsChart = null;
let gradeChart = null;

// Animate counter
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 30;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toLocaleString();
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 30);
}

// Load Statistics
async function loadStats() {
    try {
        const response = await fetch('controllers/ClubController.php?action=stats');
        const data = await response.json();
        
        if (data.success) {
            // Update stat cards with animation
            animateCounter(document.getElementById('stat-total-clubs'), data.totalClubs);
            animateCounter(document.getElementById('stat-total-students'), data.totalStudents);
            animateCounter(document.getElementById('stat-open-clubs'), data.openClubs);
            animateCounter(document.getElementById('stat-full-clubs'), data.fullClubs);
            
            // Render charts
            renderStatusChart(data.openClubs, data.fullClubs);
            renderTopClubsChart(data.topClubs);
            renderGradeChart(data.gradeStats);
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Status Donut Chart
function renderStatusChart(open, full) {
    const ctx = document.getElementById('statusChart').getContext('2d');
    
    if (statusChart) statusChart.destroy();
    
    statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['เปิดรับสมัคร', 'เต็มแล้ว'],
            datasets: [{
                data: [open, full],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    'rgba(34, 197, 94, 1)',
                    'rgba(239, 68, 68, 1)'
                ],
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { family: 'Mali', size: 14 },
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `${context.label}: ${context.raw} ชุมนุม (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Top Clubs Bar Chart
function renderTopClubsChart(clubs) {
    const ctx = document.getElementById('topClubsChart').getContext('2d');
    
    if (topClubsChart) topClubsChart.destroy();
    
    const labels = clubs.map(c => c.name);
    const currentData = clubs.map(c => c.current);
    const maxData = clubs.map(c => c.max);
    
    topClubsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'สมาชิกปัจจุบัน',
                    data: currentData,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                },
                {
                    label: 'รับได้สูงสุด',
                    data: maxData,
                    backgroundColor: 'rgba(156, 163, 175, 0.4)',
                    borderColor: 'rgba(156, 163, 175, 0.8)',
                    borderWidth: 1,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { family: 'Mali', size: 12 },
                        usePointStyle: true
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(156, 163, 175, 0.2)'
                    },
                    ticks: {
                        font: { family: 'Mali' }
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Mali', size: 11 }
                    }
                }
            }
        }
    });
}

// Grade Stats Chart
function renderGradeChart(gradeStats) {
    const ctx = document.getElementById('gradeChart').getContext('2d');
    
    if (gradeChart) gradeChart.destroy();
    
    const labels = Object.keys(gradeStats);
    const data = Object.values(gradeStats);
    
    const gradientColors = [
        'rgba(239, 68, 68, 0.8)',   // ม.1 - Red
        'rgba(249, 115, 22, 0.8)',  // ม.2 - Orange
        'rgba(234, 179, 8, 0.8)',   // ม.3 - Yellow
        'rgba(34, 197, 94, 0.8)',   // ม.4 - Green
        'rgba(59, 130, 246, 0.8)',  // ม.5 - Blue
        'rgba(139, 92, 246, 0.8)'   // ม.6 - Purple
    ];
    
    gradeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'จำนวนนักเรียน',
                data: data,
                backgroundColor: gradientColors,
                borderColor: gradientColors.map(c => c.replace('0.8', '1')),
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Mali', size: 14, weight: 'bold' }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(156, 163, 175, 0.2)'
                    },
                    ticks: {
                        font: { family: 'Mali' }
                    }
                }
            }
        }
    });
}

// Render mobile cards
function renderMobileCards(data, filter = '') {
    const container = document.getElementById('club-cards');
    let filtered = data;
    
    if (filter) {
        filtered = data.filter(club => club.grade_levels && club.grade_levels.includes(filter));
    }
    
    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-search text-4xl mb-4 opacity-50"></i>
                <p>ไม่พบข้อมูลชุมนุม</p>
            </div>`;
        return;
    }
    
    container.innerHTML = filtered.map(club => {
        const current = parseInt(club.current_members_count || 0);
        const max = parseInt(club.max_members || 0);
        const percent = max > 0 ? Math.round((current / max) * 100) : 0;
        const isFull = current >= max;
        const barColor = isFull ? 'bg-red-500' : percent >= 70 ? 'bg-yellow-400' : 'bg-emerald-500';
        const statusBadge = isFull 
            ? '<span class="px-3 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold">เต็ม</span>'
            : '<span class="px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold">เปิดรับ</span>';
        
        return `
            <div class="glass rounded-2xl p-5 card-hover">
                <!-- Header -->
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-primary-600 dark:text-primary-400">${club.club_name || '-'}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">${club.advisor_teacher_name || '-'}</p>
                    </div>
                    ${statusBadge}
                </div>
                
                <!-- Description -->
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">${club.description || 'ไม่มีคำอธิบาย'}</p>
                
                <!-- Grade Levels -->
                <div class="flex flex-wrap gap-1 mb-4">
                    ${(club.grade_levels || '').split(',').map(g => 
                        `<span class="px-2 py-0.5 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold">${g.trim()}</span>`
                    ).join('')}
                </div>
                
                <!-- Progress Bar -->
                <div class="mb-2">
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                        <span>สมาชิก</span>
                        <span class="font-bold">${current} / ${max}</span>
                    </div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="${barColor} h-full transition-all duration-500" style="width: ${Math.min(percent, 100)}%"></div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

$(document).ready(function() {
    // Load statistics
    loadStats();
    
    // Fetch data for mobile cards
    fetch('controllers/ClubController.php?action=list')
        .then(r => r.json())
        .then(response => {
            if (response.data) {
                clubData = response.data;
                renderMobileCards(clubData);
            }
        })
        .catch(err => {
            console.error('Error fetching clubs:', err);
            document.getElementById('club-cards').innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                    <p>ไม่สามารถโหลดข้อมูลได้</p>
                </div>`;
        });

    // Desktop DataTable
    var table = $('#club-table').DataTable({
        "ajax": "controllers/ClubController.php?action=list",
        "columns": [
            { "data": "club_id", "visible": false },
            { "data": "club_name", className: "text-center font-bold text-primary-600 dark:text-primary-400" },
            { "data": "description", className: "text-gray-600 dark:text-gray-300" },
            { "data": "advisor_teacher_name", className: "text-center" },
            { "data": "grade_levels", className: "text-center" },
            { 
                "data": null,
                "render": function(data, type, row) {
                    var current = row.current_members_count ? parseInt(row.current_members_count) : 0;
                    var max = row.max_members ? parseInt(row.max_members) : 0;
                    var percent = (max > 0) ? Math.round((current / max) * 100) : 0;
                    if (percent > 100) percent = 100;
                    var barColor = percent >= 100 ? 'bg-red-500' : percent >= 70 ? 'bg-yellow-400' : 'bg-emerald-500';
                    return `
                        <div class="w-32 mx-auto">
                            <div class="relative h-6 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="absolute left-0 top-0 h-6 ${barColor} transition-all" style="width:${percent}%"></div>
                                <div class="absolute w-full text-xs text-center top-0 left-0 h-6 leading-6 font-bold text-gray-700 dark:text-gray-200">${current} / ${max}</div>
                            </div>
                        </div>`;
                },
                "className": "text-center"
            },
            { 
                "data": null,
                "render": function(data, type, row) {
                    var current = row.current_members_count ? parseInt(row.current_members_count) : 0;
                    var max = row.max_members ? parseInt(row.max_members) : 0;
                    if (current >= max) {
                        return '<span class="px-3 py-1 rounded-full bg-red-100 text-red-600 text-xs font-bold">เต็ม</span>';
                    }
                    return '<span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold">เปิดรับ</span>';
                },
                "className": "text-center"
            },
        ],
        "columnDefs": [
            { "targets": 0, "visible": false }
        ],
        "language": {
            "lengthMenu": "แสดง _MENU_ รายการ",
            "zeroRecords": "ไม่พบข้อมูลชุมนุม",
            "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
            "infoEmpty": "ไม่มีข้อมูล",
            "infoFiltered": "(กรองจาก _MAX_ รายการ)",
            "search": "ค้นหา:",
            "paginate": { "first": "แรก", "last": "สุดท้าย", "next": "ถัดไป", "previous": "ก่อนหน้า" }
        },
        "pageLength": 10,
        "order": [[1, "asc"]],
        "responsive": true
    });

    // Filter handler - works for both views
    $('#grade-filter').on('change', function() {
        const filter = $(this).val();
        // Desktop table
        table.column(4).search(filter).draw();
        // Mobile cards
        renderMobileCards(clubData, filter);
    });
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

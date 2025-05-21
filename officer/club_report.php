<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];
require_once('header.php');
?>
<!-- Tailwind CSS CDN -->


<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h5 class="m-0">รายงานกิจกรรมชมรม</h5>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card">


                <!-- Nav tabs -->
                <ul class="flex border-b my-4" id="reportTabs" role="tablist">
                    <li class="mr-2">
                        <a class="nav-link active inline-block px-6 py-2 rounded-t-lg font-semibold text-blue-700 bg-blue-100 transition-all duration-200 ease-in-out hover:bg-blue-200 focus:outline-none" id="room-tab" data-toggle="tab" href="#room" role="tab" aria-controls="room" aria-selected="true">
                            <svg class="inline w-5 h-5 mr-1 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 17l4 4 4-4m-4-5v9"></path><path d="M20.24 12.24A9 9 0 1 0 21 12"></path></svg>
                            รายห้อง
                        </a>
                    </li>
                    <li class="mr-2">
                        <a class="nav-link inline-block px-6 py-2 rounded-t-lg font-semibold text-gray-700 bg-gray-100 transition-all duration-200 ease-in-out hover:bg-blue-100 focus:outline-none" id="level-tab" data-toggle="tab" href="#level" role="tab" aria-controls="level" aria-selected="false">
                            <svg class="inline w-5 h-5 mr-1 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10h18M3 6h18M3 14h18M3 18h18"></path></svg>
                            รายชั้น
                        </a>
                    </li>
                    <li>
                        <a class="nav-link inline-block px-6 py-2 rounded-t-lg font-semibold text-gray-700 bg-gray-100 transition-all duration-200 ease-in-out hover:bg-blue-100 focus:outline-none" id="overview-tab" data-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="false">
                            <svg class="inline w-5 h-5 mr-1 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M8 12l2 2 4-4"></path></svg>
                            ภาพรวมทั้งโรงเรียน
                        </a>
                    </li>
                    <li>
                        <a class="nav-link inline-block px-6 py-2 rounded-t-lg font-semibold text-gray-700 bg-gray-100 transition-all duration-200 ease-in-out hover:bg-blue-100 focus:outline-none" id="club-tab" data-toggle="tab" href="#club" role="tab" aria-controls="club" aria-selected="false">
                            <svg class="inline w-5 h-5 mr-1 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0 0v8"></path></svg>
                            รายชุมนุม
                        </a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active animate-fade-in" id="room" role="tabpanel" aria-labelledby="room-tab">
                        <!-- เนื้อหารายห้อง -->
                        <div class="p-6 bg-blue-50 rounded-lg shadow-inner transition-all duration-300">
                            <p class="text-lg font-medium text-blue-800 mb-4">รายงานตามห้องเรียน</p>
                            <div class="flex flex-wrap gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-1" for="select-level">เลือกชั้น</label>
                                    <select id="select-level" class="block w-32 px-3 py-2 border border-blue-300 rounded focus:ring focus:ring-blue-200">
                                        <option value="">-- เลือกชั้น --</option>
                                        <option value="ม.1">ม.1</option>
                                        <option value="ม.2">ม.2</option>
                                        <option value="ม.3">ม.3</option>
                                        <option value="ม.4">ม.4</option>
                                        <option value="ม.5">ม.5</option>
                                        <option value="ม.6">ม.6</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-1" for="select-room">เลือกห้อง</label>
                                    <select id="select-room" class="block w-32 px-3 py-2 border border-blue-300 rounded focus:ring focus:ring-blue-200" disabled>
                                        <option value="">-- เลือกห้อง --</option>
                                    </select>
                                </div>
                            </div>
                            <div id="room-table-container">
                                <!-- ตารางจะแสดงตรงนี้ -->
                                <div class="text-gray-400 text-center py-8">กรุณาเลือกชั้นและห้อง</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade animate-fade-in" id="level" role="tabpanel" aria-labelledby="level-tab">
                        <!-- เนื้อหารายชั้น -->
                        <div class="p-6 bg-green-50 rounded-lg shadow-inner transition-all duration-300">
                            <p class="text-lg font-medium text-green-800 mb-4">รายงานตามชั้นเรียน</p>
                            <div class="flex flex-wrap gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-green-700 mb-1" for="select-level2">เลือกชั้น</label>
                                    <select id="select-level2" class="block w-32 px-3 py-2 border border-green-300 rounded focus:ring focus:ring-green-200">
                                        <option value="">-- เลือกชั้น --</option>
                                        <option value="ม.1">ม.1</option>
                                        <option value="ม.2">ม.2</option>
                                        <option value="ม.3">ม.3</option>
                                        <option value="ม.4">ม.4</option>
                                        <option value="ม.5">ม.5</option>
                                        <option value="ม.6">ม.6</option>
                                    </select>
                                </div>
                            </div>
                            <div id="level-table-container">
                                <div class="text-gray-400 text-center py-8">กรุณาเลือกชั้น</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade animate-fade-in" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <!-- เนื้อหาภาพรวมทั้งโรงเรียน -->
                        <div class="p-6 bg-yellow-50 rounded-lg shadow-inner transition-all duration-300">
                            <p class="text-lg font-medium text-yellow-800 mb-4">รายงานภาพรวมทั้งโรงเรียน</p>
                            <div id="overview-table-container">
                                <div class="text-gray-400 text-center py-8">กำลังโหลดข้อมูล...</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade animate-fade-in" id="club" role="tabpanel" aria-labelledby="club-tab">
                        <!-- เนื้อหารายชุมนุม -->
                        <div class="p-6 bg-purple-50 rounded-lg shadow-inner transition-all duration-300">
                            <p class="text-lg font-medium text-purple-800 mb-4">รายงานตามชุมนุม</p>
                            <div id="club-table-container">
                                <div class="text-gray-400 text-center py-8">กำลังโหลดข้อมูล...</div>
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Bootstrap JS (ถ้ายังไม่มี) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Tailwind tab switcher & animation -->
<script>
document.querySelectorAll('#reportTabs .nav-link').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        // Remove active classes
        document.querySelectorAll('#reportTabs .nav-link').forEach(t => {
            t.classList.remove('active', 'bg-blue-100', 'text-blue-700');
            t.classList.add('bg-gray-100', 'text-gray-700');
        });
        // Add active to clicked
        this.classList.add('active', 'bg-blue-100', 'text-blue-700');
        this.classList.remove('bg-gray-100', 'text-gray-700');
        // Switch tab content
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        const target = this.getAttribute('href');
        const pane = document.querySelector(target);
        if (pane) {
            pane.classList.add('show', 'active');
        }
    });
});
// Animation for fade-in
document.querySelectorAll('.tab-pane').forEach(pane => {
    pane.classList.add('transition-opacity', 'duration-300');
});
</script>
<style>
/* Simple fade-in animation */
.animate-fade-in {
    opacity: 0;
    transition: opacity 0.3s;
}
.tab-pane.active.show.animate-fade-in {
    opacity: 1;
}
</style>
<!-- แยก script ของแต่ละแท็บ -->
<script src="js/room_report.js"></script>
<script src="js/level_report.js"></script>
<script src="js/club_report.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"/>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script>
// OVERVIEW TAB: ภาพรวมทั้งโรงเรียน
const overviewTableContainer = document.getElementById('overview-table-container');
const overviewTab = document.getElementById('overview-tab');

function fetchOverviewData() {
    overviewTableContainer.innerHTML = '<div class="text-yellow-400 text-center py-8 animate-pulse">กำลังโหลดข้อมูล...</div>';
    fetch('api/fetch_club_report.php')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                let sum = {
                    "ม.1": 0, "ม.2": 0, "ม.3": 0, "ม.4": 0, "ม.5": 0, "ม.6": 0, total: 0
                };
                data.forEach(row => {
                    if (row.grade_levels) {
                        for (let k of ["ม.1", "ม.2", "ม.3", "ม.4", "ม.5", "ม.6"]) {
                            if (row.grade_levels[k] !== undefined) sum[k] += row.grade_levels[k];
                        }
                    }
                    sum.total += row.total_count;
                });

                fetch('api/fetch_student_count_by_level.php')
                    .then(res2 => res2.json())
                    .then(studentData => {
                        // Chart.js card
                        let chartId = "overview-bar-chart";
                        let chartColors = [
                            "#fbbf24", "#34d399", "#60a5fa", "#f472b6", "#a78bfa", "#f87171"
                        ];
                        let chartLabels = ["ม.1", "ม.2", "ม.3", "ม.4", "ม.5", "ม.6"];
                        let chartData = chartLabels.map(k => sum[k]);
                        let chartMax = Math.max(...Object.values(studentData), ...chartData) + 5;

                        let html = `
                        <div class="flex flex-wrap gap-4 justify-center mb-6">
                            ${chartLabels.map((k, i) => `
                                <div class="bg-white rounded-lg shadow-lg p-4 w-40 hover:scale-105 transition-transform duration-200 border-t-4" style="border-color: ${chartColors[i]}">
                                    <div class="text-sm text-gray-500 mb-1 text-center">${k}</div>
                                    <div class="text-3xl font-bold text-gray-800 text-center animate-pulse">${sum[k]}</div>
                                    <div class="text-xs text-gray-400 text-center">สมัครแล้ว / ทั้งหมด</div>
                                    <div class="flex justify-center items-end h-16 mt-2">
                                        <div class="relative w-6 mx-1">
                                            <div class="absolute bottom-0 left-0 w-6 rounded-t bg-yellow-400 transition-all duration-700"
                                                style="height:${studentData[k] ? (sum[k]/studentData[k]*100) : 0}%; min-height:8px; max-height:100%;"></div>
                                            <div class="absolute bottom-0 left-0 w-6 h-full border border-yellow-300 rounded-t"></div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 text-center mt-1">${sum[k]} / ${studentData[k] ?? 0}</div>
                                </div>
                            `).join('')}
                            <div class="bg-yellow-100 rounded-lg shadow-lg p-4 w-40 hover:scale-105 transition-transform duration-200 border-t-4 border-yellow-400">
                                <div class="text-sm text-gray-700 mb-1 text-center font-bold">รวมทั้งสิ้น</div>
                                <div class="text-3xl font-bold text-yellow-700 text-center animate-bounce">${sum.total}</div>
                                <div class="text-xs text-gray-400 text-center">สมัครแล้ว / ทั้งหมด</div>
                                <div class="flex justify-center items-end h-16 mt-2">
                                    <div class="relative w-6 mx-1">
                                        <div class="absolute bottom-0 left-0 w-6 rounded-t bg-yellow-400 transition-all duration-700"
                                            style="height:${Object.values(studentData).reduce((a,b)=>a+b,0) ? (sum.total/Object.values(studentData).reduce((a,b)=>a+b,0)*100) : 0}%; min-height:8px; max-height:100%;"></div>
                                        <div class="absolute bottom-0 left-0 w-6 h-full border border-yellow-300 rounded-t"></div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-700 text-center mt-1 font-bold">${sum.total} / ${Object.values(studentData).reduce((a,b)=>a+b,0)}</div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded shadow text-sm" id="overview-report-table">
                            <thead>
                                <tr class="bg-yellow-200 text-yellow-900">
                                    <th class="px-4 py-2 text-left">ระดับชั้น</th>
                                    <th class="px-4 py-2 text-center">จำนวนที่สมัครแล้ว</th>
                                    <th class="px-4 py-2 text-center">ยอดนักเรียนทั้งหมด</th>
                                </tr>
                            </thead>
                            <tbody>
                        `;
                        let total_students = 0;
                        for (let k of chartLabels) {
                            const reg = sum[k];
                            const all = studentData[k] !== undefined ? studentData[k] : 0;
                            total_students += all;
                            html += `
                                <tr class="hover:bg-yellow-50">
                                    <td class="px-4 py-2">${k}</td>
                                    <td class="px-4 py-2 text-center">${reg}</td>
                                    <td class="px-4 py-2 text-center">${all}</td>
                                </tr>
                            `;
                        }
                        html += `
                                <tr class="bg-yellow-100 font-bold">
                                    <td class="px-4 py-2">รวมทั้งสิ้น</td>
                                    <td class="px-4 py-2 text-center">${sum.total}</td>
                                    <td class="px-4 py-2 text-center">${total_students}</td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                        <div class="flex justify-center mt-8">
                            <canvas id="${chartId}" height="120"></canvas>
                        </div>
                        `;
                        overviewTableContainer.innerHTML = html;

                        // Chart.js bar chart
                        if (window.Chart) {
                            const ctx = document.getElementById(chartId).getContext('2d');
                            new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: chartLabels,
                                    datasets: [
                                        {
                                            label: 'สมัครแล้ว',
                                            data: chartData,
                                            backgroundColor: chartColors,
                                            borderRadius: 8
                                        },
                                        {
                                            label: 'นักเรียนทั้งหมด',
                                            data: chartLabels.map(k => studentData[k] ?? 0),
                                            backgroundColor: 'rgba(156,163,175,0.2)',
                                            borderColor: 'rgba(156,163,175,0.5)',
                                            borderWidth: 2,
                                            borderRadius: 8
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: { display: true },
                                        tooltip: { enabled: true }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: chartMax
                                        }
                                    },
                                    animation: {
                                        duration: 1200,
                                        easing: 'easeOutBounce'
                                    }
                                }
                            });
                        }
                    })
                    .catch(() => {
                        overviewTableContainer.innerHTML = '<div class="text-red-400 text-center py-8">เกิดข้อผิดพลาดในการโหลดข้อมูลนักเรียน</div>';
                    });
            } else {
                overviewTableContainer.innerHTML = '<div class="text-red-400 text-center py-8">ไม่พบข้อมูล</div>';
            }
        })
        .catch(() => {
            overviewTableContainer.innerHTML = '<div class="text-red-400 text-center py-8">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
        });
}

if (overviewTab) {
    overviewTab.addEventListener('click', function() {
        fetchOverviewData();
    });
}
</script>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>

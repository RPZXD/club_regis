<?php

$config = json_decode(file_get_contents('config.json'), true);
$global = $config['global'];
require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h5 class="m-0">Best For Teen 2025 - จัดการกิจกรรม</h5>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header bg-blue-600 text-white font-semibold text-lg ">
                        <span>รายการกิจกรรม</span>
                    </div>
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div id="summary" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                            <div class="p-3 rounded bg-blue-50 border border-blue-200">
                                <div class="text-xs text-blue-600">จำนวนกิจกรรม</div>
                                <div id="card-activities" class="text-2xl font-bold text-blue-800">0</div>
                            </div>
                            <div class="p-3 rounded bg-green-50 border border-green-200">
                                <div class="text-xs text-green-600">ที่รับทั้งหมด</div>
                                <div id="card-capacity" class="text-2xl font-bold text-green-800">0</div>
                            </div>
                            <div class="p-3 rounded bg-indigo-50 border border-indigo-200">
                                <div class="text-xs text-indigo-600">สมัครแล้ว</div>
                                <div id="card-registered" class="text-2xl font-bold text-indigo-800">0</div>
                            </div>
                            <div class="p-3 rounded bg-yellow-50 border border-yellow-200">
                                <div class="text-xs text-yellow-600">อัตราการเต็ม</div>
                                <div id="card-fill" class="text-2xl font-bold text-yellow-800">0%</div>
                            </div>
                        </div>

                        <!-- Chart -->
                        <div class="mb-6">
                            <canvas id="best-chart" height="120"></canvas>
                        </div>

                        <div class="overflow-x-auto">
                            <table id="best-table" class="display min-w-full bg-white border border-gray-200 rounded shadow">
                                <thead>
                                    <tr class="bg-blue-500 text-white font-semibold">
                                        <th class="py-2 px-4 text-center border-b">รหัส</th>
                                        <th class="py-2 px-4 text-center border-b">ชื่อกิจกรรม</th>
                                        <th class="py-2 px-4 text-center border-b">ระดับชั้น</th>
                                        <th class="py-2 px-4 text-center border-b">จำนวนที่รับ</th>
                                        <th class="py-2 px-4 text-center border-b">ปี</th>
                                        <th class="py-2 px-4 text-center border-b">สมาชิก</th>
                                    </tr>
                                </thead>
                                <tbody id="best-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </div>
        </section>
    </div>
    <?php require_once('footer.php');?>
</div>
<?php require_once('script.php');?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
let table; let currentActivityId = null; let bestChart = null;

function showToast(message, type = 'info') {
    const icon = ['success','error','warning','info','question'].includes(type) ? type : 'info';
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
}

function fetchList() {
    return fetch('controllers/BestActivityController.php?action=list').then(r => r.json());
}

function render(list) {
    if (!table) {
        table = $('#best-table').DataTable({
            ordering: true,
            order: [[0,'asc']],
            language: { search: 'ค้นหา:' }
        });
    }
    table.clear();
    list.forEach(a => {
        const current = parseInt(a.current_members_count||0);
        const max = parseInt(a.max_members||0);
        const percent = max > 0 ? Math.round((current/max)*100) : 0;
        let barColor = 'bg-green-400';
        if (percent >= 100) barColor = 'bg-red-500';
        else if (percent >= 70) barColor = 'bg-yellow-400';
        const progress = `
            <div class="w-36 mx-auto">
                <div class="relative h-5 bg-gray-200 rounded-full overflow-hidden">
                    <div class="absolute left-0 top-0 h-5 ${barColor}" style="width:${percent}%"></div>
                    <div class="absolute w-full text-xs text-center top-0 left-0 h-5 leading-5">${current} / ${max}</div>
                </div>
            </div>`;
        table.row.add([
            a.id,
            `<span class=\"font-semibold text-blue-700\">${a.name}</span>`,
            a.grade_levels,
            progress,
            a.year,
            current
        ]);
    });
    table.draw(false);

    // Update summary and chart
    updateSummary(list);
    renderChart(list);
}

function updateSummary(list) {
    const totalActivities = list.length;
    const totalCapacity = list.reduce((s,a)=> s + (parseInt(a.max_members||0)), 0);
    const totalCurrent = list.reduce((s,a)=> s + (parseInt(a.current_members_count||0)), 0);
    const fill = totalCapacity > 0 ? Math.round((totalCurrent/totalCapacity)*100) : 0;
    document.getElementById('card-activities').textContent = totalActivities;
    document.getElementById('card-capacity').textContent = totalCapacity;
    document.getElementById('card-registered').textContent = totalCurrent;
    document.getElementById('card-fill').textContent = fill + '%';
}

function renderChart(list) {
    const ordered = [...list].sort((a,b)=> (parseInt(b.max_members||0) - parseInt(a.max_members||0)));
    const top = ordered.slice(0, 10);
    const labels = top.map(a=> a.name || ('กิจกรรม #' + a.id));
    const current = top.map(a=> parseInt(a.current_members_count||0));
    const capacity = top.map(a=> parseInt(a.max_members||0));
    const remaining = capacity.map((c, i)=> Math.max(0, c - current[i]));
    const ctx = document.getElementById('best-chart').getContext('2d');
    if (bestChart) { bestChart.destroy(); }
    bestChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'สมัครแล้ว',
                    data: current,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                },
                {
                    label: 'คงเหลือ',
                    data: remaining,
                    backgroundColor: 'rgba(201, 203, 207, 0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            }
        }
    });
}


function load() {
    fetchList().then(d=>{ if (d.data) render(d.data); });
}

// no management events; read-only

 document.addEventListener('DOMContentLoaded', load);
</script>
</body>
</html>

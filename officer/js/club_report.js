const clubTableContainer = document.getElementById('club-table-container');
const clubTab = document.getElementById('club-tab');

function fetchClubData() {
    clubTableContainer.innerHTML = '<div class="text-purple-400 text-center py-8 animate-pulse">กำลังโหลดข้อมูล...</div>';
    fetch('api/fetch_club_report.php')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                // Chart: แสดงจำนวนสมาชิกแต่ละชุมนุม (รวมทุกระดับชั้น)
                const chartLabels = data.map(row => row.club_name);
                const chartData = data.map(row => row.total_count);
                const chartColors = [
                    "#fbbf24", "#34d399", "#60a5fa", "#f472b6", "#a78bfa", "#f87171", "#facc15", "#818cf8", "#fb7185", "#4ade80"
                ];
                // ...sum by level for card...
                let sum = { "ม.1": 0, "ม.2": 0, "ม.3": 0, "ม.4": 0, "ม.5": 0, "ม.6": 0, total: 0 };
                data.forEach(row => {
                    if (row.grade_levels) {
                        for (let k of ["ม.1", "ม.2", "ม.3", "ม.4", "ม.5", "ม.6"]) {
                            if (row.grade_levels[k] !== undefined) sum[k] += row.grade_levels[k];
                        }
                    }
                    sum.total += row.total_count;
                });

                let cardHtml = `
                <div class="flex flex-wrap gap-4 justify-center mb-6">
                    ${["ม.1", "ม.2", "ม.3", "ม.4", "ม.5", "ม.6"].map((k, i) => `
                        <div class="bg-white rounded-lg shadow-lg p-4 w-40 hover:scale-105 transition-transform duration-200 border-t-4" style="border-color: ${chartColors[i]}">
                            <div class="text-sm text-gray-500 mb-1 text-center">${k}</div>
                            <div class="text-3xl font-bold text-gray-800 text-center animate-pulse">${sum[k]}</div>
                            <div class="text-xs text-gray-400 text-center">จำนวนสมาชิก</div>
                            <div class="flex justify-center items-end h-16 mt-2">
                                <div class="relative w-6 mx-1">
                                    <div class="absolute bottom-0 left-0 w-6 rounded-t" style="background:${chartColors[i]};height:${sum[k] > 0 ? Math.min(sum[k]*8, 100) : 8}px;transition:all 0.7s"></div>
                                    <div class="absolute bottom-0 left-0 w-6 h-full border border-gray-200 rounded-t"></div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                    <div class="bg-purple-100 rounded-lg shadow-lg p-4 w-40 hover:scale-105 transition-transform duration-200 border-t-4 border-purple-400">
                        <div class="text-sm text-gray-700 mb-1 text-center font-bold">รวมทั้งสิ้น</div>
                        <div class="text-3xl font-bold text-purple-700 text-center animate-bounce">${sum.total}</div>
                        <div class="text-xs text-gray-400 text-center">จำนวนสมาชิก</div>
                        <div class="flex justify-center items-end h-16 mt-2">
                            <div class="relative w-6 mx-1">
                                <div class="absolute bottom-0 left-0 w-6 rounded-t bg-purple-400" style="height:${sum.total > 0 ? Math.min(sum.total*8, 100) : 8}px;transition:all 0.7s"></div>
                                <div class="absolute bottom-0 left-0 w-6 h-full border border-purple-300 rounded-t"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center mt-8">
                    <canvas id="club-bar-chart" height="180"></canvas>
                </div>
                `;

                let html = cardHtml + `
                <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow text-sm" id="club-report-table">
                    <thead>
                        <tr class="bg-purple-200 text-purple-900">
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">ชื่อชุมนุม</th>
                            <th class="px-4 py-2 text-left">ครูที่ปรึกษา</th>
                            <th class="px-4 py-2 text-center">ม.1</th>
                            <th class="px-4 py-2 text-center">ม.2</th>
                            <th class="px-4 py-2 text-center">ม.3</th>
                            <th class="px-4 py-2 text-center">ม.4</th>
                            <th class="px-4 py-2 text-center">ม.5</th>
                            <th class="px-4 py-2 text-center">ม.6</th>
                            <th class="px-4 py-2 text-center">รวมทั้งสิ้น</th>
                        </tr>
                    </thead>
                    <tbody>
                `;
                data.forEach((row, idx) => {
                    // รองรับทุกระดับชั้น ม.1-ม.6
                    const m1 = row.grade_levels && row.grade_levels["ม.1"] !== undefined ? row.grade_levels["ม.1"] : 0;
                    const m2 = row.grade_levels && row.grade_levels["ม.2"] !== undefined ? row.grade_levels["ม.2"] : 0;
                    const m3 = row.grade_levels && row.grade_levels["ม.3"] !== undefined ? row.grade_levels["ม.3"] : 0;
                    const m4 = row.grade_levels && row.grade_levels["ม.4"] !== undefined ? row.grade_levels["ม.4"] : 0;
                    const m5 = row.grade_levels && row.grade_levels["ม.5"] !== undefined ? row.grade_levels["ม.5"] : 0;
                    const m6 = row.grade_levels && row.grade_levels["ม.6"] !== undefined ? row.grade_levels["ม.6"] : 0;
                    html += `
                        <tr class="hover:bg-purple-50">
                            <td class="px-4 py-2">${idx+1}</td>
                            <td class="px-4 py-2">${row.club_name}</td>
                            <td class="px-4 py-2">${row.advisor}</td>
                            <td class="px-4 py-2 text-center">${m1}</td>
                            <td class="px-4 py-2 text-center">${m2}</td>
                            <td class="px-4 py-2 text-center">${m3}</td>
                            <td class="px-4 py-2 text-center">${m4}</td>
                            <td class="px-4 py-2 text-center">${m5}</td>
                            <td class="px-4 py-2 text-center">${m6}</td>
                            <td class="px-4 py-2 text-center font-bold">${row.total_count}</td>
                        </tr>
                    `;
                });
                html += `
                    </tbody>
                </table>
                </div>
                `;
                clubTableContainer.innerHTML = html;

                // Chart.js bar chart: จำนวนสมาชิกแต่ละชุมนุม
                if (window.Chart) {
                    const ctx = document.getElementById("club-bar-chart").getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [
                                {
                                    label: 'จำนวนสมาชิก',
                                    data: chartData,
                                    backgroundColor: chartLabels.map((_, i) => chartColors[i % chartColors.length]),
                                    borderRadius: 8
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                                tooltip: { enabled: true }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        autoSkip: false,
                                        maxRotation: 60,
                                        minRotation: 30,
                                        font: { size: 12 }
                                    }
                                },
                                y: {
                                    beginAtZero: true
                                }
                            },
                            animation: {
                                duration: 1200,
                                easing: 'easeOutBounce'
                            }
                        }
                    });
                }
                if (window.jQuery && window.jQuery.fn.dataTable) {
                    $('#club-report-table').DataTable({
                        // ...DataTables config (copy from your main file)...
                        "language": {
                            "lengthMenu": "แสดง _MENU_ แถว",
                            "zeroRecords": "ไม่พบข้อมูล",
                            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
                            "infoEmpty": "ไม่มีข้อมูล",
                            "infoFiltered": "(กรองจาก _MAX_ แถว)",
                            "search": "ค้นหา:",
                            "paginate": {
                                "first": "หน้าแรก",
                                "last": "หน้าสุดท้าย",
                                "next": "ถัดไป",
                                "previous": "ก่อนหน้า"
                            }
                        },
                        "order": [[0, "asc"]],
                        "pageLength": 10,
                        "lengthMenu": [5, 10, 25, 50, 100],
                        "pagingType": "simple",
                        "searching": true,
                        "info": true,
                        "autoWidth": false,
                        "responsive": true,
                        dom: '<"flex flex-wrap items-center justify-between mb-2"Bf>rt<"flex flex-wrap items-center justify-between mt-2"lip>',
                        buttons: [
                            {
                                extend: 'copy',
                                text: 'คัดลอก',
                                className: 'bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'excel',
                                text: 'ส่งออก Excel',
                                className: 'bg-green-200 hover:bg-green-300 text-green-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'pdf',
                                text: 'ส่งออก PDF',
                                className: 'bg-red-200 hover:bg-red-300 text-red-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'csv',
                                text: 'ส่งออก CSV',
                                className: 'bg-blue-200 hover:bg-blue-300 text-blue-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'print',
                                text: 'พิมพ์',
                                className: 'bg-yellow-200 hover:bg-yellow-300 text-yellow-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            }
                        ],
                        drawCallback: function() {
                            $('.dataTables_filter input').addClass('border border-purple-300 rounded px-2 py-1 ml-2');
                            $('.dataTables_length select').addClass('border border-purple-300 rounded px-2 py-1 ml-2');
                            $('.dt-buttons button').addClass('transition-all duration-150');
                        }
                    });
                }
            } else {
                clubTableContainer.innerHTML = '<div class="text-red-400 text-center py-8">ไม่พบข้อมูล</div>';
            }
        })
        .catch(() => {
            clubTableContainer.innerHTML = '<div class="text-red-400 text-center py-8">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
        });
}

if (clubTab) {
    clubTab.addEventListener('click', function() {
        fetchClubData();
    });
}

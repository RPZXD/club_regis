const selectLevel2 = document.getElementById('select-level2');
const levelTableContainer = document.getElementById('level-table-container');

if (selectLevel2 && levelTableContainer) {
    selectLevel2.addEventListener('change', function() {
        const level = this.value;
        if (level) {
            fetchLevelData(level);
        } else {
            levelTableContainer.innerHTML = '<div class="text-gray-400 text-center py-8">กรุณาเลือกชั้น</div>';
        }
    });
}

function fetchLevelData(level) {
    levelTableContainer.innerHTML = '<div class="text-green-400 text-center py-8 animate-pulse">กำลังโหลดข้อมูล...</div>';
    fetch('api/fetch_level_report.php?level=' + encodeURIComponent(level))
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                let html = `
                <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow text-sm" id="level-report-table">
                    <thead>
                        <tr class="bg-green-200 text-green-900">
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">ห้อง</th>
                            <th class="px-4 py-2 text-left">จำนวนนักเรียน</th>
                            <th class="px-4 py-2 text-left">ชุมนุมที่สมัครมากที่สุด</th>
                            <th class="px-4 py-2 text-left">จำนวนสมาชิก</th>
                        </tr>
                    </thead>
                    <tbody>
                `;
                data.forEach((row, idx) => {
                    html += `
                        <tr class="hover:bg-green-50">
                            <td class="px-4 py-2">${idx+1}</td>
                            <td class="px-4 py-2">${row.room}</td>
                            <td class="px-4 py-2">${row.student_count}</td>
                            <td class="px-4 py-2">${row.top_club}</td>
                            <td class="px-4 py-2">${row.top_club_count}</td>
                        </tr>
                    `;
                });
                html += `
                    </tbody>
                </table>
                </div>
                `;
                levelTableContainer.innerHTML = html;
                if (window.jQuery && window.jQuery.fn.dataTable) {
                    $('#level-report-table').DataTable({
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
                            $('.dataTables_filter input').addClass('border border-green-300 rounded px-2 py-1 ml-2');
                            $('.dataTables_length select').addClass('border border-green-300 rounded px-2 py-1 ml-2');
                            $('.dt-buttons button').addClass('transition-all duration-150');
                        }
                    });
                }
            } else {
                levelTableContainer.innerHTML = '<div class="text-red-400 text-center py-8">ไม่พบข้อมูล</div>';
            }
        })
        .catch(() => {
            levelTableContainer.innerHTML = '<div class="text-red-400 text-center py-8">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
        });
}

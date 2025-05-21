// ตัวอย่างข้อมูลห้อง (ควร fetch จาก backend จริง)
const roomsByLevel = {
    "ม.1": [1,2,3,4,5,6,7,8,9,10,11,12],
    "ม.2": [1,2,3,4,5,6,7,8,9,10,11,12],
    "ม.3": [1,2,3,4,5,6,7,8,9,10,11,12],
    "ม.4": [1,2,3,4,5,6,7],
    "ม.5": [1,2,3,4,5,6,7],
    "ม.6": [1,2,3,4,5,6,7]
};

const selectLevel = document.getElementById('select-level');
const selectRoom = document.getElementById('select-room');
const tableContainer = document.getElementById('room-table-container');

if (selectLevel && selectRoom && tableContainer) {
    selectLevel.addEventListener('change', function() {
        const level = this.value;
        selectRoom.innerHTML = '<option value="">-- เลือกห้อง --</option>';
        if (roomsByLevel[level]) {
            roomsByLevel[level].forEach(room => {
                selectRoom.innerHTML += `<option value="${room}">${room}</option>`;
            });
            selectRoom.disabled = false;
        } else {
            selectRoom.disabled = true;
        }
        tableContainer.innerHTML = '<div class="text-gray-400 text-center py-8">กรุณาเลือกชั้นและห้อง</div>';
    });

    selectRoom.addEventListener('change', function() {
        const level = selectLevel.value;
        const room = this.value;
        if (level && room) {
            fetchRoomData(level, room);
        } else {
            tableContainer.innerHTML = '<div class="text-gray-400 text-center py-8">กรุณาเลือกชั้นและห้อง</div>';
        }
    });
}

function fetchRoomData(level, room) {
    tableContainer.innerHTML = '<div class="text-blue-400 text-center py-8 animate-pulse">กำลังโหลดข้อมูล...</div>';
    fetch('api/fetch_room_report.php?level=' + encodeURIComponent(level) + '&room=' + encodeURIComponent(room))
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                let html = `
                <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow text-sm" id="room-report-table">
                    <thead>
                        <tr class="bg-blue-200 text-blue-900">
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">เลขประจำตัว</th>
                            <th class="px-4 py-2 text-left">ชื่อ-สกุล</th>
                            <th class="px-4 py-2 text-left">ชั้น/ห้อง</th>
                            <th class="px-4 py-2 text-left">เลขที่</th>
                            <th class="px-4 py-2 text-left">ชุมนุมที่สมัคร</th>
                            <th class="px-4 py-2 text-left">ครูที่ปรึกษาชุมนุม</th>
                        </tr>
                    </thead>
                    <tbody>
                `;
                data.forEach((row, idx) => {
                    html += `
                        <tr class="hover:bg-blue-50">
                            <td class="px-4 py-2">${idx+1}</td>
                            <td class="px-4 py-2">${row.student_id}</td>
                            <td class="px-4 py-2">${row.fullname}</td>
                            <td class="px-4 py-2">${row.level}/${row.room}</td>
                            <td class="px-4 py-2">${row.number}</td>
                            <td class="px-4 py-2">${row.club}</td>
                            <td class="px-4 py-2">${row.advisor}</td>
                        </tr>
                    `;
                });
                html += `
                    </tbody>
                </table>
                </div>
                `;
                tableContainer.innerHTML = html;
                if (window.jQuery && window.jQuery.fn.dataTable) {
                    $('#room-report-table').DataTable({
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
                            $('.dataTables_filter input').addClass('border border-blue-300 rounded px-2 py-1 ml-2');
                            $('.dataTables_length select').addClass('border border-blue-300 rounded px-2 py-1 ml-2');
                            $('.dt-buttons button').addClass('transition-all duration-150');
                        }
                    });
                }
            } else {
                tableContainer.innerHTML = '<div class="text-red-400 text-center py-8">ไม่พบข้อมูล</div>';
            }
        })
        .catch(() => {
            tableContainer.innerHTML = '<div class="text-red-400 text-center py-8">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
        });
}

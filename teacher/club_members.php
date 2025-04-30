<?php
require_once('../includes/header.php');
require_once('../config/Database.php');

// ตรวจสอบ session เพื่อป้องกันการเข้าถึงโดยไม่ได้ login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Teacher_login'])) {
    header('Location: ../login.php');
    exit();
}

?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('../includes/wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">จัดการสมาชิกในชุมนุม</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header bg-blue-600 text-white font-semibold text-lg">
                        <form id="club-select-form" class="flex items-center gap-2" onsubmit="return false;">
                            <label for="club_id" class="font-medium">เลือกชุมนุม:</label>
                            <select name="club_id" id="club_id" class="border text-black border-gray-300 rounded p-2 w-60">
                                <option class="text-center text-black" value="">-- เลือกชุมนุม --</option>
                                <!-- Clubs will be loaded here via JS -->
                            </select>
                            <button type="button" id="print-btn" class="ml-4 btn bg-yellow-400 text-gray-700 hover:bg-yellow-600 hover:text-gray-900" style="display:none;">
                                <i class="fa fa-print"></i> พิมพ์รายชื่อ
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div id="club-info" class="mb-4" style="display:none;">
                            <h5 class="font-bold mb-1" id="club-title"></h5>
                            <div id="advisor-info"></div>
                        </div>
                        <div class="overflow-x-auto">
                            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
                            <table id="members-table" class="min-w-full bg-white border border-gray-200 rounded shadow display">
                                <thead>
                                    <tr class="bg-blue-500 text-white font-semibold">
                                        <th class="py-2 px-4 text-center border-b">ลำดับ</th>
                                        <th class="py-2 px-4 text-center border-b">รหัสนักเรียน</th>
                                        <th class="py-2 px-4 text-center border-b">ชื่อนักเรียน</th>
                                        <th class="py-2 px-4 text-center border-b">ชั้น</th>
                                        <th class="py-2 px-4 text-center border-b">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="members-table-body">
                                    <!-- DataTables will populate here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../includes/footer.php'); ?>
</div>
<?php require_once('../includes/script.php'); ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
    // โหลดรายชื่อชุมนุมผ่าน AJAX
    let clubsData = [];
    function loadClubs() {
        $.ajax({
            url: '../controllers/ClubMemberController.php',
            method: 'GET',
            data: { action: 'clubs' },
            dataType: 'json',
            success: function(res) {
                clubsData = res;
                var $clubSelect = $('#club_id');
                $clubSelect.empty();
                $clubSelect.append('<option class="text-center text-black" value="">-- เลือกชุมนุม --</option>');
                if (Array.isArray(res) && res.length > 0) {
                    res.forEach(function(club) {
                        $clubSelect.append(
                            $('<option>', {
                                value: club.club_id,
                                text: club.club_name,
                                class: 'text-center text-black',
                                'data-advisor': club.advisor_teacher || '',
                                'data-phone': club.advisor_phone || ''
                            })
                        );
                    });
                } else {
                    $clubSelect.append('<option value="">ไม่พบชุมนุม</option>');
                }
                // ถ้ามี club_id ใน url (refresh หรือกลับมาหน้าเดิม)
                const urlParams = new URLSearchParams(window.location.search);
                const selectedClub = urlParams.get('club_id');
                if (selectedClub) {
                    $clubSelect.val(selectedClub).trigger('change');
                }
            },
            error: function(xhr, status, error) {
                var $clubSelect = $('#club_id');
                $clubSelect.empty();
                $clubSelect.append('<option value="">เกิดข้อผิดพลาดในการโหลดชุมนุม</option>');
            }
        });
    }

    var table = $('#members-table').DataTable({
        paging: true,
        searching: true,
        info: false,
        ordering: false,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json"
        }
    });

    function loadMembers(club_id) {
        if (!club_id) {
            table.clear().draw();
            $('#club-info').hide();
            $('#print-btn').hide();
            return;
        }
        // แสดงข้อมูลหัวกระดาษ
        let club = clubsData.find(c => c.club_id == club_id);
        if (club) {
            $('#club-title').text('รายชื่อชุมนุม ' + (club.club_name || ''));
            $('#advisor-info').html(
                'ครูที่ปรึกษา ' + (club.advisor_teacher_name || club.advisor_teacher || '-') +
                (club.advisor_phone ? ' เบอร์โทร ' + club.advisor_phone : '')
            );
            $('#club-info').show();
            $('#print-btn').show();
        } else {
            $('#club-info').hide();
            $('#print-btn').hide();
        }

        $.ajax({
            url: '../controllers/ClubMemberController.php',
            method: 'GET',
            data: { action: 'list', club_id: club_id },
            dataType: 'json',
            success: function(res) {
                table.clear();
                if (res && res.length > 0) {
                    res.forEach(function(row, idx) {
                        table.row.add([
                            '<div class="text-center">' + (idx + 1) + '</div>',
                            '<div class="text-center">' + row.student_id + '</div>',
                            row.student_name,
                            '<div class="text-center">' + row.class_room + '</div>',
                            '<div class="text-center"><button class="btn btn-danger btn-sm remove-member" data-id="'+row.member_id+'">ลบ</button></div>'
                        ]);
                    });
                }
                table.draw();
            }
        });
    }

    loadClubs();

    $('#club_id').on('change', function() {
        loadMembers(this.value);
        // update url parameter (optional)
        const url = new URL(window.location);
        if (this.value) {
            url.searchParams.set('club_id', this.value);
        } else {
            url.searchParams.delete('club_id');
        }
        window.history.replaceState({}, '', url);
    });

    $('#members-table').on('click', '.remove-member', function() {
        var memberId = $(this).data('id');
        Swal.fire({
            title: 'ยืนยันการลบสมาชิกนี้?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../controllers/ClubMemberController.php',
                    method: 'POST',
                    data: { action: 'delete', member_id: memberId },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            $('#club_id').trigger('change');
                            Swal.fire('ลบสำเร็จ', '', 'success');
                        } else {
                            Swal.fire('เกิดข้อผิดพลาด', res.message || 'ไม่สามารถลบได้', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถลบได้', 'error');
                    }
                });
            }
        });
    });

    // ปุ่มพิมพ์
    $('#print-btn').on('click', function() {
        // เตรียมข้อมูลลายเซ็นครูที่ปรึกษา
        let club_id = $('#club_id').val();
        let club = clubsData.find(c => c.club_id == club_id);
        let teacherSignatures = '';
        if (club && club.advisor_teacher_name) {
            teacherSignatures += '<div style="margin-top:40px;text-align:right;">';
            teacherSignatures += '<div style="display:inline-block;text-align:center;">';
            teacherSignatures += '<p style="font-size:16px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ...............................................ครูที่ปรึกษาชุมนุม</p>';
            teacherSignatures += `<p style="font-size:16px;">(${club.advisor_teacher_name})</p>`;
            teacherSignatures += '</div></div>';
        }

        let printContents = '';
        printContents += '<div style="text-align:center;">';
        printContents += '<h3>' + $('#club-title').text() + '</h3>';
        printContents += '<div>' + $('#advisor-info').html() + '</div>';
        printContents += '</div>';
        printContents += `
        <table border="1" cellspacing="0" cellpadding="6" style="width:100%;margin-top:10px;font-size:16px;border-collapse:collapse;">
            <thead>
                <tr style="background:#2563eb;color:#fff;font-weight:bold;">
        `;
        // ข้ามคอลัมน์ "จัดการ" และเพิ่ม "หมายเหตุ"
        $('#members-table thead th').each(function(idx) {
            if (idx < 4) {
                printContents += '<th style="padding:8px 4px;text-align:center;border:1px solid #2563eb;">' + $(this).text() + '</th>';
            }
        });
        printContents += '<th style="padding:8px 4px;text-align:center;border:1px solid #2563eb;">หมายเหตุ</th>';
        printContents += `
                </tr>
            </thead>
            <tbody>
        `;
        table.rows({search:'applied'}).every(function(rowIdx, tableLoop, rowLoop){
            var data = this.data();
            printContents += '<tr>';
            for(let i=0; i<4; i++) {
                printContents += '<td style="text-align:center;border:1px solid #2563eb;">' + $('<div>').html(data[i]).text() + '</td>';
            }
            printContents += '<td style="border:1px solid #2563eb;"></td>'; // หมายเหตุ (ว่าง)
            printContents += '</tr>';
        });
        printContents += `
            </tbody>
        </table>
        `;
        printContents += teacherSignatures;

        var win = window.open('', '', 'height=700,width=900');
        win.document.write('<html><head><title>พิมพ์รายชื่อชุมนุม</title>');
        win.document.write(`
            <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
            <style>
                body { font-family: 'Sarabun', Tahoma, sans-serif; }
                table { font-family: 'Sarabun', Tahoma, sans-serif; }
                h3 { font-family: 'Sarabun', Tahoma, sans-serif; }
                p { font-family: 'Sarabun', Tahoma, sans-serif; }
                div { font-family: 'Sarabun', Tahoma, sans-serif; }
            </style>
        `);
        win.document.write('</head><body>');
        win.document.write(printContents);
        win.document.write('</body></html>');
        win.document.close();
        win.print();
    });
});
</script>
</body>
</html>

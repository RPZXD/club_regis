<?php
session_start();
if (empty($_SESSION['logged_in']) || empty($_SESSION['role']) || empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

// โหลด config
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
$pageConfig = $config['global'];


require_once 'function.php';
$menuItems = getMenuByRole($role);

// โหลด autoload ถ้ายังไม่มี
require_once __DIR__ . '/classes/DatabaseDocuments.php';
require_once __DIR__ . '/models/document.php';
require_once __DIR__ . '/controllers/ViewController.php';

use App\Controllers\ViewController;

// ดึงข้อมูลเอกสาร
$controller = new ViewController();
$documents = $controller->index();

// เพิ่ม CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
     <?php require_once 'header.php'; ?>
</head>
<body class="bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-700 to-purple-700 shadow text-white px-4 md:px-6 py-4 flex flex-row md:justify-between md:items-center gap-3 md:gap-0 relative">
        <div class="flex items-center space-x-3">
            <?php if (!empty($pageConfig['logoLink'])): ?>
                <img src="<?php echo htmlspecialchars($pageConfig['logoLink']); ?>" alt="logo" class="h-10 w-10 rounded-full bg-white p-1 shadow" />
            <?php else: ?>
                <span class="text-2xl">📄</span>
            <?php endif; ?>
            <span class="font-bold text-xl tracking-wide"><?php echo htmlspecialchars($pageConfig['nameschool']); ?></span>
        </div>
        <ul class="hidden md:flex flex-wrap justify-center md:justify-start space-x-4 md:space-x-6 text-base">
            <?php foreach ($menuItems as $item): ?>
                <li>
                    <a href="<?php echo htmlspecialchars($item['href']); ?>" class="hover:underline <?php echo isset($item['class']) ? $item['class'] : ''; ?>">
                        <?php echo htmlspecialchars($item['label']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="flex items-center space-x-2 justify-center md:justify-end mt-2 md:mt-0">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-tr from-blue-300 to-purple-300 flex items-center justify-center text-xl md:text-2xl shadow overflow-hidden">
                <img src="<?php echo htmlspecialchars($ImgProfileUser); ?>" alt="profile" class="object-cover w-full h-full">
            </div>
            <div class="flex flex-col">
                <span class="font-semibold text-sm md:text-base"><?php echo htmlspecialchars($user['Teach_name']); ?></span>
                <span class="text-xs text-blue-100"><?php echo htmlspecialchars($role); ?> | <?php echo htmlspecialchars(getRoleEdocName($user['role_edoc'])); ?></span>
            </div>
        </div>
        <!-- Floating Mobile Menu Button (top-right) -->
        <div x-data="{ open: false }" class="md:hidden absolute top-4 right-4">
            <button @click="open = !open"
                class="bg-gradient-to-r from-blue-700 to-purple-700 text-white rounded-full shadow-lg w-14 h-14 flex items-center justify-center focus:outline-none transition hover:scale-110"
                aria-label="เมนู">
                <svg x-show="!open" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <!-- Mobile Menu Drawer -->
            <div x-show="open" @click.away="open = false"
                class="absolute right-0 mt-2 bg-gradient-to-r from-blue-700 to-purple-700 rounded-xl shadow-lg p-4 w-48 flex flex-col space-y-3 transition z-50"
                x-transition>
                <?php foreach ($menuItems as $item): ?>
                    <a href="<?php echo htmlspecialchars($item['href']); ?>" class="block text-white hover:underline <?php echo isset($item['class']) ? $item['class'] : ''; ?>">
                        <?php echo htmlspecialchars($item['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <main class="flex-1 flex flex-col items-center justify-center py-6 md:py-10 px-2">
        <div class="w-full max-w-7xl bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-extrabold mb-6 text-blue-700 flex items-center gap-2">
                📑 รายการเอกสาร
            </h2>
            <!-- Year Filter -->
            <div class="mb-6 flex flex-wrap items-center gap-3 bg-gradient-to-r from-blue-100 to-purple-100 rounded-lg px-4 py-3 shadow">
                <label for="yearFilter" class="font-semibold text-blue-700 flex items-center gap-1">
                    🗓️ ปี:
                </label>
                <select id="yearFilter" class="border border-blue-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none transition text-blue-700 font-medium bg-white shadow">
                    <option value="">ทั้งหมด</option>
                </select>
            </div>
            <div class="overflow-x-auto rounded-lg shadow">
                <table id="documentsTable" class="min-w-full table-auto border border-gray-200 rounded-lg overflow-hidden bg-white">
                    <thead class="bg-gradient-to-r from-blue-200 to-purple-200">
                        <tr>
                            <th class="px-3 py-3 border text-blue-800 font-bold text-center w-12">#️⃣</th>
                            <th class="px-3 py-3 border text-blue-800 font-bold text-center w-32">🆔 เลขที่เอกสาร</th>
                            <th class="px-3 py-3 border text-blue-800 font-bold text-center w-64">📝 หัวเรื่อง</th>
                            <th class="px-3 py-3 border text-blue-800 font-bold text-center w-32">🏢 ฝ่ายงาน</th>
                            <th class="px-3 py-3 border text-blue-800 font-bold text-center w-40">📅 วันที่อัปโหลด</th>
                            <th class="px-3 py-3 border text-blue-800 font-bold text-center w-20">📄 ดาวน์โหลด</th>
                            <?php if ($role === 'เจ้าหน้าที่' || $role === 'admin'): ?>
                            <th class="px-3 py-3 border text-blue-800 font-bold text-center w-32">⚙️ จัดการ</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate here -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <?php require_once 'footer.php'; ?>
<!-- เพิ่ม DataTables CDN และ JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
function thaiDate(dateStr) {
    if (!dateStr) return '';
    const months = [
        '', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
    ];
    const d = new Date(dateStr.replace(' ', 'T'));
    if (isNaN(d)) return dateStr;
    const day = d.getDate();
    const month = d.getMonth() + 1;
    const year = d.getFullYear() + 543;
    const hour = d.getHours().toString().padStart(2, '0');
    const min = d.getMinutes().toString().padStart(2, '0');
    return `${day} ${months[month]} ${year} ${hour}:${min} น.`;
}

$(document).ready(function() {
    var columns = [
        { 
            data: null,
            render: function (data, type, row, meta) {
                return `<span class="font-bold text-blue-700">${meta.row + 1}</span>`;
            },
            className: "text-center"
        },
        { 
            data: 'doc_num',
            render: function(data) {
                return `<span class="text-blue-900 font-semibold">${data}</span>`;
            },
            className: "text-center"
        },
        { 
            data: 'title',
            render: function(data) {
                return `<span class="text-gray-800">${data}</span>`;
            }
        },
        { 
            data: 'group_type',
            render: function(data) {
                const groupMap = {
                    1: 'วิชาการ',
                    2: 'บุคคล',
                    3: 'กิจการนักเรียน',
                    4: 'การเงิน',
                    5: 'บริหารทั่วไป'
                };
                return `<span class="text-indigo-700 font-medium">${groupMap[data] || '-'}</span>`;
            },
            className: "text-center"
        },
        { 
            data: 'date_upload',
            render: function(data) {
                return `<span class="text-purple-700">${thaiDate(data)}</span>`;
            },
            className: "text-center"
        },
        { 
            data: 'file_name',
            render: function(data, type, row) {
                return `<a href="uploads/files/${encodeURIComponent(data)}" class="text-blue-600 underline font-medium hover:text-purple-600 transition" target="_blank">ดาวน์โหลด📁</a>`;
            },
            className: "text-center"
        }
    ];

    <?php if ($role === 'เจ้าหน้าที่' || $role === 'admin'): ?>
    columns.push({
        data: null,
        render: function(data, type, row) {
            return `
                <button class="edit-btn bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded mr-1" data-id="${row.id}" title="แก้ไข">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm0 0V21h8" />
                    </svg>
                </button>
                <button class="delete-btn bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded" data-id="${row.id}" title="ลบ">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
        },
        orderable: false,
        className: "text-center"
    });
    <?php endif; ?>

    var table = $('#documentsTable').DataTable({
        ajax: 'api/documents.php',
        columns: columns,
        initComplete: function(settings, json) {
            // สร้างปีใน dropdown จากข้อมูล pee
            var years = {};
            if (json && json.data) {
                json.data.forEach(function(row) {
                    if (row.pee && row.pee !== "0" && row.pee !== null) years[row.pee] = true;
                });
            }
            var $yearFilter = $('#yearFilter');
            Object.keys(years).sort().reverse().forEach(function(y) {
                $yearFilter.append(`<option value="${y}">${y}</option>`);
            });
        }
    });

    // filter ปี
    $('#yearFilter').on('change', function() {
        table.draw();
    });

    // custom filter function for pee (ปี)
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData) {
        var selectedYear = $('#yearFilter').val();
        if (!selectedYear) return true;
        if (rowData && rowData.pee) {
            return rowData.pee == selectedYear;
        }
        return true;
    });

    <?php if ($role === 'เจ้าหน้าที่' || $role === 'admin'): ?>
    // Modal logic
    function openEditModal(rowData) {
        $('#editDocId').val(rowData.id);
        $('#editTitle').val(rowData.title);
        $('#editDocNum').val(rowData.doc_num);
        $('#editDetail').val(rowData.detail);
        $('#editModal').removeClass('hidden');
    }
    function openDeleteModal(rowData) {
        $('#deleteDocId').val(rowData.id);
        $('#deleteDocTitle').text(rowData.title);
        $('#deleteModal').removeClass('hidden');
    }
    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var rowData = table.row($(this).closest('tr')).data();
        openEditModal(rowData);
    });
    $(document).on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        var rowData = table.row($(this).closest('tr')).data();
        openDeleteModal(rowData);
    });
    $('.modal-close').on('click', function() {
        $(this).closest('.modal').addClass('hidden');
    });
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        $.post($form.attr('action'), $form.serialize())
            .done(function() {
                $('#deleteModal').addClass('hidden');
                table.ajax.reload(null, false);
            })
            .fail(function() {
                alert('เกิดข้อผิดพลาดในการลบ');
            });
    });
    <?php endif; ?>
});
</script>

<?php if ($role === 'เจ้าหน้าที่' || $role === 'admin'): ?>
<!-- Edit Modal -->
<div id="editModal" class="modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button class="modal-close absolute top-2 right-2 text-gray-500 hover:text-red-500 text-2xl">&times;</button>
        <h3 class="text-xl font-bold mb-4 text-yellow-600">แก้ไขเอกสาร</h3>
        <form id="editForm" method="post" action="controllers/EditDocumentController.php">
            <input type="hidden" name="id" id="editDocId">
            <!-- เพิ่ม CSRF token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="mb-3">
                <label class="block mb-1 font-medium">หัวเรื่อง</label>
                <input type="text" name="title" id="editTitle" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1 font-medium">เลขที่เอกสาร</label>
                <input type="text" name="doc_num" id="editDocNum" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1 font-medium">รายละเอียด</label>
                <textarea name="detail" id="editDetail" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="modal-close bg-gray-300 px-4 py-2 rounded">ยกเลิก</button>
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">บันทึก</button>
            </div>
        </form>
    </div>
</div>
<!-- Delete Modal -->
<div id="deleteModal" class="modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 relative">
        <button class="modal-close absolute top-2 right-2 text-gray-500 hover:text-red-500 text-2xl">&times;</button>
        <h3 class="text-xl font-bold mb-4 text-red-600">ยืนยันการลบ</h3>
        <form id="deleteForm" method="post" action="controllers/DeleteDocumentController.php">
            <input type="hidden" name="id" id="deleteDocId">
            <!-- เพิ่ม CSRF token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <p class="mb-4">คุณต้องการลบเอกสาร <span id="deleteDocTitle" class="font-semibold text-red-700"></span> ใช่หรือไม่?</p>
            <div class="flex justify-end gap-2">
                <button type="button" class="modal-close bg-gray-300 px-4 py-2 rounded">ยกเลิก</button>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">ลบ</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
</body>
</html>

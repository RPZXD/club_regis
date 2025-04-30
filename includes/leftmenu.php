<?php
function createNavItem($href, $iconClass, $text) {
    return '
    <li class="nav-item">
        <a href="' . htmlspecialchars($href) . '" class="nav-link">
            <i class="bi ' . htmlspecialchars($iconClass) . '"></i>
            <p>' . htmlspecialchars($text) . '</p>
        </a>
    </li>';
}

if (isset($_SESSION['Teacher_login'])) {
    echo createNavItem('index.php', 'bi-house', 'หน้าหลัก');
    echo createNavItem('club_list.php', 'bi-list-check', 'รายชื่อชุมนุม');
    // เพิ่มเมนูเฉพาะครู
    echo createNavItem('club_members.php', 'bi-person-badge', 'จัดการนักเรียน');
    echo createNavItem('/club_regis/logout.php', 'bi-box-arrow-right', 'ออกจากระบบ');
} elseif (isset($_SESSION['Student_login'])) {
    echo createNavItem('index.php', 'bi-house', 'หน้าหลัก');
    echo createNavItem('club_register.php', 'bi-people-fill', 'สมัครชุมนุม');
    echo createNavItem('my_club.php', 'bi-star', 'ชุมนุมของฉัน');
    echo createNavItem('/club_regis/logout.php', 'bi-box-arrow-right', 'ออกจากระบบ');
} elseif (isset($_SESSION['Admin_login'])) {
    echo createNavItem('index.php', 'bi-house', 'หน้าหลัก');
    echo createNavItem('admin_manage.php', 'bi-gear', 'จัดการระบบ');
    echo createNavItem('/club_regis/logout.php', 'bi-box-arrow-right', 'ออกจากระบบ');
} else {
    // guest/ยังไม่ login
    echo createNavItem('index.php', 'bi-house', 'หน้าหลัก');
    echo createNavItem('club_list.php', 'bi-list-check', 'รายชื่อชุมนุม');
    echo createNavItem('login.php', 'bi-box-arrow-in-right', 'ลงชื่อเข้าสู่ระบบ');
}
?>
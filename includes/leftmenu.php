<?php
function createNavItem($href, $iconClass, $text) {
    return '
    <li class="nav-item">
        <a href="' . htmlspecialchars($href) . '" class="nav-link">
            <i class="fas ' . htmlspecialchars($iconClass) . '"></i>
            <p>' . htmlspecialchars($text) . '</p>
        </a>
    </li>';
}

if (isset($_SESSION['Teacher_login'])) {
    echo createNavItem('index.php', 'fa-home', 'หน้าหลัก');
    echo createNavItem('club_list.php', 'fa-list-check', 'รายชื่อชุมนุม');
    // เพิ่มเมนูเฉพาะครู
    echo createNavItem('club_members.php', 'fa-user-tie', 'จัดการนักเรียน');
    echo createNavItem('logout.php', 'fa-sign-out-alt', 'ออกจากระบบ');
} elseif (isset($_SESSION['Student_login'])) {
    echo createNavItem('index.php', 'fa-home', 'หน้าหลัก');
    echo createNavItem('register.php', 'fa-users', 'สมัครชุมนุม');
    echo createNavItem('my_club.php', 'fa-star', 'ชุมนุมของฉัน');
    echo createNavItem('logout.php', 'fa-sign-out-alt', 'ออกจากระบบ');
} elseif (isset($_SESSION['Admin_login'])) {
    echo createNavItem('index.php', 'fa-home', 'หน้าหลัก');
    echo createNavItem('admin_manage.php', 'fa-cogs', 'จัดการระบบ');
    echo createNavItem('logout.php', 'fa-sign-out-alt', 'ออกจากระบบ');
} else {
    // guest/ยังไม่ login
    echo createNavItem('index.php', 'fa-home', 'หน้าหลัก');
    echo createNavItem('club_list.php', 'fa-list-check', 'รายชื่อชุมนุม');
    echo createNavItem('login.php', 'fa-sign-in-alt', 'ลงชื่อเข้าสู่ระบบ');
}
?>
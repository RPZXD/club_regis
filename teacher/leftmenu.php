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


    echo createNavItem('index.php', 'bi-house', 'หน้าหลัก');
    echo createNavItem('club_list.php', 'bi-list-check', 'รายชื่อชุมนุม');
    // เพิ่มเมนูเฉพาะครู
    echo createNavItem('club_members.php', 'bi-person-badge', 'จัดการนักเรียน');
    echo createNavItem('../logout.php', 'bi-box-arrow-right', 'ออกจากระบบ');

?>
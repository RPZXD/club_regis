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
echo createNavItem('club_regis.php', 'bi-people-fill', 'สมัครชุมนุม');
echo createNavItem('my_club.php', 'bi-star', 'ชุมนุมของฉัน');
echo createNavItem('../logout.php', 'bi-box-arrow-right', 'ออกจากระบบ');

?>
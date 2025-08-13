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

    // guest/ยังไม่ login
    echo createNavItem('index.php', 'bi-house', 'หน้าหลัก');
    echo createNavItem('club_list.php', 'bi-list-check', 'รายชื่อชุมนุม');
    echo createNavItem('best_list.php', 'bi-list-check', 'Best For Teen 2025');
    echo createNavItem('login.php', 'bi-box-arrow-in-right', 'ลงชื่อเข้าสู่ระบบ');

?>
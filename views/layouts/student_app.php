<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$config = json_decode(file_get_contents(__DIR__ . '/../../config.json'), true);
$global = $config['global'];
?>
<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle ?? 'นักเรียน'; ?> | <?php echo $global['nameschool']; ?></title>
    
    <!-- Google Font: Mali -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS (Compiled) -->
    <link rel="stylesheet" href="../dist/css/tailwind.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <style>
        :root {
            --color-primary-50: #ecfdf5;
            --color-primary-100: #d1fae5;
            --color-primary-200: #a7f3d0;
            --color-primary-300: #6ee7b7;
            --color-primary-400: #34d399;
            --color-primary-500: #10b981;
            --color-primary-600: #059669;
            --color-primary-700: #047857;
            --color-primary-800: #065f46;
            --color-primary-900: #064e3b;

            --color-accent-50: #f0fdfa;
            --color-accent-100: #ccfbf1;
            --color-accent-200: #99f6e4;
            --color-accent-300: #5eead4;
            --color-accent-400: #2dd4bf;
            --color-accent-500: #14b8a6;
            --color-accent-600: #0d9488;
            --color-accent-700: #0f766e;
            --color-accent-800: #115e59;
            --color-accent-900: #134e4a;
        }
        * { font-family: 'Mali', sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.1); }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #10b981 0%, #059669 100%); border-radius: 4px; }
        .dark .glass { background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(255, 255, 255, 0.1); }
        select, input, textarea { color-scheme: light dark; }
        .dark select { background-color: #1e293b; color: #f8fafc; }
        .dark select option { background-color: #0f172a !important; color: #f8fafc !important; }
        .sidebar-item { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-item:hover { transform: translateX(8px); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .loader { border: 3px solid rgba(16, 185, 129, 0.2); border-top: 3px solid #10b981; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
    
    <link rel="icon" type="image/png" href="../dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>">
</head>

<body class="font-mali bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 min-h-screen transition-colors duration-500">
    
    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 z-50 flex items-center justify-center bg-white dark:bg-slate-900 transition-opacity duration-500">
        <div class="text-center">
            <img src="../dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>" alt="Logo" class="w-24 h-24 mx-auto animate-bounce-slow">
            <div class="loader mx-auto mt-6"></div>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 animate-pulse">ระบบนักเรียน</p>
        </div>
    </div>
    
    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../components/student_sidebar.php'; ?>
        
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Navbar -->
            <nav class="sticky top-0 z-30 flex items-center justify-between px-4 md:px-8 py-4 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2.5 rounded-2xl bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 transition-all">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="hidden sm:block">
                        <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <span class="w-2 h-8 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full"></span>
                            <?php echo $pageTitle ?? 'ระบบนักเรียน'; ?>
                        </h1>
                    </div>
                </div>
                <div class="flex items-center gap-2 md:gap-4">
                    <button onclick="toggleDarkMode()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-amber-400 hover:scale-110 active:scale-95 transition-all shadow-sm border border-slate-200 dark:border-slate-700">
                        <i class="fas fa-sun dark:hidden"></i>
                        <i class="fas fa-moon hidden dark:block"></i>
                    </button>
                </div>
            </nav>
            
            <main class="flex-1 p-4 md:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto animate-fade-in">
                    <?php echo $content ?? ''; ?>
                </div>
            </main>
            
            <footer class="py-4 px-6 text-center text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
                <p>&copy; <?php echo date('Y') + 543; ?> <?php echo $global['nameschool']; ?> - All rights reserved.</p>
                <p class="mt-1"><?php echo $global['footerCredit'] ?? ''; ?></p>
            </footer>
        </div>
    </div>
    
    <script>
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            setTimeout(() => { preloader.style.opacity = '0'; setTimeout(() => { preloader.style.display = 'none'; }, 500); }, 800);
        });
        function toggleDarkMode() { document.documentElement.classList.toggle('dark'); localStorage.setItem('darkMode', document.documentElement.classList.contains('dark')); }
        if (localStorage.getItem('darkMode') === 'true') { document.documentElement.classList.add('dark'); }
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>

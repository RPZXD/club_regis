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
    <title><?php echo $pageTitle ?? 'ครู'; ?> | <?php echo $global['nameschool']; ?></title>
    
    <!-- Google Font: Mali -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'mali': ['Mali', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc',
                            400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca',
                            800: '#3730a3', 900: '#312e81',
                        },
                        accent: {
                            50: '#faf5ff', 100: '#f3e8ff', 200: '#e9d5ff', 300: '#d8b4fe',
                            400: '#c084fc', 500: '#a855f7', 600: '#9333ea', 700: '#7e22ce',
                            800: '#6b21a8', 900: '#581c87',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'bounce-slow': 'bounce 2s infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                    },
                }
            }
        }
    </script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <style>
        * { font-family: 'Mali', sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.1); }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #6366f1 0%, #4f46e5 100%); border-radius: 4px; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .dark .glass { background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-item { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-item:hover { transform: translateX(8px); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .loader { border: 3px solid rgba(99, 102, 241, 0.2); border-top: 3px solid #6366f1; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
    
    <link rel="icon" type="image/png" href="../dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>">
</head>

<body class="font-mali bg-gradient-to-br from-indigo-50 via-violet-50 to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 min-h-screen transition-colors duration-500">
    
    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 z-50 flex items-center justify-center bg-white dark:bg-slate-900 transition-opacity duration-500">
        <div class="text-center">
            <img src="../dist/img/<?php echo $global['logoLink'] ?? 'logo-phicha.png'; ?>" alt="Logo" class="w-24 h-24 mx-auto animate-bounce-slow">
            <div class="loader mx-auto mt-6"></div>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 animate-pulse">ระบบครู</p>
        </div>
    </div>
    
    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../components/teacher_sidebar.php'; ?>
        
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Navbar -->
            <nav class="sticky top-0 z-30 flex items-center justify-between px-4 md:px-8 py-4 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2.5 rounded-2xl bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 transition-all">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="hidden sm:block">
                        <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <span class="w-2 h-8 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></span>
                            <?php echo $pageTitle ?? 'ระบบครู'; ?>
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

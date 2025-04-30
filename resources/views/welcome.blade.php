{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ระบบสมัครชุมนุม</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Mali', cursive;
        }

        .emoji-bounce {
            animation: bounce 1.2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        .glow {
            box-shadow: 0 0 16px 2px #3b82f6aa;
        }

        /* Space background animation */
        .space-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .star {
            position: absolute;
            border-radius: 50%;
            background: white;
            opacity: 0.7;
            animation: twinkle 3s infinite alternate;
        }

        @keyframes twinkle {
            from {
                opacity: 0.5;
            }

            to {
                opacity: 1;
            }
        }
    </style>
    <script>
        // Generate random stars for space effect
        document.addEventListener('DOMContentLoaded', function() {
            const starCount = 60;
            const spaceBg = document.createElement('div');
            spaceBg.className = 'space-bg';
            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                const size = Math.random() * 2 + 1;
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;
                star.style.top = `${Math.random() * 100}%`;
                star.style.left = `${Math.random() * 100}%`;
                star.style.animationDuration = `${2 + Math.random() * 3}s`;
                spaceBg.appendChild(star);
            }
            document.body.prepend(spaceBg);
        });
    </script>
</head>

<body
    class="bg-gradient-to-br from-blue-900 via-indigo-900 to-black min-h-screen flex flex-col items-center justify-center relative overflow-hidden"
    style="font-family: 'Mali', cursive;">
    {{-- องค์ประกอบพื้นหลังอวกาศจะถูกแทรกโดย JS --}}

    <!-- ปุ่มเข้าสู่ระบบมุมขวาบน -->
    <a href="{{ url('/login') }}"
        class="fixed top-6 right-8 z-20 inline-flex items-center bg-white/80 border border-blue-400 text-blue-700 px-6 py-3 rounded-xl text-lg font-semibold shadow hover:bg-blue-50 hover:text-blue-900 transition-all duration-300 gap-2 justify-center">
        <span>🔑</span>
        เข้าสู่ระบบ
    </a>

    <div
        class="text-center bg-white/80 rounded-3xl shadow-xl px-10 py-12 mb-10 border border-blue-100 backdrop-blur-md relative z-10">
        <div class="flex justify-center mb-4">
            <span class="text-6xl emoji-bounce">🎉</span>
        </div>
        <h1 class="text-5xl font-extrabold text-blue-400 mb-3 drop-shadow-lg flex items-center justify-center gap-2">
            ระบบสมัครชุมนุม
            <span class="text-3xl">🤝</span>
        </h1>
        <p class="text-yellow-400 mb-8 text-lg flex items-center justify-center gap-2">
            โรงเรียนของเราเปิดโอกาสให้นักเรียนได้เลือกชุมนุมตามความสนใจ
            <span class="text-xl">✨</span>
        </p>

        <a href="{{ url('/login') }}"
            class="bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-700 glow text-white px-8 py-4 rounded-xl text-xl font-semibold shadow-lg hover:scale-105 hover:from-pink-500 hover:to-yellow-400 hover:glow transition-all duration-300 flex items-center gap-2 justify-center">
            <span class="emoji-bounce">🚀</span>
            สมัครเข้าชุมนุม
        </a>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <span
                class="inline-flex items-center bg-blue-100 text-blue-700 px-4 py-2 rounded-full font-medium shadow hover:bg-blue-200 transition">
                <span class="mr-2">🎨</span> ศิลปะ
            </span>
            <span
                class="inline-flex items-center bg-green-100 text-green-700 px-4 py-2 rounded-full font-medium shadow hover:bg-green-200 transition">
                <span class="mr-2">⚽</span> กีฬา
            </span>
            <span
                class="inline-flex items-center bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full font-medium shadow hover:bg-yellow-200 transition">
                <span class="mr-2">💻</span> คอมพิวเตอร์
            </span>
            <span
                class="inline-flex items-center bg-pink-100 text-pink-700 px-4 py-2 rounded-full font-medium shadow hover:bg-pink-200 transition">
                <span class="mr-2">🎵</span> ดนตรี
            </span>
            <span
                class="inline-flex items-center bg-purple-100 text-purple-700 px-4 py-2 rounded-full font-medium shadow hover:bg-purple-200 transition">
                <span class="mr-2">📚</span> วิชาการ
            </span>
        </div>
    </div>

    <footer class="absolute bottom-4 text-gray-500 text-sm flex items-center gap-2 z-10"
        style="font-family: 'Mali', cursive;">
        <strong>
            &copy; <?= date('Y') ?>,
            <a href="https://www.phichai.ac.th" class="text-blue-500 font-bold">by ICT @Phichai school</a>.
            พัฒนาระบบโดย นายทิวา เรืองศรี ทีมงาน ICT โรงเรียนพิชัย
        </strong>
        <div class="float-right d-none d-sm-inline-block">
            <a href="https://www.phichai.ac.th" target="_blank" class="text-gray-700 dark:text-gray-300">
                <i class="fas fa-globe fa-lg"></i>
            </a>
            <a href="https://www.facebook.com/PhichaischoolSec39" target="_blank"
                class="text-blue-600 dark:text-blue-400">
                <i class="fab fa-facebook fa-lg"></i>
            </a>
        </div>
    </footer>
</body>

</html>

{{-- resources/views/login.blade.php --}}
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ | ระบบสมัครชุมนุม</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Mali', cursive;
        }

        .glow {
            box-shadow: 0 0 16px 2px #3b82f6aa;
        }

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

        /* Shooting star */
        .shooting-star {
            position: absolute;
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg, #fff, rgba(255, 255, 255, 0));
            opacity: 0.8;
            z-index: 1;
            animation: shooting 1.2s linear forwards;
        }

        @keyframes shooting {
            0% {
                opacity: 0;
                transform: translateX(0) translateY(0) scaleX(0.5);
            }

            10% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: translateX(400px) translateY(100px) scaleX(1);
            }
        }

        /* Key icon animation */
        .key-animate {
            animation: floatKey 2.5s ease-in-out infinite alternate, spinKey 8s linear infinite;
        }

        @keyframes floatKey {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-10px);
            }
        }

        @keyframes spinKey {
            0% {
                rotate: 0deg;
            }

            100% {
                rotate: 360deg;
            }
        }

        /* Shake animation for error */
        .shake {
            animation: shake 0.4s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-10px);
            }

            40%,
            80% {
                transform: translateX(10px);
            }
        }
    </style>
    <script>
        // ดาวพื้นหลัง
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

            // ดาวตก (shooting star)
            setInterval(() => {
                if (Math.random() < 0.5) { // 50% chance
                    const shooting = document.createElement('div');
                    shooting.className = 'shooting-star';
                    shooting.style.top = `${Math.random() * 60 + 10}%`;
                    shooting.style.left = `${Math.random() * 40}%`;
                    document.body.appendChild(shooting);
                    setTimeout(() => shooting.remove(), 1300);
                }
            }, 1800);
        });

        // Toggle password visibility
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('togglePwdIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.textContent = '🙈';
            } else {
                pwd.type = 'password';
                icon.textContent = '👁️';
            }
        }
    </script>
</head>

<body
    class="bg-gradient-to-br from-blue-900 via-indigo-900 to-black min-h-screen flex flex-col items-center justify-center relative overflow-hidden"
    style="font-family: 'Mali', cursive;">
    {{-- องค์ประกอบพื้นหลังอวกาศจะถูกแทรกโดย JS --}}

    <a href="{{ url('/') }}"
        class="fixed top-6 left-8 z-20 inline-flex items-center bg-white/80 border border-blue-400 text-blue-700 px-6 py-3 rounded-xl text-lg font-semibold shadow hover:bg-blue-50 hover:text-blue-900 transition-all duration-300 gap-2 justify-center">
        <span>🏠</span>
        กลับหน้าหลัก
    </a>

    <div
        class="bg-white/90 rounded-3xl shadow-xl px-8 py-10 w-full max-w-md border border-blue-100 backdrop-blur-md relative z-10">
        <div class="flex flex-col items-center mb-6">
            <span class="text-5xl key-animate">🔑</span>
            <span class="mt-2 text-blue-400 text-lg animate-pulse">ยินดีต้อนรับสู่ระบบสมัครชุมนุม 🚀</span>
        </div>
        <h2 class="text-3xl font-bold text-blue-500 mb-6 text-center">เข้าสู่ระบบ</h2>
        {{-- ตัวอย่าง error message --}}
        @if ($errors->has('login'))
            <div class="mb-4 text-red-500 text-center font-bold shake">{{ $errors->first('login') }}</div>
        @endif
        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="mb-4">
                <label for="username" class="block text-blue-700 mb-2">ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" required autofocus
                    class="w-full text-center px-4 py-3 rounded-lg border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                    placeholder="กรอกชื่อผู้ใช้" value="{{ old('username') }}">
            </div>
            <div class="mb-6 relative">
                <label for="password" class="block text-blue-700 mb-2">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required
                    class="w-full text-center px-4 py-3 rounded-lg border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 transition pr-12"
                    placeholder="กรอกรหัสผ่าน">
                <button type="button" onclick="togglePassword()" tabindex="-1"
                    class="absolute top-9 right-3 text-xl focus:outline-none select-none" style="background: none;"
                    id="togglePwdBtn">
                    <span id="togglePwdIcon">👁️</span>
                </button>
            </div>
            <div class="mb-4">
                <label for="role" class="block text-blue-700 mb-2">บทบาท</label>
                <select id="role" name="role" required
                    class="w-full text-center px-4 py-3 rounded-lg border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 transition bg-white">
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>เลือกบทบาท</option>
                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>นักเรียน</option>
                    <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>ครู</option>
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>เจ้าหน้าที่</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-700 glow text-white py-3 rounded-lg text-lg font-semibold shadow-lg hover:scale-105 hover:from-pink-500 hover:to-yellow-400 hover:glow transition-all duration-300 flex items-center justify-center gap-2">
                <span>🚀</span>
                เข้าสู่ระบบ
            </button>
        </form>
        <div class="mt-8 text-center text-blue-400 text-sm animate-pulse">
            <span>🌌 เข้าสู่ระบบเพื่อเริ่มต้นการผจญภัยในโลกของชุมนุม!</span>
        </div>
    </div>
</body>

</html>

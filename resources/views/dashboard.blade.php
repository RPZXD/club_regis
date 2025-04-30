{{-- filepath: c:\xampp\htdocs\club_regis\resources\views\dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | ระบบสมัครชุมนุม</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Mali', cursive;
        }
    </style>
</head>

<body
    class="bg-gradient-to-br from-blue-900 via-indigo-900 to-black min-h-screen flex flex-col items-center justify-center">
    <div
        class="bg-white/90 rounded-3xl shadow-xl px-8 py-10 w-full max-w-md border border-blue-100 backdrop-blur-md relative z-10 text-center">
        <h1 class="text-4xl font-bold text-blue-500 mb-4">🎉 ยินดีต้อนรับสู่ Dashboard</h1>
        <p class="text-lg text-blue-700 mb-6">คุณเข้าสู่ระบบสำเร็จแล้ว</p>
        <a href="{{ url('/') }}"
            class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-blue-700 transition">กลับหน้าหลัก</a>
    </div>
</body>

</html>

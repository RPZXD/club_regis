<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                <i class="fas fa-star text-xl"></i>
            </div>
            <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-xl md:text-2xl font-black text-gray-800 dark:text-white">Best For Teen</h1>
                <span id="year-badge" class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/50">
                ปีการศึกษา <?= $current_year ?>
                </span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">จัดการและตรวจสอบข้อมูลกิจกรรม Best For Teen</p>
            </div>
        </div>

        <!-- Academic Year Selector & Add Button -->
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 glass rounded-2xl px-3.5 py-2.5 border border-white/50 dark:border-white/10 shadow-sm">
                <i class="fas fa-calendar-alt text-amber-500 text-sm"></i>
                <label for="year-select" class="text-xs font-black text-gray-700 dark:text-gray-200 whitespace-nowrap">ปีการศึกษา:</label>
                <select id="year-select" class="bg-transparent font-black text-sm text-gray-800 dark:text-white focus:outline-none cursor-pointer">
                    <?php foreach ($available_years as $y): ?>
                    <option value="<?= $y ?>" <?= $y == $current_year ? 'selected' : '' ?> class="bg-white text-gray-800 dark:bg-slate-900 dark:text-white">
                        <?= $y ?> <?= $y == $current_year ? '(ปัจจุบัน)' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button id="btn-new" class="hidden md:flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-5 py-3 rounded-xl shadow-lg transition-all active:scale-95">
                <i class="fas fa-plus"></i>
                <span>เพิ่มกิจกรรม</span>
            </button>
        </div>
    </div>

    <!-- Mobile Add Button -->
    <button id="btn-new-mobile" class="md:hidden w-full mt-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
        <i class="fas fa-plus"></i>
        <span>เพิ่มกิจกรรม</span>
    </button>
</div>

<!-- History Notice (Shown when not current year) -->
<div id="history-notice" class="hidden mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 flex items-center justify-between gap-3 text-amber-800 dark:text-amber-300 animate__animated animate__fadeIn">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
            <i class="fas fa-history text-lg"></i>
        </div>
        <div>
            <div class="font-bold text-sm">กำลังดูข้อมูลย้อนหลัง: ปีการศึกษา <span id="history-year-text" class="font-black"></span></div>
            <div class="text-xs text-amber-700/80 dark:text-amber-400/80">สามารถดูรายชื่อสมาชิก และพิมพ์รายงานกิจกรรมย้อนหลังได้</div>
        </div>
    </div>
    <button onclick="resetToCurrentYear()" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-sm">
        กลับสู่ปีปัจจุบัน
    </button>
</div>

<!-- Stats Cards & Action Bar -->
<div class="mb-6">
    <div class="flex items-center justify-between gap-3 mb-3">
        <h2 class="text-sm font-black text-gray-700 dark:text-gray-200 flex items-center gap-2">
            <i class="fas fa-chart-pie text-amber-500"></i> ภาพรวมการรับสมัคร
        </h2>
        <button type="button" id="btn-toggle-chart" onclick="toggleChartSection()" class="flex items-center gap-2 px-3.5 py-2 rounded-2xl glass border border-white/50 dark:border-white/10 text-xs font-black text-gray-700 dark:text-gray-200 hover:text-amber-600 dark:hover:text-amber-400 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chart-bar text-amber-500"></i>
            <span id="chart-toggle-text">ดูกราฟสถิติ</span>
            <i id="chart-toggle-icon" class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform"></i>
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-lg shrink-0">
                <i class="fas fa-shapes"></i>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">จำนวนกิจกรรม</div>
                <div id="card-activities" class="text-2xl font-black text-gray-800 dark:text-white leading-tight">0</div>
            </div>
        </div>

        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-lg shrink-0">
                <i class="fas fa-user-friends"></i>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ที่รับทั้งหมด</div>
                <div id="card-capacity" class="text-2xl font-black text-gray-800 dark:text-white leading-tight">0</div>
            </div>
        </div>

        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-violet-500/10 dark:bg-violet-500/20 text-violet-600 dark:text-violet-400 flex items-center justify-center font-black text-lg shrink-0">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">สมัครแล้ว</div>
                <div id="card-registered" class="text-2xl font-black text-gray-800 dark:text-white leading-tight">0</div>
            </div>
        </div>

        <div class="glass rounded-2xl p-4 border border-white/50 dark:border-white/10 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black text-lg shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">อัตราการเต็ม</div>
                <div id="card-fill" class="text-2xl font-black text-gray-800 dark:text-white leading-tight">0%</div>
            </div>
        </div>
    </div>
</div>

<!-- Collapsible Chart Section (Hidden by default to avoid cluttering) -->
<div id="chart-section" class="hidden mb-6 glass rounded-3xl p-6 border border-white/50 dark:border-white/10 shadow-xl animate__animated animate__fadeIn">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200/50 dark:border-gray-700/50">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-500 flex items-center justify-center">
                <i class="fas fa-chart-bar text-sm"></i>
            </div>
            <div>
                <h3 class="font-black text-gray-800 dark:text-white text-sm">สถิติการรับสมัครรายกิจกรรม (Top 8)</h3>
                <p id="chart-year-label" class="text-[11px] text-gray-500 dark:text-gray-400 font-bold">ปีการศึกษา <?= $current_year ?></p>
            </div>
        </div>
        <button type="button" onclick="toggleChartSection()" class="w-8 h-8 rounded-xl bg-gray-200/60 dark:bg-slate-700/60 text-gray-500 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-slate-600 flex items-center justify-center transition-all" title="ซ่อนกราฟ">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
    <div class="relative h-64 w-full">
        <canvas id="best-chart"></canvas>
    </div>
</div>

<!-- Search & Filter Controls -->
<div class="glass rounded-3xl p-4 md:p-5 mb-6 border border-white/50 dark:border-white/10 shadow-sm space-y-3">
    <div class="flex flex-col md:flex-row md:items-center gap-3">
        <!-- Search Input -->
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="activity-search" placeholder="ค้นหากิจกรรม (ชื่อ, ระดับชั้น, รายละเอียด)..." 
                   class="w-full pl-11 pr-10 py-3.5 rounded-2xl bg-white dark:bg-slate-900 border-2 border-gray-100 dark:border-slate-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition-all font-bold text-sm placeholder:text-gray-400 placeholder:font-normal shadow-sm">
            <button type="button" id="btn-clear-activity-search" onclick="clearActivitySearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>

        <!-- Grade Filter Chips -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 shrink-0">
            <button type="button" class="grade-filter-btn px-3.5 py-2.5 rounded-xl text-xs font-black bg-amber-500 text-white shadow-sm transition-all" data-grade="all">ทั้งหมด</button>
            <?php foreach(['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'] as $gf): ?>
            <button type="button" class="grade-filter-btn px-3.5 py-2.5 rounded-xl text-xs font-black bg-white/70 dark:bg-slate-800/70 border border-gray-200/60 dark:border-slate-700/60 text-gray-600 dark:text-gray-300 hover:bg-amber-100 hover:text-amber-700 dark:hover:bg-slate-700 transition-all" data-grade="<?= $gf ?>"><?= $gf ?></button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Mobile Cards View -->
<div id="activity-cards" class="space-y-4 md:hidden">
    <div class="text-center py-16">
        <div class="w-12 h-12 border-4 border-amber-200 border-t-amber-600 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-gray-500 font-bold">กำลังโหลดกิจกรรม...</p>
    </div>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block glass rounded-3xl overflow-hidden mb-8 border border-white/40 dark:border-white/10 shadow-xl">
    <table id="best-table" class="w-full">
        <thead>
            <tr class="bg-gradient-to-r from-amber-500 to-orange-600 text-white">
                <th class="py-4 px-4 text-center font-black w-16">#</th>
                <th class="py-4 px-4 text-left font-black">ชื่อกิจกรรม</th>
                <th class="py-4 px-4 text-center font-black">ระดับชั้น</th>
                <th class="py-4 px-4 text-center font-black w-48">สมาชิก / ความจุ</th>
                <th class="py-4 px-4 text-center font-black w-44">จัดการ</th>
            </tr>
        </thead>
        <tbody id="best-body" class="divide-y divide-gray-100 dark:divide-gray-800/60">
            <tr>
                <td colspan="5" class="py-16 text-center text-gray-400 font-bold">
                    <div class="w-10 h-10 border-4 border-amber-200 border-t-amber-600 rounded-full animate-spin mx-auto mb-3"></div>
                    กำลังโหลดข้อมูล...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Modal Create/Edit -->
<div id="modal" class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm hidden p-0 md:p-4">
    <div class="bg-white dark:bg-slate-900 w-full md:max-w-lg md:rounded-3xl rounded-t-3xl shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-5 flex justify-between items-center text-white z-10">
            <div>
                <h3 id="modal-title" class="text-xl font-black">เพิ่มกิจกรรม</h3>
                <p id="modal-subtitle" class="text-xs opacity-90">ปีการศึกษา <?= $current_year ?></p>
            </div>
            <button id="btn-cancel" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-all">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form id="best-form" class="p-6 space-y-5">
            <input type="hidden" id="activity_id">
            <input type="hidden" id="activity_year" value="<?= $current_year ?>">
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-2 text-sm">ชื่อกิจกรรม</label>
                <input type="text" id="name" required placeholder="เช่น Robot & Coding Club" class="w-full bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3.5 focus:border-amber-500 focus:outline-none transition-all font-bold dark:text-white">
            </div>
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-2 text-sm">รายละเอียด</label>
                <textarea id="description" rows="3" placeholder="รายละเอียดหรือเป้าหมายของกิจกรรม..." class="w-full bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3.5 focus:border-amber-500 focus:outline-none transition-all dark:text-white"></textarea>
            </div>
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-3 text-sm">ระดับชั้นที่เปิดรับ</label>
                <div class="grid grid-cols-3 gap-2">
                    <?php foreach(['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'] as $g): ?>
                    <label class="relative cursor-pointer">
                        <input type="checkbox" class="grade-opt peer sr-only" value="<?= $g ?>">
                        <div class="w-full py-3 text-center rounded-xl border-2 border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 font-black text-gray-500 dark:text-gray-400 transition-all peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white active:scale-95">
                            <?= $g ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <label class="block font-black text-gray-700 dark:text-gray-200 mb-2 text-sm">จำนวนที่รับ (คน)</label>
                <input type="number" id="max_members" min="1" required class="w-full bg-gray-50 dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3.5 focus:border-amber-500 focus:outline-none transition-all font-bold dark:text-white text-center text-xl">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" id="btn-cancel-2" class="flex-1 py-4 rounded-xl font-black text-gray-500 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 transition-all active:scale-[0.98]">
                    ยกเลิก
                </button>
                <button type="submit" class="flex-1 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-[0.98]">
                    บันทึก
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Members Modal -->
<div id="members-modal" class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black/60 backdrop-blur-sm hidden p-0 md:p-4">
    <div class="bg-white dark:bg-slate-900 w-full md:max-w-2xl md:rounded-3xl rounded-t-3xl shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-5 flex justify-between items-center text-white z-20">
            <div>
                <h3 class="text-xl font-black" id="modal-members-title">จัดการสมาชิก</h3>
                <p class="text-xs opacity-90" id="modal-members-subtitle">รายชื่อสมาชิกที่ลงทะเบียน</p>
            </div>
            <div class="flex items-center gap-2">
                <a id="modal-members-print-btn" href="#" target="_blank" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-all" title="พิมพ์รายชื่อ">
                    <i class="fas fa-print text-sm"></i>
                </a>
                <button id="btn-close-members" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-all">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Add Member with Autocomplete -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-slate-800/40 relative">
            <label class="block text-xs font-black text-gray-600 dark:text-gray-300 mb-2">
                <i class="fas fa-user-plus text-amber-500 mr-1"></i> เพิ่มนักเรียนเข้ากิจกรรม (ค้นหาด้วยรหัส, ชื่อ หรือนามสกุล)
            </label>
            <div class="relative">
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" id="member-student-search" autocomplete="off"
                               placeholder="พิมพ์รหัสนักเรียน, ชื่อ หรือนามสกุล..." 
                               class="w-full bg-white dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl pl-11 pr-10 py-3 font-bold text-sm text-gray-800 dark:text-white focus:border-blue-500 focus:outline-none transition-all placeholder:text-gray-400 placeholder:font-normal shadow-sm">
                        <div id="autocomplete-loading" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                            <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                        <button type="button" id="btn-clear-search" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                    <input type="hidden" id="member-student-id">
                    <button id="btn-add-member" class="px-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-xl transition-all active:scale-95 flex items-center gap-1.5 whitespace-nowrap text-sm shadow-md shadow-emerald-500/20">
                        <i class="fas fa-plus"></i>
                        <span>เพิ่ม</span>
                    </button>
                </div>
                
                <!-- Autocomplete Dropdown List -->
                <div id="student-autocomplete-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-700 max-h-72 overflow-y-auto z-50 divide-y divide-gray-100 dark:divide-slate-700/50">
                    <!-- Results injected here -->
                </div>
            </div>
            
            <!-- Selected Student Preview -->
            <div id="selected-student-tag" class="hidden mt-2 p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 flex items-center justify-between text-xs animate__animated animate__fadeIn">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="w-5 h-5 rounded-md bg-blue-500 text-white font-black flex items-center justify-center text-[10px]">✓</span>
                    <span class="font-black text-gray-800 dark:text-white" id="sel-tag-name"></span>
                    <span class="text-gray-500 dark:text-gray-400 font-mono font-bold" id="sel-tag-id"></span>
                    <span class="px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 font-bold" id="sel-tag-class"></span>
                </div>
                <button type="button" onclick="clearSelectedStudent()" class="text-rose-500 hover:text-rose-700 dark:text-rose-400 text-xs font-bold ml-2">
                    <i class="fas fa-times mr-1"></i>ยกเลิก
                </button>
            </div>
        </div>

        <!-- Members list -->
        <div id="members-list" class="p-4 space-y-2"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const CURRENT_SYSTEM_YEAR = <?= $current_year ?>;
let selectedYear = CURRENT_SYSTEM_YEAR;
let activitiesData = [];
let currentActivityId = null;
let currentActivityMembers = [];
let bestChart = null;
let searchDebounceTimer = null;
let activeAutocompleteIndex = -1;
let currentAutocompleteStudents = [];

function showToast(msg, type = 'info') {
    Swal.fire({ toast: true, position: 'top-end', icon: type, title: msg, showConfirmButton: false, timer: 2000 });
}

function resetToCurrentYear() {
    selectedYear = CURRENT_SYSTEM_YEAR;
    document.getElementById('year-select').value = selectedYear;
    loadData();
}

function renderCard(a) {
    const current = parseInt(a.current_members_count || 0);
    const max = parseInt(a.max_members || 0);
    const percent = max > 0 ? Math.round((current / max) * 100) : 0;
    const isFull = percent >= 100;
    
    const grades = (a.grade_levels || '').split(',').map(g => 
        `<span class="px-2 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-xs font-black">${g.trim()}</span>`
    ).join(' ');

    return `
        <div class="activity-card glass rounded-3xl shadow-lg overflow-hidden border border-white/40 dark:border-white/10" data-name="${(a.name || '').toLowerCase()}" data-grades="${(a.grade_levels || '').toLowerCase()}" data-id="${a.id}">
            <div class="p-4 pb-3">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br ${isFull ? 'from-rose-500 to-red-600' : 'from-amber-500 to-orange-600'} flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas ${isFull ? 'fa-lock' : 'fa-star'} text-lg"></i>
                    </div>
                    <div class="flex-1 w-0">
                        <h3 class="font-black text-gray-800 dark:text-white text-base whitespace-nowrap overflow-hidden text-ellipsis">${a.name}</h3>
                        <div class="flex gap-1 mt-2 flex-wrap">${grades}</div>
                    </div>
                    ${isFull ? '<span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black">เต็ม</span>' : ''}
                </div>
            </div>
            <div class="mx-4 mb-4 p-3 rounded-2xl bg-black/5 dark:bg-white/5">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-black text-gray-500 dark:text-gray-400 uppercase">สมาชิก</span>
                    <span class="text-sm font-black ${isFull ? 'text-rose-500' : 'text-amber-600 dark:text-amber-400'}">${current} / ${max}</span>
                </div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full ${isFull ? 'bg-gradient-to-r from-rose-500 to-red-500' : 'bg-gradient-to-r from-amber-500 to-orange-500'}" style="width: ${percent}%"></div>
                </div>
            </div>
            <div class="px-4 py-3 bg-black/[0.02] dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex flex-wrap gap-2">
                <button class="edit-btn flex-1 px-3 py-2 rounded-xl bg-indigo-500 hover:bg-indigo-600 text-white font-bold text-sm active:scale-95 transition-all" data-id="${a.id}">
                    <i class="fas fa-edit mr-1"></i> แก้ไข
                </button>
                <button class="members-btn flex-1 px-3 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-bold text-sm active:scale-95 transition-all" data-id="${a.id}">
                    <i class="fas fa-users mr-1"></i> สมาชิก
                </button>
                <a href="print_best.php?id=${a.id}" target="_blank" class="flex-1 px-3 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm text-center active:scale-95 transition-all">
                    <i class="fas fa-print mr-1"></i> พิมพ์
                </a>
                <button class="delete-btn px-3 py-2 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold text-sm active:scale-95 transition-all" data-id="${a.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>`;
}

function renderMobileCards(data) {
    const container = document.getElementById('activity-cards');
    if (!data || data.length === 0) {
        container.innerHTML = '<div class="text-center py-16 glass rounded-3xl border border-white/40 dark:border-white/10"><i class="fas fa-folder-open text-gray-300 dark:text-gray-600 text-3xl mb-4"></i><p class="text-gray-500 dark:text-gray-400 font-bold">ไม่พบกิจกรรมในปีการศึกษานี้</p></div>';
        return;
    }
    container.innerHTML = data.map(a => renderCard(a)).join('');
}

function renderTable(data) {
    const tbody = document.getElementById('best-body');
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="py-16 text-center text-gray-400 dark:text-gray-500 font-bold"><i class="fas fa-folder-open text-2xl mb-2 block text-gray-300 dark:text-gray-600"></i>ไม่พบกิจกรรมในปีการศึกษานี้</td></tr>';
        return;
    }

    tbody.innerHTML = data.map((a, idx) => {
        const current = parseInt(a.current_members_count || 0);
        const max = parseInt(a.max_members || 0);
        const percent = max > 0 ? Math.round((current / max) * 100) : 0;
        const isFull = percent >= 100;
        const grades = (a.grade_levels || '').split(',').map(g => 
            `<span class="px-2 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-xs font-black">${g.trim()}</span>`
        ).join(' ');

        return `
            <tr class="activity-row hover:bg-amber-50/40 dark:hover:bg-slate-700/40 transition-colors" data-name="${(a.name || '').toLowerCase()}" data-grades="${(a.grade_levels || '').toLowerCase()}" data-id="${a.id}">
                <td class="py-4 px-4 text-center font-bold text-gray-400 dark:text-gray-500">${idx + 1}</td>
                <td class="py-4 px-4">
                    <div class="font-black text-gray-800 dark:text-white text-base">${a.name}</div>
                    ${a.description ? `<div class="text-xs text-gray-400 dark:text-gray-500 line-clamp-1 mt-0.5">${a.description}</div>` : ''}
                </td>
                <td class="py-4 px-4 text-center">
                    <div class="flex gap-1 justify-center flex-wrap">${grades}</div>
                </td>
                <td class="py-4 px-4">
                    <div class="w-full max-w-[160px] mx-auto">
                        <div class="flex justify-between items-center text-xs font-black mb-1">
                            <span class="${isFull ? 'text-rose-500' : 'text-amber-600 dark:text-amber-400'}">${current} / ${max}</span>
                            <span class="text-gray-400 dark:text-gray-500 font-bold">${percent}%</span>
                        </div>
                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full ${isFull ? 'bg-rose-500' : 'bg-gradient-to-r from-amber-500 to-orange-500'}" style="width: ${percent}%"></div>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-4 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <button class="edit-btn p-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-600 text-white transition-all active:scale-95" data-id="${a.id}" title="แก้ไข">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button class="members-btn p-2.5 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition-all active:scale-95" data-id="${a.id}" title="รายชื่อสมาชิก">
                            <i class="fas fa-users text-xs"></i>
                        </button>
                        <a href="print_best.php?id=${a.id}" target="_blank" class="p-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white transition-all active:scale-95" title="พิมพ์รายชื่อ">
                            <i class="fas fa-print text-xs"></i>
                        </a>
                        <button class="delete-btn p-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white transition-all active:scale-95" data-id="${a.id}" title="ลบ">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function updateSummary(data) {
    const total = data.length;
    const capacity = data.reduce((s, a) => s + parseInt(a.max_members || 0), 0);
    const registered = data.reduce((s, a) => s + parseInt(a.current_members_count || 0), 0);
    const fill = capacity > 0 ? Math.round((registered / capacity) * 100) : 0;
    document.getElementById('card-activities').textContent = total;
    document.getElementById('card-capacity').textContent = capacity;
    document.getElementById('card-registered').textContent = registered;
    document.getElementById('card-fill').textContent = fill + '%';
}

let selectedGradeFilter = 'all';

function toggleChartSection() {
    const sec = document.getElementById('chart-section');
    const isHidden = sec.classList.contains('hidden');
    const txt = document.getElementById('chart-toggle-text');
    const icon = document.getElementById('chart-toggle-icon');
    
    if (isHidden) {
        sec.classList.remove('hidden');
        txt.textContent = 'ซ่อนกราฟ';
        if (icon) icon.classList.add('rotate-180');
        renderChart([...activitiesData]);
    } else {
        sec.classList.add('hidden');
        txt.textContent = 'ดูกราฟสถิติ';
        if (icon) icon.classList.remove('rotate-180');
    }
}

function renderChart(data) {
    const chartSec = document.getElementById('chart-section');
    if (chartSec.classList.contains('hidden')) return;

    const top = [...data].sort((a, b) => parseInt(b.max_members || 0) - parseInt(a.max_members || 0)).slice(0, 8);
    const canvas = document.getElementById('best-chart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (bestChart) bestChart.destroy();
    
    if (top.length === 0) {
        bestChart = null;
        return;
    }

    bestChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: top.map(a => a.name.length > 18 ? a.name.substring(0, 18) + '...' : a.name),
            datasets: [
                { 
                    label: 'สมัครแล้ว', 
                    data: top.map(a => parseInt(a.current_members_count || 0)), 
                    backgroundColor: 'rgba(245, 158, 11, 0.85)', 
                    borderRadius: 8 
                },
                { 
                    label: 'คงเหลือ', 
                    data: top.map(a => Math.max(0, parseInt(a.max_members || 0) - parseInt(a.current_members_count || 0))), 
                    backgroundColor: 'rgba(226, 232, 240, 0.85)', 
                    borderRadius: 8 
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { weight: 'bold', family: 'Prompt, sans-serif' } } }
            },
            scales: {
                x: { stacked: true, grid: { display: false } },
                y: { stacked: true, beginAtZero: true }
            }
        }
    });
}

function updateYearUiState() {
    const isCurrent = (parseInt(selectedYear) === parseInt(CURRENT_SYSTEM_YEAR));
    const badge = document.getElementById('year-badge');
    const notice = document.getElementById('history-notice');
    const chartLabel = document.getElementById('chart-year-label');
    const modalSubtitle = document.getElementById('modal-subtitle');
    const activityYearInput = document.getElementById('activity_year');

    if (activityYearInput) activityYearInput.value = selectedYear;
    if (chartLabel) chartLabel.textContent = `ปีการศึกษา ${selectedYear}`;
    if (modalSubtitle) modalSubtitle.textContent = `ปีการศึกษา ${selectedYear}`;

    if (isCurrent) {
        badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300';
        badge.innerHTML = `<i class="fas fa-check-circle mr-1"></i>ปีการศึกษา ${selectedYear} (ปัจจุบัน)`;
        notice.classList.add('hidden');
    } else {
        badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-black bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300';
        badge.innerHTML = `<i class="fas fa-history mr-1"></i>ข้อมูลย้อนหลัง ปีการศึกษา ${selectedYear}`;
        notice.classList.remove('hidden');
        document.getElementById('history-year-text').textContent = selectedYear;
    }
}

async function loadData() {
    updateYearUiState();
    try {
        const res = await fetch(`../controllers/BestActivityController.php?action=list&year=${selectedYear}`);
        const data = await res.json();
        if (data.success) {
            activitiesData = data.data || [];
            renderMobileCards(activitiesData);
            renderTable(activitiesData);
            updateSummary(activitiesData);
            renderChart([...activitiesData]);
            applyFilters();
        } else {
            showToast(data.message || 'โหลดข้อมูลไม่สำเร็จ', 'error');
        }
    } catch (e) {
        showToast('โหลดข้อมูลไม่สำเร็จ', 'error');
    }
}

async function loadMembers(id) {
    try {
        const res = await fetch(`../controllers/BestActivityController.php?action=members&id=${id}`);
        const data = await res.json();
        const list = document.getElementById('members-list');
        const titleEl = document.getElementById('modal-members-title');
        const subtitleEl = document.getElementById('modal-members-subtitle');
        const printBtn = document.getElementById('modal-members-print-btn');

        if (printBtn) printBtn.href = `print_best.php?id=${id}`;

        if (data.success) {
            const act = data.activity || {};
            currentActivityMembers = data.members || [];
            titleEl.textContent = act.name ? `สมาชิก: ${act.name}` : 'จัดการสมาชิก';
            subtitleEl.textContent = `ปีการศึกษา ${data.year || selectedYear} • ทั้งหมด ${currentActivityMembers.length} คน`;

            if (currentActivityMembers.length > 0) {
                list.innerHTML = currentActivityMembers.map((m, idx) => `
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700/50">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 flex items-center justify-center font-black text-xs">${idx + 1}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-800 dark:text-white truncate">${m.name}</div>
                            <div class="text-xs text-gray-400 font-medium">${m.student_id} • ${m.class_name || 'ไม่ระบุห้อง'}</div>
                        </div>
                        <button class="remove-member-btn w-9 h-9 rounded-xl bg-rose-50 hover:bg-rose-500 text-rose-500 hover:text-white active:scale-95 transition-all flex items-center justify-center" data-sid="${m.student_id}" title="ลบสมาชิก">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                `).join('');
            } else {
                list.innerHTML = '<div class="text-center py-10 text-gray-400"><i class="fas fa-users-slash text-3xl mb-2 block text-gray-300"></i><p class="font-bold">ยังไม่มีสมาชิกในกิจกรรมนี้</p></div>';
            }
        }
    } catch (e) {
        showToast('โหลดสมาชิกไม่สำเร็จ', 'error');
    }
}

function clearActivitySearch() {
    const el = document.getElementById('activity-search');
    el.value = '';
    document.getElementById('btn-clear-activity-search').classList.add('hidden');
    applyFilters();
}

function applyFilters() {
    const q = document.getElementById('activity-search').value.trim().toLowerCase();
    const clearBtn = document.getElementById('btn-clear-activity-search');
    if (clearBtn) {
        if (q) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }

    document.querySelectorAll('.activity-card').forEach(c => {
        const name = c.dataset.name || '';
        const grades = c.dataset.grades || '';
        const matchQ = !q || name.includes(q) || grades.includes(q);
        const matchGrade = (selectedGradeFilter === 'all') || grades.includes(selectedGradeFilter.toLowerCase());
        c.style.display = (matchQ && matchGrade) ? '' : 'none';
    });

    document.querySelectorAll('.activity-row').forEach(r => {
        const name = r.dataset.name || '';
        const grades = r.dataset.grades || '';
        const matchQ = !q || name.includes(q) || grades.includes(q);
        const matchGrade = (selectedGradeFilter === 'all') || grades.includes(selectedGradeFilter.toLowerCase());
        r.style.display = (matchQ && matchGrade) ? '' : 'none';
    });
}

function filterActivities(q) {
    applyFilters();
}

// Student Autocomplete Logic
function clearSelectedStudent() {
    document.getElementById('member-student-id').value = '';
    document.getElementById('member-student-search').value = '';
    document.getElementById('selected-student-tag').classList.add('hidden');
    document.getElementById('btn-clear-search').classList.add('hidden');
    hideAutocompleteDropdown();
    document.getElementById('member-student-search').focus();
}

function selectStudent(student) {
    document.getElementById('member-student-id').value = student.student_id;
    document.getElementById('member-student-search').value = `${student.student_id} - ${student.fullname}`;
    
    document.getElementById('sel-tag-name').textContent = student.fullname;
    document.getElementById('sel-tag-id').textContent = student.student_id;
    document.getElementById('sel-tag-class').textContent = student.class_name;
    document.getElementById('selected-student-tag').classList.remove('hidden');
    document.getElementById('btn-clear-search').classList.remove('hidden');

    hideAutocompleteDropdown();
}

function hideAutocompleteDropdown() {
    const dropdown = document.getElementById('student-autocomplete-dropdown');
    dropdown.classList.add('hidden');
    activeAutocompleteIndex = -1;
}

async function searchStudents(query) {
    const dropdown = document.getElementById('student-autocomplete-dropdown');
    const loading = document.getElementById('autocomplete-loading');
    const clearBtn = document.getElementById('btn-clear-search');

    if (!query) {
        hideAutocompleteDropdown();
        clearBtn.classList.add('hidden');
        return;
    }

    clearBtn.classList.remove('hidden');
    loading.classList.remove('hidden');

    try {
        const res = await fetch(`../controllers/BestActivityController.php?action=search_students&q=${encodeURIComponent(query)}&year=${selectedYear}`);
        const data = await res.json();
        loading.classList.add('hidden');

        if (data.success && data.students) {
            currentAutocompleteStudents = data.students;
            activeAutocompleteIndex = -1;

            if (data.students.length === 0) {
                dropdown.innerHTML = `
                    <div class="p-4 text-center text-gray-400 text-xs font-bold">
                        <i class="fas fa-search text-gray-300 text-lg mb-1 block"></i>
                        ไม่พบข้อมูลนักเรียนที่ตรงกับ "${query}"
                    </div>
                `;
            } else {
                dropdown.innerHTML = data.students.map((s, idx) => {
                    const isAlreadyInThis = currentActivityMembers.some(m => m.student_id == s.student_id);
                    const isRegisteredOther = s.registered_activity_id && s.registered_activity_id != currentActivityId;
                    
                    let statusBadge = '';
                    if (isAlreadyInThis) {
                        statusBadge = '<span class="px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 text-[10px] font-black">อยู่ในกิจกรรมนี้แล้ว</span>';
                    } else if (isRegisteredOther) {
                        statusBadge = `<span class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-[10px] font-black truncate max-w-[140px]">ลง: ${s.registered_activity_name}</span>`;
                    } else {
                        statusBadge = '<span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300 text-[10px] font-black">ยังไม่ได้ลงทะเบียน</span>';
                    }

                    return `
                        <div class="autocomplete-item p-3 hover:bg-blue-50 dark:hover:bg-slate-700/50 cursor-pointer flex items-center justify-between gap-3 transition-colors ${idx === 0 ? 'bg-gray-50/50 dark:bg-slate-700/30' : ''}" data-index="${idx}">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0">
                                    ${(s.name || '').charAt(0)}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-black text-gray-800 dark:text-white text-sm truncate flex items-center gap-1.5">
                                        <span>${s.fullname}</span>
                                        <span class="text-xs font-mono font-bold text-gray-400">(${s.student_id})</span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">${s.class_name}</div>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                ${statusBadge}
                            </div>
                        </div>
                    `;
                }).join('');
            }
            dropdown.classList.remove('hidden');
        }
    } catch (e) {
        loading.classList.add('hidden');
    }
}

// Autocomplete Input Events
const studentSearchInput = document.getElementById('member-student-search');

studentSearchInput.addEventListener('input', function() {
    const q = this.value.trim();
    // Reset hidden student id if user changes query manually
    document.getElementById('member-student-id').value = '';
    document.getElementById('selected-student-tag').classList.add('hidden');

    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => {
        searchStudents(q);
    }, 200);
});

studentSearchInput.addEventListener('keydown', function(e) {
    const dropdown = document.getElementById('student-autocomplete-dropdown');
    if (dropdown.classList.contains('hidden')) return;

    const items = dropdown.querySelectorAll('.autocomplete-item');
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeAutocompleteIndex = (activeAutocompleteIndex + 1) % items.length;
        updateActiveAutocompleteItem(items);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeAutocompleteIndex = (activeAutocompleteIndex - 1 + items.length) % items.length;
        updateActiveAutocompleteItem(items);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (activeAutocompleteIndex >= 0 && activeAutocompleteIndex < currentAutocompleteStudents.length) {
            selectStudent(currentAutocompleteStudents[activeAutocompleteIndex]);
        } else if (currentAutocompleteStudents.length > 0) {
            selectStudent(currentAutocompleteStudents[0]);
        }
    } else if (e.key === 'Escape') {
        hideAutocompleteDropdown();
    }
});

function updateActiveAutocompleteItem(items) {
    items.forEach((it, idx) => {
        if (idx === activeAutocompleteIndex) {
            it.classList.add('bg-blue-100', 'dark:bg-slate-700');
            it.scrollIntoView({ block: 'nearest' });
        } else {
            it.classList.remove('bg-blue-100', 'dark:bg-slate-700');
        }
    });
}

// Click on Autocomplete item delegation
document.getElementById('student-autocomplete-dropdown').addEventListener('click', function(e) {
    const item = e.target.closest('.autocomplete-item');
    if (item) {
        const idx = parseInt(item.dataset.index);
        if (currentAutocompleteStudents[idx]) {
            selectStudent(currentAutocompleteStudents[idx]);
        }
    }
});

// Clear search button
document.getElementById('btn-clear-search').addEventListener('click', clearSelectedStudent);

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#member-student-search') && !e.target.closest('#student-autocomplete-dropdown')) {
        hideAutocompleteDropdown();
    }
});

// Year selector change
document.getElementById('year-select').addEventListener('change', function() {
    selectedYear = parseInt(this.value);
    loadData();
});

// Search input
document.getElementById('activity-search').addEventListener('input', function() {
    applyFilters();
});

// Grade Filter Buttons
document.querySelectorAll('.grade-filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        selectedGradeFilter = this.dataset.grade;
        document.querySelectorAll('.grade-filter-btn').forEach(b => {
            b.className = 'grade-filter-btn px-3.5 py-2.5 rounded-xl text-xs font-black bg-white/70 dark:bg-slate-800/70 border border-gray-200/60 dark:border-slate-700/60 text-gray-600 dark:text-gray-300 hover:bg-amber-100 hover:text-amber-700 dark:hover:bg-slate-700 transition-all';
        });
        this.className = 'grade-filter-btn px-3.5 py-2.5 rounded-xl text-xs font-black bg-amber-500 text-white shadow-sm transition-all';
        applyFilters();
    });
});

// New button handlers
['btn-new', 'btn-new-mobile'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('click', () => {
            document.getElementById('activity_id').value = '';
            document.getElementById('activity_year').value = selectedYear;
            document.getElementById('name').value = '';
            document.getElementById('description').value = '';
            document.querySelectorAll('.grade-opt').forEach(cb => cb.checked = false);
            document.getElementById('max_members').value = '';
            document.getElementById('modal-title').textContent = 'เพิ่มกิจกรรม';
            document.getElementById('modal-subtitle').textContent = `ปีการศึกษา ${selectedYear}`;
            document.getElementById('modal').classList.remove('hidden');
        });
    }
});

['btn-cancel', 'btn-cancel-2'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('click', () => document.getElementById('modal').classList.add('hidden'));
    }
});

document.getElementById('btn-close-members').addEventListener('click', () => {
    document.getElementById('members-modal').classList.add('hidden');
    clearSelectedStudent();
});

// Action click delegation
document.addEventListener('click', function(e) {
    const editBtn = e.target.closest('.edit-btn');
    if (editBtn) {
        const id = editBtn.dataset.id;
        const a = activitiesData.find(x => x.id == id);
        if (a) {
            document.getElementById('activity_id').value = a.id;
            document.getElementById('activity_year').value = a.year || selectedYear;
            document.getElementById('name').value = a.name;
            document.getElementById('description').value = a.description || '';
            const sel = (a.grade_levels || '').split(',').map(s => s.trim());
            document.querySelectorAll('.grade-opt').forEach(cb => cb.checked = sel.includes(cb.value));
            document.getElementById('max_members').value = a.max_members;
            document.getElementById('modal-title').textContent = 'แก้ไขกิจกรรม';
            document.getElementById('modal-subtitle').textContent = `ปีการศึกษา ${a.year || selectedYear}`;
            document.getElementById('modal').classList.remove('hidden');
        }
    }
    
    const membersBtn = e.target.closest('.members-btn');
    if (membersBtn) {
        currentActivityId = membersBtn.dataset.id;
        clearSelectedStudent();
        loadMembers(currentActivityId);
        document.getElementById('members-modal').classList.remove('hidden');
    }
    
    const deleteBtn = e.target.closest('.delete-btn');
    if (deleteBtn) {
        Swal.fire({
            title: 'ลบกิจกรรม?',
            text: 'การลบกิจกรรมจะส่งผลให้ข้อมูลการลงทะเบียนของกิจกรรมนี้ถูกลบด้วย',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'ลบกิจกรรม',
            cancelButtonText: 'ยกเลิก'
        }).then(r => {
            if (r.isConfirmed) {
                const fd = new FormData();
                fd.append('action', 'delete');
                fd.append('id', deleteBtn.dataset.id);
                fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            showToast('ลบกิจกรรมเรียบร้อย', 'success');
                            loadData();
                        } else {
                            showToast(d.message || 'ลบไม่สำเร็จ', 'error');
                        }
                    });
            }
        });
    }
    
    const rmBtn = e.target.closest('.remove-member-btn');
    if (rmBtn) {
        const sid = rmBtn.dataset.sid;
        Swal.fire({
            title: 'ลบสมาชิก?',
            text: `ต้องการนำนักเรียนรหัส ${sid} ออกจากกิจกรรมใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'ลบสมาชิก',
            cancelButtonText: 'ยกเลิก'
        }).then(r => {
            if (r.isConfirmed) {
                const fd = new FormData();
                fd.append('action', 'remove_member');
                fd.append('id', currentActivityId);
                fd.append('student_id', sid);
                fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            showToast('ลบสมาชิกสำเร็จ', 'success');
                            loadMembers(currentActivityId);
                            loadData();
                        } else {
                            showToast(d.message || 'ลบไม่สำเร็จ', 'error');
                        }
                    });
            }
        });
    }
});

// Add member button in modal
document.getElementById('btn-add-member').addEventListener('click', async function() {
    let sid = document.getElementById('member-student-id').value.trim();
    if (!sid) {
        // Fallback to extract ID from input if user typed directly
        const rawText = document.getElementById('member-student-search').value.trim();
        const match = rawText.match(/^\d+/);
        if (match) {
            sid = match[0];
        }
    }
    if (!sid) {
        showToast('กรุณาค้นหาและเลือกนักเรียน หรือกรอกรหัสประจำตัว', 'warning');
        return;
    }
    const fd = new FormData();
    fd.append('action', 'add_member');
    fd.append('id', currentActivityId);
    fd.append('student_id', sid);
    const res = await fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd });
    const d = await res.json();
    if (d.success) {
        showToast('เพิ่มสมาชิกสำเร็จ', 'success');
        clearSelectedStudent();
        loadMembers(currentActivityId);
        loadData();
    } else {
        showToast(d.message || 'เพิ่มไม่สำเร็จ', 'error');
    }
});

// Best Form Submit (Create / Update)
document.getElementById('best-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = document.getElementById('activity_id').value;
    const year = document.getElementById('activity_year').value || selectedYear;
    const grades = Array.from(document.querySelectorAll('.grade-opt:checked')).map(cb => cb.value);
    if (!grades.length) {
        showToast('เลือกระดับชั้นอย่างน้อย 1 ระดับชั้น', 'warning');
        return;
    }
    const fd = new FormData();
    fd.append('action', id ? 'update' : 'create');
    if (id) fd.append('id', id);
    fd.append('year', year);
    fd.append('name', document.getElementById('name').value);
    fd.append('description', document.getElementById('description').value);
    fd.append('grade_levels', grades.join(','));
    fd.append('max_members', document.getElementById('max_members').value);

    const res = await fetch('../controllers/BestActivityController.php', { method: 'POST', body: fd });
    const d = await res.json();
    if (d.success) {
        showToast(id ? 'แก้ไขกิจกรรมสำเร็จ' : 'เพิ่มกิจกรรมสำเร็จ', 'success');
        document.getElementById('modal').classList.add('hidden');
        loadData();
    } else {
        showToast(d.message || 'บันทึกไม่สำเร็จ', 'error');
    }
});

document.addEventListener('DOMContentLoaded', loadData);
</script>


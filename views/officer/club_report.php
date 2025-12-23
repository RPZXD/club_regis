<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-violet-500/30">
            <i class="fas fa-chart-bar text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-black text-gray-800 dark:text-white">รายงานชุมนุม</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">ดูสถิติและข้อมูลการลงทะเบียน</p>
        </div>
    </div>
</div>

<!-- Tab Navigation -->
<div class="flex flex-wrap gap-2 mb-6 overflow-x-auto pb-2">
    <button class="report-tab active px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2" data-tab="room">
        <i class="fas fa-door-open"></i>
        <span>รายห้อง</span>
    </button>
    <button class="report-tab px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2" data-tab="level">
        <i class="fas fa-layer-group"></i>
        <span>รายชั้น</span>
    </button>
    <button class="report-tab px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2" data-tab="overview">
        <i class="fas fa-chart-pie"></i>
        <span>ภาพรวม</span>
    </button>
    <button class="report-tab px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2" data-tab="club">
        <i class="fas fa-users"></i>
        <span>รายชุมนุม</span>
    </button>
</div>

<!-- Tab Content: Room -->
<div id="tab-room" class="tab-content">
    <div class="glass rounded-2xl p-5 mb-4">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-2">เลือกชั้น</label>
                <select id="select-level" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 font-bold focus:border-violet-500 focus:outline-none transition-all">
                    <option value="">-- เลือก --</option>
                    <?php foreach(['ม.1','ม.2','ม.3','ม.4','ม.5','ม.6'] as $g): ?>
                    <option value="<?= $g ?>"><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-2">เลือกห้อง</label>
                <select id="select-room" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 font-bold focus:border-violet-500 focus:outline-none transition-all" disabled>
                    <option value="">-- เลือก --</option>
                </select>
            </div>
        </div>
    </div>
    <div id="room-table-container" class="glass rounded-2xl p-5">
        <div class="text-center py-10 text-gray-400">
            <i class="fas fa-door-open text-3xl mb-3 opacity-30"></i>
            <p class="font-bold">กรุณาเลือกชั้นและห้อง</p>
        </div>
    </div>
</div>

<!-- Tab Content: Level -->
<div id="tab-level" class="tab-content hidden">
    <div class="glass rounded-2xl p-5 mb-4">
        <div>
            <label class="block text-sm font-bold text-gray-600 mb-2">เลือกชั้น</label>
            <select id="select-level2" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 font-bold focus:border-violet-500 focus:outline-none transition-all">
                <option value="">-- เลือก --</option>
                <?php foreach(['ม.1','ม.2','ม.3','ม.4','ม.5','ม.6'] as $g): ?>
                <option value="<?= $g ?>"><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div id="level-table-container" class="glass rounded-2xl p-5">
        <div class="text-center py-10 text-gray-400">
            <i class="fas fa-layer-group text-3xl mb-3 opacity-30"></i>
            <p class="font-bold">กรุณาเลือกชั้น</p>
        </div>
    </div>
</div>

<!-- Tab Content: Overview -->
<div id="tab-overview" class="tab-content hidden">
    <div id="overview-stats" class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6"></div>
    <div class="glass rounded-2xl p-5 mb-6">
        <canvas id="overview-chart" height="150"></canvas>
    </div>
    <div id="overview-table-container" class="glass rounded-2xl p-5">
        <div class="text-center py-10 text-gray-400">
            <i class="fas fa-chart-pie text-3xl mb-3 opacity-30"></i>
            <p class="font-bold">กำลังโหลดข้อมูล...</p>
        </div>
    </div>
</div>

<!-- Tab Content: Club -->
<div id="tab-club" class="tab-content hidden">
    <div id="club-table-container" class="glass rounded-2xl p-5">
        <div class="text-center py-10 text-gray-400">
            <i class="fas fa-users text-3xl mb-3 opacity-30"></i>
            <p class="font-bold">กำลังโหลดข้อมูล...</p>
        </div>
    </div>
</div>

<style>
.report-tab { background: rgb(243, 244, 246); color: rgb(107, 114, 128); }
.report-tab.active { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3); }
.dark .report-tab { background: rgb(51, 65, 85); color: rgb(148, 163, 184); }
.dark .report-tab.active { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Tab switching
document.querySelectorAll('.report-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.report-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
        
        if (this.dataset.tab === 'overview') loadOverview();
        if (this.dataset.tab === 'club') loadClubReport();
    });
});

// Room dropdown
document.getElementById('select-level').addEventListener('change', function() {
    const level = this.value;
    const roomSelect = document.getElementById('select-room');
    roomSelect.disabled = !level;
    roomSelect.innerHTML = '<option value="">-- เลือก --</option>';
    if (level) {
        // We could fetch actual rooms, but 1-15 is common. 
        // Let's use 1-12 as it's more standard for Phichai
        for (let i = 1; i <= 12; i++) {
            roomSelect.innerHTML += `<option value="${i}">${i}</option>`;
        }
    }
});

document.getElementById('select-room').addEventListener('change', function() {
    const level = document.getElementById('select-level').value;
    const room = this.value;
    if (level && room) loadRoomReport(level, room);
});

document.getElementById('select-level2').addEventListener('change', function() {
    if (this.value) loadLevelReport(this.value);
});

async function loadRoomReport(level, room) {
    const container = document.getElementById('room-table-container');
    container.innerHTML = '<div class="text-center py-20"><div class="w-12 h-12 border-4 border-violet-200 border-t-violet-600 rounded-full animate-spin mx-auto"></div><p class="mt-4 text-sm font-bold text-gray-400">กำลังดึงข้อมูลนักเรียน...</p></div>';
    
    try {
        const res = await fetch(`api/fetch_room_report.php?level=${level}&room=${room}`);
        const data = await res.json();
        
        if (Array.isArray(data) && data.length > 0) {
            let html = `
                <div class="hidden md:block overflow-x-auto rounded-xl">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-800 text-[10px] font-black uppercase text-gray-500 tracking-widest">
                                <th class="py-4 px-6 text-center w-16">เลขที่</th>
                                <th class="py-4 px-6">ชื่อ-นามสกุล</th>
                                <th class="py-4 px-6">ชุมนุม</th>
                                <th class="py-4 px-6">ครูที่ปรึกษา</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
            `;
            
            data.forEach(s => {
                html += `
                    <tr class="hover:bg-violet-50 dark:hover:bg-violet-900/10 transition-colors">
                        <td class="py-4 px-6 text-center font-bold text-gray-400">${s.number || '-'}</td>
                        <td class="py-4 px-6">
                            <div class="font-black text-gray-800 dark:text-white">${s.fullname}</div>
                            <div class="text-[10px] text-gray-400 font-mono">${s.student_id}</div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 rounded-lg ${s.club !== '-' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'} text-[10px] font-black uppercase tracking-tight border border-current opacity-80">
                                ${s.club || '-'}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-sm font-bold text-gray-500">${s.advisor || '-'}</td>
                    </tr>
                `;
            });
            
            html += `</tbody></table></div>`;
            
            // Mobile View
            html += `<div class="md:hidden space-y-3">`;
            data.forEach(s => {
                html += `
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border-l-4 ${s.club !== '-' ? 'border-emerald-500' : 'border-rose-500'} shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">เลขที่ ${s.number} | ${s.student_id}</div>
                            <span class="text-[10px] font-black ${s.club !== '-' ? 'text-emerald-500' : 'text-rose-500'} uppercase underline decoration-dotted">
                                ${s.club !== '-' ? 'สมัครแล้ว' : 'ยังไม่สมัคร'}
                            </span>
                        </div>
                        <div class="font-black text-gray-800 dark:text-white mb-3 text-sm">${s.fullname}</div>
                        <div class="flex flex-col gap-2 p-3 bg-gray-50 dark:bg-slate-900 rounded-xl">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-users text-[10px] text-gray-400"></i>
                                <span class="text-[10px] font-black text-gray-600 dark:text-gray-300">${s.club}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-chalkboard-teacher text-[10px] text-gray-400"></i>
                                <span class="text-[10px] font-bold text-gray-500 italic">${s.advisor}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += `</div>`;
            
            container.innerHTML = html;
        } else {
            container.innerHTML = '<div class="text-center py-20 text-gray-400">ไม่พบข้อมูลนักเรียนรุ่นนี้</div>';
        }
    } catch (e) {
        container.innerHTML = '<div class="text-center py-20 text-rose-500 font-bold">เกิดข้อผิดพลาดในการดึงข้อมูล</div>';
    }
}

async function loadLevelReport(level) {
    const container = document.getElementById('level-table-container');
    container.innerHTML = '<div class="text-center py-20"><div class="w-12 h-12 border-4 border-violet-200 border-t-violet-600 rounded-full animate-spin mx-auto"></div></div>';
    
    try {
        const res = await fetch(`api/fetch_level_report.php?level=${level}`);
        const data = await res.json();
        
        if (Array.isArray(data) && data.length > 0) {
            let html = `
                <div class="hidden md:block overflow-x-auto rounded-xl">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-800 text-[10px] font-black uppercase text-gray-500">
                                <th class="py-4 px-6 text-center w-24">ห้อง</th>
                                <th class="py-4 px-6 text-center w-32">จำนวนนักเรียน</th>
                                <th class="py-4 px-6">ชุมนุมยอดนิยม</th>
                                <th class="py-4 px-6 text-center w-32">สัดส่วน</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
            `;
            
            data.forEach(r => {
                html += `
                    <tr class="hover:bg-violet-50 dark:hover:bg-violet-900/10 transition-colors">
                        <td class="py-4 px-6 text-center">
                            <span class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center mx-auto text-violet-600 font-black">${r.room}</span>
                        </td>
                        <td class="py-4 px-6 text-center font-black text-gray-700 dark:text-gray-200">${r.student_count} คน</td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-800 dark:text-white text-sm">${r.top_club || '-'}</div>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2 py-1 rounded bg-gray-100 dark:bg-slate-700 text-[10px] font-black text-gray-500">${r.top_club_count} คน</span>
                        </td>
                    </tr>
                `;
            });
            
            html += `</tbody></table></div>`;
            
            // Mobile View
            html += `<div class="md:hidden space-y-3">`;
            data.forEach(r => {
                html += `
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-violet-500 text-white flex flex-col items-center justify-center leading-none">
                                <span class="text-[9px] font-black uppercase opacity-70">ม.${level.slice(-1)}/</span>
                                <span class="text-xl font-black">${r.room}</span>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">ยอดนิยม</div>
                                <div class="font-black text-gray-800 dark:text-white text-sm truncate max-w-[150px]">${r.top_club}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-black text-violet-600">${r.student_count}</div>
                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">STUDENTS</div>
                        </div>
                    </div>
                `;
            });
            html += `</div>`;
            
            container.innerHTML = html;
        } else {
            container.innerHTML = '<div class="text-center py-20 text-gray-400">ไม่พบข้อมูลรายงานรายชั้น</div>';
        }
    } catch (e) {
        container.innerHTML = '<div class="text-center py-20 text-rose-500 font-bold">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
    }
}

async function loadOverview() {
    const statsContainer = document.getElementById('overview-stats');
    const tableContainer = document.getElementById('overview-table-container');
    
    tableContainer.innerHTML = '<div class="text-center py-10"><div class="w-8 h-8 border-4 border-violet-200 border-t-violet-500 rounded-full animate-spin mx-auto"></div></div>';
    
    try {
        const [clubRes, studentRes] = await Promise.all([
            fetch('api/fetch_club_report.php'),
            fetch('api/fetch_student_count_by_level.php')
        ]);
        const clubData = await clubRes.json();
        const studentData = await studentRes.json();
        
        if (Array.isArray(clubData)) {
            const levels = ['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'];
            const sum = { total: 0 };
            levels.forEach(l => sum[l] = 0);
            
            clubData.forEach(row => {
                if (row.grade_levels) {
                    levels.forEach(l => {
                        if (row.grade_levels[l]) sum[l] += row.grade_levels[l];
                    });
                }
                sum.total += row.total_count || 0;
            });
            
            const colors = ['from-rose-500 to-red-600', 'from-amber-500 to-orange-600', 'from-emerald-500 to-teal-600', 'from-blue-500 to-indigo-600', 'from-violet-500 to-purple-600', 'from-pink-500 to-rose-600'];
            const textColors = ['text-rose-500', 'text-amber-500', 'text-emerald-500', 'text-blue-500', 'text-violet-500', 'text-pink-500'];
            const bgColors = ['bg-rose-50', 'bg-amber-50', 'bg-emerald-50', 'bg-blue-50', 'bg-violet-50', 'bg-pink-50'];

            statsContainer.innerHTML = levels.map((l, i) => {
                const total = studentData[l] || 0;
                const percent = total > 0 ? Math.round((sum[l] / total) * 100) : 0;
                return `
                    <div class="glass rounded-2xl p-4 border border-white/40 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br ${colors[i]} flex items-center justify-center text-white font-black text-xs shadow-sm">${l.slice(-1)}</div>
                            <span class="text-[10px] font-black ${textColors[i]} tracking-widest uppercase">${l}</span>
                        </div>
                        <div class="flex items-end justify-between mb-2">
                            <div class="text-xl font-black text-gray-800 dark:text-white">${sum[l]} <span class="text-[10px] text-gray-400">/ ${total}</span></div>
                            <div class="text-[10px] font-black ${textColors[i]}">${percent}%</div>
                        </div>
                        <div class="w-full h-1.5 ${bgColors[i]} dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r ${colors[i]} rounded-full transition-all duration-1000" style="width: ${percent}%"></div>
                        </div>
                    </div>
                `;
            }).join('') + `
                <div class="glass rounded-2xl p-6 col-span-2 md:col-span-4 bg-gradient-to-br from-violet-600 to-indigo-700 text-white shadow-xl shadow-indigo-500/20 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <div class="text-xs font-black uppercase tracking-widest opacity-80 mb-1">ภาพรวมการลงทะเบียนทั้งหมด</div>
                            <div class="text-4xl font-black leading-none">${sum.total} <span class="text-lg opacity-70">/ ${Object.values(studentData).reduce((a,b)=>a+b,0)}</span></div>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl">
                            <i class="fas fa-users-crown"></i>
                        </div>
                    </div>
                </div>`;
            
            tableContainer.innerHTML = `
                <div class="flex items-center justify-center gap-2 py-4 text-emerald-500 font-black text-sm uppercase tracking-widest animate__animated animate__pulse animate__infinite">
                    <i class="fas fa-check-circle"></i>
                    <span>Real-time data synced</span>
                </div>
            `;
        }
    } catch (e) {
        console.error('Overview loading error:', e);
        tableContainer.innerHTML = '<div class="text-center py-10 text-rose-500"><i class="fas fa-exclamation-triangle"></i> เกิดข้อผิดพลาดในการเชื่อมต่อข้อมูล</div>';
    }
}

async function loadClubReport() {
    const container = document.getElementById('club-table-container');
    container.innerHTML = '<div class="text-center py-20"><div class="w-12 h-12 border-4 border-violet-200 border-t-violet-600 rounded-full animate-spin mx-auto"></div></div>';
    
    try {
        const res = await fetch('api/fetch_club_report.php');
        const data = await res.json();
        
        if (Array.isArray(data) && data.length > 0) {
            // Sort by total_count desc
            data.sort((a,b) => (b.total_count||0) - (a.total_count||0));
            
            container.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${data.map((club, i) => `
                        <div class="group glass rounded-2xl p-4 flex items-center gap-5 border border-white/20 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex flex-col items-center justify-center text-white font-black shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">
                                <span class="text-[9px] opacity-70 font-black uppercase">TOP</span>
                                <span class="text-xl">${i+1}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-black text-gray-800 dark:text-white truncate text-sm mb-0.5">${club.club_name}</div>
                                <div class="text-[10px] text-gray-400 font-bold flex items-center gap-1">
                                    <i class="fas fa-chalkboard-teacher text-[8px]"></i>
                                    ${club.advisor || '-'}
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="text-xl font-black text-violet-600 dark:text-violet-400 leading-none">${club.total_count || 0}</div>
                                <div class="text-[9px] font-black text-gray-300 uppercase tracking-tighter mt-1">MEMBERS</div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        } else {
            container.innerHTML = '<div class="text-center py-20 text-gray-400 flex flex-col items-center gap-3"><i class="fas fa-inbox text-4xl opacity-20"></i><p class="font-bold">ยังไม่มีข้อมูลรายงานชุมนุม</p></div>';
        }
    } catch (e) {
        container.innerHTML = '<div class="text-center py-20 text-rose-500 font-bold">เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์</div>';
    }
}
</script>

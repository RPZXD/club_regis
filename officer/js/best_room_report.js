(function(){
  let dtSummary; let dtStudents;
  async function loadSummary(){
    try {
      const res = await fetch('api/best_fetch_room_report.php');
      const json = await res.json();
      const tbody = document.querySelector('#best-room-table tbody');
      if (!tbody) return;
      tbody.innerHTML = '';
      
      if (!json || !json.ok) {
        console.error('API Error:', json?.message || 'Unknown error');
        tbody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>';
        return;
      }
      
      const data = json.data || [];
      if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="2" class="text-center text-muted">ไม่มีข้อมูล</td></tr>';
        return;
      }
      
      const levels = new Set();
      const roomsByLevel = {};
      data.forEach(r=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${r.room}</td><td class="text-right"><span class="badge badge-info">${r.cnt}</span></td>`;
        tbody.appendChild(tr);
        // build selectors data
        const m = String(r.room).match(/^ม\.(\d+)\/(\d+)/);
        if (m) {
          levels.add(m[1]);
          roomsByLevel[m[1]] = roomsByLevel[m[1]] || new Set();
          roomsByLevel[m[1]].add(m[2]);
        }
      });
      if (dtSummary) dtSummary.destroy();
      dtSummary = $('#best-room-table').DataTable({ 
        paging:true, 
        searching:true, 
        ordering:true, 
        order:[[0,'asc']], 
        dom: 'Bfrtip', 
        buttons:[
          {extend: 'copy', className: 'btn-primary btn-sm'},
          {extend: 'csv', className: 'btn-success btn-sm'},
          {extend: 'excel', className: 'btn-info btn-sm'},
          {extend: 'print', className: 'btn-secondary btn-sm'}
        ],
        language: {
          search: 'ค้นหา:',
          emptyTable: 'ไม่มีข้อมูล',
          zeroRecords: 'ไม่พบข้อมูลที่ค้นหา'
        }
      });

      // populate selectors if empty
      const lvlSel = document.getElementById('room-level-select');
      const roomSel = document.getElementById('room-room-select');
      if (lvlSel && lvlSel.options.length === 0) {
        lvlSel.innerHTML = '<option value="">-- เลือกชั้น --</option>';
        Array.from(levels).sort((a,b)=>a-b).forEach(l=>{
          const opt = document.createElement('option'); opt.value = l; opt.textContent = 'ม.'+l; lvlSel.appendChild(opt);
        });
        lvlSel.addEventListener('change', ()=> fillRooms(lvlSel.value));
        if (lvlSel.value) fillRooms(lvlSel.value);
      }
      function fillRooms(level){
        if (!roomSel) return;
        roomSel.innerHTML = '<option value="">-- เลือกห้อง --</option>';
        const set = roomsByLevel[level] || new Set();
        Array.from(set).sort((a,b)=>parseInt(a)-parseInt(b)).forEach(rm=>{
          const opt = document.createElement('option'); opt.value = rm; opt.textContent = rm; roomSel.appendChild(opt);
        });
      }
    } catch (error) {
      console.error('Fetch Error:', error);
      const tbody = document.querySelector('#best-room-table tbody');
      if (tbody) tbody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>';
    }
  }

  async function loadStudents(){
    try {
      const lvl = document.getElementById('room-level-select')?.value || '';
      const rm = document.getElementById('room-room-select')?.value || '';
      
      if (!lvl) {
        alert('กรุณาเลือกชั้นก่อน');
        return;
      }
      
      const url = new URL('api/best_fetch_room_students.php', window.location.origin+window.location.pathname.replace(/\/[^/]*$/, '/'));
      url.searchParams.set('level', lvl);
      if (rm) url.searchParams.set('room', rm);
      
      const res = await fetch(url.toString());
      const json = await res.json();
      
      if (!json.ok) {
        console.error('API Error:', json.message || 'Unknown error');
        showStudentError('ไม่สามารถโหลดข้อมูลได้');
        return;
      }
      
      const rows = json.data || [];
      const tbody = document.querySelector('#best-room-students-table tbody');
      if (!tbody) return;
      tbody.innerHTML = '';
      
      if (rows.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">ไม่พบข้อมูลนักเรียน</td></tr>';
        return;
      }
      
      rows.forEach((r,i)=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td class="text-center">${i+1}</td>
                        <td><code>${r.student_id}</code></td>
                        <td>${r.name}</td>
                        <td><span class="badge badge-secondary">${r.room}</span></td>
                        <td><small class="text-primary">${r.activity}</small></td>`;
        tbody.appendChild(tr);
      });
      if (dtStudents) dtStudents.destroy();
      dtStudents = $('#best-room-students-table').DataTable({ 
        paging:true, 
        searching:true, 
        ordering:true, 
        order:[[1,'asc']], 
        dom:'Bfrtip', 
        buttons:[
          {extend: 'copy', className: 'btn-primary btn-sm'},
          {extend: 'csv', className: 'btn-success btn-sm'},
          {extend: 'excel', className: 'btn-info btn-sm'},
          {extend: 'print', className: 'btn-secondary btn-sm'}
        ],
        language: {
          search: 'ค้นหา:',
          emptyTable: 'ไม่มีข้อมูล',
          zeroRecords: 'ไม่พบข้อมูลที่ค้นหา'
        }
      });
    } catch (error) {
      console.error('Fetch Error:', error);
      showStudentError('เกิดข้อผิดพลาดในการโหลดข้อมูล');
    }
  }
  
  function showStudentError(message) {
    const tbody = document.querySelector('#best-room-students-table tbody');
    if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${message}</td></tr>`;
  }

  window.initBestRoom = function(){
    loadSummary();
    const btn = document.getElementById('room-search');
    if (btn && !btn._hooked) { 
      btn.addEventListener('click', loadStudents); 
      btn._hooked = true; 
    }
  }
})();

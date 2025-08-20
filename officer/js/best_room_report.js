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

  // Optimized loadStudents function with enhanced performance
  let loadingRequest = null; // Prevent multiple concurrent requests
  
  async function loadStudents(){
    // Cancel previous request if still pending
    if (loadingRequest) {
      loadingRequest.abort();
    }
    
    const tbody = document.querySelector('#best-room-students-table tbody');
    if (!tbody) return;
    
    try {
      const lvl = document.getElementById('room-level-select')?.value || '';
      const rm = document.getElementById('room-room-select')?.value || '';
      
      if (!lvl) {
        alert('กรุณาเลือกชั้นก่อน');
        return;
      }
      
      // Show loading state immediately
      tbody.innerHTML = '<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin text-primary"></i> กำลังโหลดข้อมูล...</td></tr>';
      
      // Create AbortController for request cancellation
      const controller = new AbortController();
      loadingRequest = controller;
      
      const url = new URL('api/best_fetch_room_students.php', window.location.origin+window.location.pathname.replace(/\/[^/]*$/, '/'));
      url.searchParams.set('level', lvl);
      if (rm) url.searchParams.set('room', rm);
      
      // Add timeout and optimized fetch options
      const res = await Promise.race([
        fetch(url.toString(), {
          signal: controller.signal,
          cache: 'no-cache',
          headers: {
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
          }
        }),
        new Promise((_, reject) => 
          setTimeout(() => reject(new Error('Request timeout - เครือข่ายช้า')), 15000)
        )
      ]);
      
      if (!res.ok) {
        throw new Error(`HTTP ${res.status}: ${res.statusText}`);
      }
      
      const json = await res.json();
      loadingRequest = null;
      
      if (!json.ok) {
        throw new Error(json.message || 'API Error');
      }
      
      const rows = json.data || [];
      tbody.innerHTML = '';
      
      if (rows.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">ไม่พบข้อมูลนักเรียน</td></tr>';
        return;
      }
      
      // Efficient DOM creation with DocumentFragment
      const fragment = document.createDocumentFragment();
      
      rows.forEach((r, i) => {
        const tr = document.createElement('tr');
        if (!r.has_activity) {
          tr.className = 'table-warning'; // Highlight students without activity
        }
        
        tr.innerHTML = `
          <td class="text-center">${i+1}</td>
          <td><code>${escapeHtml(r.student_id)}</code></td>
          <td>${escapeHtml(r.name)}</td>
          <td><span class="badge badge-secondary">${escapeHtml(r.room)}</span></td>
          <td>
            ${r.has_activity 
              ? `<span class="badge badge-success">${escapeHtml(r.activity)}</span>`
              : '<span class="badge badge-warning">ไม่ได้สมัคร</span>'
            }
          </td>`;
        fragment.appendChild(tr);
      });
      
      tbody.appendChild(fragment);
      
      // Reinitialize DataTable with optimized settings
      if (dtStudents) {
        dtStudents.destroy();
      }
      
      dtStudents = $('#best-room-students-table').DataTable({ 
        paging: true,
        pageLength: 25, // Reduce initial page size for faster rendering
        searching: true, 
        ordering: true, 
        order: [[1, 'asc']], 
        dom: 'Bfrtip', 
        buttons: [
          {extend: 'copy', className: 'btn-primary btn-sm'},
          {extend: 'csv', className: 'btn-success btn-sm', filename: `รายชื่อนักเรียน_ม.${lvl}${rm ? '_' + rm : ''}`},
          {extend: 'excel', className: 'btn-info btn-sm', filename: `รายชื่อนักเรียน_ม.${lvl}${rm ? '_' + rm : ''}`},
          {extend: 'print', className: 'btn-secondary btn-sm', title: `รายชื่อนักเรียน ม.${lvl}${rm ? '/' + rm : ''}`}
        ],
        language: {
          search: 'ค้นหา:',
          emptyTable: 'ไม่มีข้อมูล',
          zeroRecords: 'ไม่พบข้อมูลที่ค้นหา',
          lengthMenu: 'แสดง _MENU_ รายการต่อหน้า',
          info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ',
          paginate: {
            first: 'หน้าแรก',
            last: 'หน้าสุดท้าย',
            next: 'ถัดไป',
            previous: 'ก่อนหน้า'
          }
        },
        responsive: true,
        deferRender: true, // Improve performance for large datasets
        stateSave: false // Disable state saving
      });
      
    } catch (error) {
      loadingRequest = null;
      
      if (error.name === 'AbortError') {
        return; // Request was cancelled, ignore
      }
      
      console.error('Fetch Error:', error);
      showStudentError(`เกิดข้อผิดพลาด: ${error.message}`);
    }
  }
  
  // HTML escape function for security
  function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
  
  function showStudentError(message) {
    const tbody = document.querySelector('#best-room-students-table tbody');
    if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${message}</td></tr>`;
  }

  window.initBestRoom = function(){
    loadSummary();
    const btn = document.getElementById('room-search');
    if (btn && !btn._hooked) { 
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Prevent multiple clicks during loading
        if (btn.disabled || loadingRequest) {
          return;
        }
        
        // Disable button during request
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังโหลด...';
        
        loadStudents().finally(() => {
          // Re-enable button after request completes
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-search"></i> ค้นหา';
        });
      }); 
      btn._hooked = true; 
    }
  }
})();

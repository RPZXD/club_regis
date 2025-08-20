(function(){
  let dtSummary; let dtStudents;
  // Global variables for room data
  let allLevels = [];
  let allRoomsByLevel = {};
  
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
      
      data.forEach(r=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${r.room}</td><td class="text-right"><span class="badge badge-info">${r.cnt}</span></td>`;
        tbody.appendChild(tr);
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

      // Now load all available rooms for dropdowns
      await loadAllRooms();
      
    } catch (error) {
      console.error('Fetch Error:', error);
      const tbody = document.querySelector('#best-room-table tbody');
      if (tbody) tbody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>';
    }
  }
  
  // Load all available rooms from student database
  async function loadAllRooms(){
    try {
      const res = await fetch('api/best_fetch_all_rooms.php');
      const json = await res.json();
      
      if (!json || !json.ok) {
        console.error('API Error:', json?.message || 'Unknown error');
        return;
      }
      
      allLevels = json.data.levels || [];
      allRoomsByLevel = json.data.roomsByLevel || {};
      
      // Populate level selector
      const lvlSel = document.getElementById('room-level-select');
      const roomSel = document.getElementById('room-room-select');
      
      if (lvlSel) {
        lvlSel.innerHTML = '<option value="">-- เลือกชั้น --</option>';
        allLevels.forEach(level => {
          const opt = document.createElement('option'); 
          opt.value = level; 
          opt.textContent = 'ม.' + level; 
          lvlSel.appendChild(opt);
        });
        
        // Add event listeners if not already added
        if (!lvlSel._eventAdded) {
          lvlSel.addEventListener('change', () => {
            fillRooms(lvlSel.value);
          });
          lvlSel._eventAdded = true;
        }
      }
      
      // Add event listener for room selection change
      if (roomSel && !roomSel._eventAdded) {
        roomSel.addEventListener('change', () => {
          // Clear results when room changes
          clearStudentTable();
        });
        roomSel._eventAdded = true;
      }
      
      // Initialize with empty table
      clearStudentTable();
      
    } catch (error) {
      console.error('Error loading all rooms:', error);
    }
  }
  
  function fillRooms(level){
    const roomSel = document.getElementById('room-room-select');
    if (!roomSel) return;
    
    roomSel.innerHTML = '<option value="">-- เลือกห้อง (ทุกห้อง) --</option>';
    
    const rooms = allRoomsByLevel[level] || [];
    rooms.forEach(room => {
      const opt = document.createElement('option'); 
      opt.value = room; 
      opt.textContent = room; 
      roomSel.appendChild(opt);
    });
    
    // Clear previous results when changing level/room
    clearStudentTable();
  }
  
  // Add function to clear student table
  function clearStudentTable() {
    const tbody = document.querySelector('#best-room-students-table tbody');
    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted"><i class="fas fa-info-circle mr-2"></i>กรุณาเลือกชั้นและห้อง แล้วกดค้นหา</td></tr>';
    }
    
    // Destroy DataTable if exists
    if (dtStudents) {
      dtStudents.destroy();
      dtStudents = null;
    }
  }

  // Optimized loadStudents function with enhanced performance
  let loadingRequest = null; // Prevent multiple concurrent requests
  
  async function loadStudents(){
    // Cancel previous request if still pending
    if (loadingRequest) {
      loadingRequest.abort();
      loadingRequest = null;
    }
    
    const tbody = document.querySelector('#best-room-students-table tbody');
    if (!tbody) return;
    
    // Destroy existing DataTable first
    if (dtStudents) {
      dtStudents.destroy();
      dtStudents = null;
    }
    
    try {
      const lvl = document.getElementById('room-level-select')?.value || '';
      const rm = document.getElementById('room-room-select')?.value || '';
      
      if (!lvl) {
        Swal.fire({
          icon: 'warning',
          title: 'กรุณาเลือกชั้น',
          text: 'โปรดเลือกชั้นก่อนทำการค้นหา',
          confirmButtonText: 'ตกลง'
        });
        return;
      }
      
      // Debug: Show what we're searching for
      console.log(`🔍 Searching for level: ${lvl}, room: ${rm || 'ทุกห้อง'}`);
      
      // Show loading state immediately
      tbody.innerHTML = '<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin text-primary"></i> กำลังค้นหาข้อมูล...</td></tr>';
      
      // Create AbortController for request cancellation
      const controller = new AbortController();
      loadingRequest = controller;
      
      // Build URL with timestamp to prevent caching
      const url = new URL('api/best_fetch_room_students.php', window.location.origin+window.location.pathname.replace(/\/[^/]*$/, '/'));
      url.searchParams.set('level', lvl);
      if (rm) {
        url.searchParams.set('room', rm);
      }
      url.searchParams.set('_t', Date.now()); // Cache buster
      
      // Debug: Show the URL being called
      console.log('🌐 Fetching URL:', url.toString());
      
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
      
      // Debug: Show what we received
      console.log(`📊 Received ${rows.length} students for level ${lvl}, room ${rm || 'ทุกห้อง'}`);
      
      if (rows.length === 0) {
        const roomText = rm ? `ห้อง ${rm}` : 'ทุกห้อง';
        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">
          <i class="fas fa-info-circle mr-2"></i>ไม่พบนักเรียนใน ม.${lvl} ${roomText}
        </td></tr>`;
        return;
      }
      
      // Clear tbody completely before adding new content
      tbody.innerHTML = '';
      
      // Efficient DOM creation with DocumentFragment
      const fragment = document.createDocumentFragment();
      let studentsWithActivity = 0;
      let studentsWithoutActivity = 0;
      
      rows.forEach((r, i) => {
        const tr = document.createElement('tr');
        if (!r.has_activity) {
          tr.className = 'table-warning'; // Highlight students without activity
          studentsWithoutActivity++;
        } else {
          studentsWithActivity++;
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
      
      // Show summary after data is loaded
      const roomText = rm ? `ห้อง ${rm}` : 'ทุกห้อง';
      console.log(`✅ สรุป ม.${lvl} ${roomText}: นักเรียนทั้งหมด ${rows.length} คน, สมัครกิจกรรม ${studentsWithActivity} คน, ไม่ได้สมัคร ${studentsWithoutActivity} คน`);
      
      // Show toast notification with summary
      if (window.toastr) {
        toastr.success(`พบนักเรียน ${rows.length} คน (สมัครกิจกรรม ${studentsWithActivity} คน, ไม่ได้สมัคร ${studentsWithoutActivity} คน)`, 
                      `ม.${lvl} ${roomText}`, 
                      {timeOut: 3000});
      }
      
      // Force re-initialize DataTable with fresh data
      setTimeout(() => {
        if (dtStudents) {
          dtStudents.destroy();
        }
        
        dtStudents = $('#best-room-students-table').DataTable({ 
          paging: true,
          pageLength: 25, 
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
          deferRender: true, 
          stateSave: false,
          destroy: true // Force destroy and recreate
        });
      }, 100); // Small delay to ensure DOM is ready
      
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

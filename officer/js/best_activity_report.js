(function(){
  let dtActivities; let dtMembers;
  async function loadActivities(){
    try {
      const res = await fetch('api/best_fetch_activities.php');
      const json = await res.json();
      const select = document.querySelector('#activity-select');
      if (!select) return;
      
      // Keep the default option
      select.innerHTML = '<option value="">-- เลือกกิจกรรม --</option>';
      
      if (!json || !json.ok) {
        console.error('API Error:', json?.message || 'Unknown error');
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'เกิดข้อผิดพลาดในการโหลดข้อมูล';
        option.disabled = true;
        select.appendChild(option);
        return;
      }
      
      const data = json.data || [];
      if (data.length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'ไม่มีข้อมูลกิจกรรม';
        option.disabled = true;
        select.appendChild(option);
        return;
      }
      
      data.forEach(r=>{
        const option = document.createElement('option');
        option.value = r.activity_name;
        option.textContent = `${r.activity_name} (${r.cnt || 0} คน)`;
        select.appendChild(option);
      });
      
    } catch (error) {
      console.error('Fetch Error:', error);
      const select = document.querySelector('#activity-select');
      if (select) {
        select.innerHTML = '<option value="">-- เกิดข้อผิดพลาด --</option>';
      }
    }
  }

  async function loadMembers(){
    try {
      const activityName = document.getElementById('activity-select')?.value;
      if (!activityName) {
        alert('กรุณาเลือกกิจกรรมก่อน');
        return;
      }
      
      const params = new URLSearchParams();
      params.set('activity', activityName);
      
      const res = await fetch('api/best_fetch_activity_members.php?'+params);
      const json = await res.json();
      
      if (!json || !json.ok) {
        console.error('API Error:', json?.message || 'Unknown error');
        showMemberError('ไม่สามารถโหลดข้อมูลสมาชิกได้');
        return;
      }
      
      const rows = json.data || [];
      const tbody = document.querySelector('#best-activity-members-table tbody');
      if (!tbody) return;
      tbody.innerHTML = '';
      
      if (rows.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">ไม่พบสมาชิกในกิจกรรมนี้</td></tr>';
        return;
      }
      
      rows.forEach((r,i)=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td class="text-center">${i+1}</td>
                        <td><code>${r.student_id}</code></td>
                        <td>${r.name}</td>
                        <td><span class="badge badge-secondary">${r.room || '-'}</span></td>
                        <td><small class="text-muted">${r.created_at || '-'}</small></td>`;
        tbody.appendChild(tr);
      });
      
      if (dtMembers) dtMembers.destroy();
      dtMembers = $('#best-activity-members-table').DataTable({ 
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
      showMemberError('เกิดข้อผิดพลาดในการโหลดข้อมูล');
    }
  }
  
  function showMemberError(message) {
    const tbody = document.querySelector('#best-activity-members-table tbody');
    if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${message}</td></tr>`;
  }

  window.initBestActivity = function(){
    loadActivities();
    const btn = document.getElementById('activity-load');
    if (btn && !btn._hooked) { 
      btn.addEventListener('click', loadMembers); 
      btn._hooked = true; 
    }
  }
})();

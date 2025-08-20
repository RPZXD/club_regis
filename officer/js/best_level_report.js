(function(){
  let dt; let chart;
  async function load(){
    try {
      const sel = document.getElementById('level-select');
      const level = sel ? sel.value : '1';
      const res = await fetch('api/best_fetch_level_activity.php?level='+level);
      const json = await res.json();
      
      if (!json.ok) {
        console.error('API Error:', json.message || 'Unknown error');
        showError('ไม่สามารถโหลดข้อมูลได้');
        return;
      }
      
      const rows = json.data || [];
      renderTable(rows, level);
      renderChart(rows);
    } catch (error) {
      console.error('Fetch Error:', error);
      showError('เกิดข้อผิดพลาดในการโหลดข้อมูล');
    }
  }
  
  function showError(message) {
    const tbody = document.querySelector('#best-level-activity-table tbody');
    tbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">${message}</td></tr>`;
  }
  
  function renderTable(rows, level){
    const tbody = document.querySelector('#best-level-activity-table tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    
    if (!rows || rows.length === 0) {
      tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted">ไม่มีข้อมูลกิจกรรมสำหรับชั้น ม.${level}</td></tr>`;
      return;
    }
    
    rows.sort((a,b)=> b.count - a.count).forEach((r,idx)=>{
      const tr = document.createElement('tr');
      tr.innerHTML = `<td class="text-center font-weight-bold">${idx+1}</td>
                      <td><i class="fas fa-star text-warning mr-2"></i>${r.name}</td>
                      <td class="text-center"><span class="badge badge-primary badge-pill">${r.count}</span></td>`;
      tbody.appendChild(tr);
    });
    if (dt) dt.destroy();
    dt = $('#best-level-activity-table').DataTable({ 
      paging:true, 
      searching:true, 
      ordering:true, 
      order:[[2,'desc']], 
      dom:'Bfrtip', 
      buttons: [
        {extend: 'copy', className: 'btn-primary btn-sm'},
        {extend: 'csv', className: 'btn-success btn-sm'},
        {extend: 'excel', className: 'btn-info btn-sm'},
        {extend: 'print', className: 'btn-secondary btn-sm'}
      ],
      language: {
        search: 'ค้นหา:',
        lengthMenu: 'แสดง _MENU_ รายการ',
        info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ',
        paginate: { first: 'แรก', last: 'สุดท้าย', next: 'ถัดไป', previous: 'ก่อนหน้า' },
        emptyTable: 'ไม่มีข้อมูล',
        zeroRecords: 'ไม่พบข้อมูลที่ค้นหา'
      }
    });
  }
  function renderChart(rows){
    if (!rows || rows.length === 0) return;
    
    const canvas = document.getElementById('best-level-chart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (chart) chart.destroy();
    
    // Create gradient colors
    const colors = rows.map((_, i) => {
      const hue = (i * 360 / rows.length) % 360;
      return `hsla(${hue}, 70%, 60%, 0.8)`;
    });
    
    chart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: rows.map(r=>r.name),
        datasets: [{ 
          label:'จำนวนผู้สมัคร', 
          data: rows.map(r=>parseInt(r.count) || 0), 
          backgroundColor: colors,
          borderColor: colors.map(c => c.replace('0.8', '1')),
          borderWidth: 2
        }]
      },
      options:{
        responsive:true,
        maintainAspectRatio: false,
        plugins: {
          legend: { 
            position: 'bottom',
            labels: { usePointStyle: true }
          },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleColor: '#fff',
            bodyColor: '#fff'
          }
        },
        animation: {
          duration: 1500,
          easing: 'easeOutBounce'
        }
      }
    });
  }
  window.initBestLevel = function(){
    const sel = document.getElementById('level-select');
    if (sel && !sel._hooked) { 
      sel.addEventListener('change', load); 
      sel._hooked = true; 
      // Add visual feedback on change
      sel.addEventListener('change', function() {
        this.style.transform = 'scale(0.98)';
        setTimeout(() => { this.style.transform = 'scale(1)'; }, 100);
      });
    }
    load();
  }
})();

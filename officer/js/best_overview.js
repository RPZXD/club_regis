(function(){
  let dt;
  let chart;
  async function load() {
    try {
      const res = await fetch('api/best_fetch_overview.php');
      const json = await res.json();
      
      if (!json.success) {
        console.error('API Error:', json.message);
        showError('ไม่สามารถโหลดข้อมูลได้: ' + json.message);
        return;
      }
      
      const data = json.data || [];
      renderTable(data);
      renderChart(data);
      // Update stats in main page
      if (window.updateStats) window.updateStats(data);
    } catch (error) {
      console.error('Fetch Error:', error);
      showError('เกิดข้อผิดพลาดในการโหลดข้อมูล');
    }
  }
  
  function showError(message) {
    const tbody = document.querySelector('#best-overview-table tbody');
    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${message}</td></tr>`;
  }
  
  function renderTable(rows){
    const tbody = document.querySelector('#best-overview-table tbody');
    tbody.innerHTML = '';
    
    if (!rows || rows.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">ไม่มีข้อมูลกิจกรรม</td></tr>';
      return;
    }
    
    rows.forEach((r,idx)=>{
      const tr = document.createElement('tr');
      const pct = r.max_members>0 ? Math.round((r.current_members_count*100)/r.max_members) : 0;
      let pctClass = 'text-success';
      if (pct >= 100) pctClass = 'text-danger';
      else if (pct >= 80) pctClass = 'text-warning';
      
      tr.innerHTML = `<td class="text-center font-weight-bold">${idx+1}</td>
                      <td><i class="fas fa-star text-warning mr-2"></i>${r.name}</td>
                      <td class="text-center"><span class="badge badge-info">${r.max_members}</span></td>
                      <td class="text-center"><span class="badge badge-success">${r.current_members_count}</span></td>
                      <td class="text-center"><span class="badge ${pctClass === 'text-success' ? 'badge-success' : pctClass === 'text-warning' ? 'badge-warning' : 'badge-danger'}">${pct}%</span></td>
                      <td><small class="text-muted">${r.grade_levels}</small></td>`;
      tbody.appendChild(tr);
    });
    if (dt) { dt.destroy(); }
    dt = $('#best-overview-table').DataTable({
      paging: true,
      searching: true,
      ordering: true,
      order: [[4,'desc']],
      dom: 'Bfrtip',
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
    
    // Check if Chart.js is available
    if (typeof Chart === 'undefined') {
      console.error('Chart.js is not loaded');
      return;
    }

    const ctx = document.getElementById('best-overview-chart');
    if (!ctx) {
      console.error('Chart canvas element not found');
      return;
    }
    
    const context = ctx.getContext('2d');
    if (!context) {
      console.error('Could not get 2d context from canvas');
      return;
    }

    const labels = rows.map(r=>r.name.length > 15 ? r.name.substring(0,15)+'...' : r.name);
    const current = rows.map(r=>parseInt(r.current_members_count) || 0);
    const capacity = rows.map(r=>parseInt(r.max_members) || 0);

    // helper to round up to "nice" numbers
    function niceMax(n) {
      if (n <= 10) return 10;
      if (n <= 25) return 25;
      if (n <= 50) return 50;
      if (n <= 100) return 100;
      if (n <= 200) return 200;
      if (n <= 500) return 500;
      return Math.ceil(n/100) * 100;
    }

    const maxCurrent = Math.max(...current, 0);
    const maxCapacity = Math.max(...capacity, 0);
    const leftMax = niceMax(maxCurrent || 1);
    const rightMax = niceMax(maxCapacity || 1);

    function stepFor(maxVal){
      if (maxVal <= 10) return 1;
      if (maxVal <= 50) return 5;
      if (maxVal <= 100) return 10;
      if (maxVal <= 500) return 25;
      return 50;
    }

    const leftStep = stepFor(leftMax);
    const rightStep = stepFor(rightMax);

    // Safely destroy existing chart
    try {
      if (chart && typeof chart.destroy === 'function') {
        chart.destroy();
        chart = null;
      }
    } catch (e) {
      console.warn('Error destroying existing chart:', e);
      chart = null;
    }

    try {
      chart = new Chart(context, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {
              label: 'ลงทะเบียนแล้ว',
              data: current,
              backgroundColor: 'rgba(54, 162, 235, 0.85)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1,
              yAxisID: 'y'
            },
            {
              label: 'รับได้สูงสุด',
              data: capacity,
              type: 'line',
              borderColor: 'rgba(255, 99, 132, 0.9)',
              backgroundColor: 'rgba(255, 99, 132, 0.15)',
              fill: false,
              tension: 0.1,
              borderWidth: 2,
              pointBackgroundColor: 'rgba(255, 99, 132, 1)',
              pointBorderColor: '#fff',
              pointBorderWidth: 1,
              pointRadius: 5,
              borderDash: [6,4],
              yAxisID: 'y1'
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom' },
            tooltip: {
              callbacks: {
                label: function(context){
                  return context.dataset.label + ': ' + context.parsed.y + ' คน';
                }
              }
            }
          },
          scales: {
            y: {
              type: 'linear',
              position: 'left',
              beginAtZero: true,
              suggestedMax: leftMax,
              ticks: {
                stepSize: leftStep,
                callback: v => Number.isInteger(v) ? v + ' คน' : ''
              },
              title: { display: true, text: 'ลงทะเบียนแล้ว' }
            },
            y1: {
              type: 'linear',
              position: 'right',
              beginAtZero: true,
              suggestedMax: rightMax,
              ticks: {
                stepSize: rightStep,
                callback: v => Number.isInteger(v) ? v + ' คน' : ''
              },
              grid: { drawOnChartArea: false },
              title: { display: true, text: 'รับได้สูงสุด' }
            },
            x: { grid: { display: false } }
          },
          interaction: { mode: 'index', intersect: false },
          animation: { duration: 800, easing: 'easeOutQuart' }
        }
      });
    } catch (error) {
      console.error('Error creating chart:', error);
      console.log('Chart.js version:', Chart.version || 'unknown');
    }
  }
  window.initBestOverview = load;
})();

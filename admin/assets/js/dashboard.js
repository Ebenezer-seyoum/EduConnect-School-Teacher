$(function () {
  // Utility to render status badge
  function statusBadge(status) {
    var text = status === 1 ? 'Active' : 'Inactive';
    var cls = status === 1 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-muted';
    return '<span class="badge ' + cls + '">' + text + '</span>';
  }

  // Initialize chart with empty data; we'll update after fetch
  var options_sales_overview = {
    series: [{ name: 'Users', data: [] }],
    chart: { type: 'bar', height: 275, toolbar: { show: false }, foreColor: '#adb0bb', fontFamily: 'inherit' },
    grid: { show: false, borderColor: 'transparent', padding: { left: 0, right: 0, bottom: 0 } },
    plotOptions: { bar: { horizontal: false, columnWidth: '35%', endingShape: 'rounded', borderRadius: 5 } },
    colors: ['var(--bs-primary)'],
    dataLabels: { enabled: false },
    yaxis: { show: true },
    stroke: { show: true, width: 5, lineCap: 'butt', colors: ['transparent'] },
    xaxis: { type: 'category', categories: [], axisBorder: { show: false } },
    fill: { opacity: 1 },
    tooltip: { theme: 'dark' },
    legend: { show: false },
  };

  var chart_column_basic = new ApexCharts(document.querySelector('#sales-overview'), options_sales_overview);
  chart_column_basic.render();

  function renderRecentUsers(list) {
    var container = $('#recent-users');
    if (!list || list.length === 0) {
      container.html('<div class="text-muted">No recent users</div>');
      return;
    }
    var html = list.map(function (u) {
      return (
        '<div class="py-2 d-flex align-items-center border-bottom">' +
        '  <span class="btn btn-light rounded-circle round-48 hstack justify-content-center me-3"><i class="ti ti-user fs-6"></i></span>' +
        '  <div class="flex-grow-1">' +
        '    <div class="fw-semibold">' + (u.full_name || 'User') + '</div>' +
        '    <div class="text-muted small">' + (u.user_type || 'unknown') + '</div>' +
        '  </div>' +
        '  <div>' + statusBadge(u.user_status) + '</div>' +
        '</div>'
      );
    }).join('');
    container.html(html);
  }

  function applyDashboardData(data) {
    // KPIs
    $('#kpi-total-users').text(data.totalUsers ?? '--');
    $('#kpi-active-users').text(data.activeUsers ?? '--');
    $('#kpi-inactive-users').text(data.inactiveUsers ?? '--');
    $('#kpi-teachers').text(data.teachers ?? '--');

    // Chart: users by type
    var cats = (data.byType || []).map(function (r) { return r.user_type || 'unknown'; });
    var vals = (data.byType || []).map(function (r) { return r.count || 0; });
    chart_column_basic.updateOptions({ xaxis: { categories: cats } });
    chart_column_basic.updateSeries([{ name: 'Users', data: vals }]);

    // Recent users
    renderRecentUsers(data.recentUsers || []);
  }

  function fetchDashboard() {
    return $.ajax({ url: 'dashboard-data.php', dataType: 'json', cache: false });
  }

  function fallbackStatic() {
    // Provide basic demo numbers if API fails
    applyDashboardData({
      totalUsers: 0,
      activeUsers: 0,
      inactiveUsers: 0,
      teachers: 0,
      byType: [
        { user_type: 'teacher', count: 0 },
        { user_type: 'admin', count: 0 },
      ],
      recentUsers: [],
    });
  }

  // Initial load
  fetchDashboard().done(applyDashboardData).fail(fallbackStatic);

  // Wire refresh
  $(document).on('click', '#refresh-dashboard', function () {
    fetchDashboard().done(applyDashboardData).fail(fallbackStatic);
  });

  // Optional: Auto refresh toggle
  var autoTimer = null;
  $(document).on('click', '#auto-refresh-toggle', function () {
    if (autoTimer) {
      clearInterval(autoTimer);
      autoTimer = null;
      $(this).text('Auto Refresh');
    } else {
      autoTimer = setInterval(function () {
        fetchDashboard().done(applyDashboardData);
      }, 15000);
      $(this).text('Auto Refresh (ON)');
    }
  });
});
<?php
// File: view/admin/dashboard.php

// --- 1. THỐNG KÊ SỐ LIỆU CƠ BẢN ---
$count_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$count_locked = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'locked'")->fetchColumn();
$count_clubs = $pdo->query("SELECT COUNT(*) FROM clubs")->fetchColumn();
$count_events = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$count_no_leader = $pdo->query("SELECT COUNT(*) FROM clubs WHERE leader_id IS NULL")->fetchColumn();

// --- 2. DỮ LIỆU BIỂU ĐỒ (Đã ép kiểu số int) ---
// Biểu đồ 1: Tăng trưởng thành viên
$sql_chart1 = "SELECT DATE_FORMAT(created_at, '%m/%Y') as month_year, COUNT(*) as total 
               FROM users 
               GROUP BY month_year 
               ORDER BY MAX(created_at) DESC LIMIT 6"; 
$stmt_chart1 = $pdo->query($sql_chart1);
$chart1_data = array_reverse($stmt_chart1->fetchAll()); 

$months = []; 
$member_counts = [];
foreach ($chart1_data as $row) { 
    $months[] = "Tháng " . $row['month_year']; 
    $member_counts[] = (int)$row['total']; // Ép kiểu số nguyên
}

// Biểu đồ 2: Top CLB
$sql_chart2 = "SELECT c.name, COUNT(cm.user_id) as total 
               FROM clubs c 
               JOIN club_members cm ON c.club_id = cm.club_id 
               WHERE cm.status = 'approved'
               GROUP BY c.club_id 
               ORDER BY total DESC LIMIT 5";
$stmt_chart2 = $pdo->query($sql_chart2);
$chart2_data = $stmt_chart2->fetchAll();

$club_names = []; 
$club_counts = [];
foreach ($chart2_data as $row) { 
    $club_names[] = $row['name']; 
    $club_counts[] = (int)$row['total']; // Ép kiểu số nguyên
}

// --- 3. DANH SÁCH THÀNH VIÊN MỚI ---
$sql_new_members = "
    SELECT u.full_name, u.email, c.name as club_name, cm.joined_at, cm.status
    FROM club_members cm
    JOIN users u ON cm.user_id = u.user_id
    JOIN clubs c ON cm.club_id = c.club_id
    ORDER BY cm.member_id DESC
    LIMIT 5
";
$stmt_members = $pdo->query($sql_new_members);
$new_members = $stmt_members->fetchAll();
?>

<!-- NẠP THƯ VIỆN BIỂU ĐỒ (Đảm bảo luôn chạy được) -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- CSS RIÊNG -->
<style>
    /* Grid */
    .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 30px; }
    
    /* Cards */
    .stat-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #f0f0f0; transition: transform 0.2s; display: flex; flex-direction: column; justify-content: space-between; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
    
    .stat-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px; }
    .stat-title { color: #64748b; font-size: 0.9rem; font-weight: 600; }
    
    /* Icon Box */
    .stat-icon-box { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0; }
    .stat-icon-box svg { width: 24px; height: 24px; }
    
    .bg-purple { background: #8b5cf6; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3); }
    .bg-green { background: #10b981; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
    .bg-orange { background: #f97316; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3); }
    .bg-blue { background: #3b82f6; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
    
    .stat-number { font-size: 2.2rem; font-weight: 700; color: #1e293b; margin-bottom: 5px; line-height: 1; }
    
    /* Trend */
    .stat-trend { display: flex; align-items: center; font-size: 0.85rem; color: #94a3b8; font-weight: 500; }
    .trend-up { color: #10b981; display: flex; align-items: center; gap: 4px; margin-right: 6px; }
    .trend-down { color: #ef4444; display: flex; align-items: center; gap: 4px; margin-right: 6px; }
    .trend-neutral { color: #64748b; margin-right: 6px; }

    /* Table */
    .table-card { background: #fff; border-radius: 16px; border: 1px solid #f0f0f0; overflow: hidden; margin-bottom: 30px; }
    .table-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th { text-align: left; color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; padding: 16px 24px; background: #f8fafc; font-weight: 600; }
    .custom-table td { padding: 16px 24px; border-bottom: 1px solid #f8fafc; color: #334155; font-size: 0.95rem; vertical-align: middle; }
    
    /* Avatar & Badges */
    .avatar-initial { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 12px; font-size: 1rem; }
    .bg-avt-1 { background: #e0f2fe; color: #0284c7; }
    .bg-avt-2 { background: #fce7f3; color: #be185d; }
    .badge-soft { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
    .badge-green { background: #dcfce7; color: #166534; }
    .badge-orange { background: #ffedd5; color: #9a3412; }
</style>

<!-- 1. HEADER -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tổng quan hệ thống</h1>
        <p class="text-gray-500 mt-1">Chào mừng trở lại, <strong><?php echo getName(); ?></strong>!</p>
    </div>
</div>

<!-- 2. STAT CARDS (Dùng SVG trực tiếp để không bị lỗi mất icon) -->
<div class="dashboard-grid">
    
    <!-- Card 1: Thành viên -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Tổng Thành viên</span>
            <div class="stat-icon-box bg-purple">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
        </div>
        <div class="stat-number"><?php echo number_format($count_users); ?></div>
        <div class="stat-trend">
            <?php if ($count_locked > 0): ?>
                <span class="trend-down">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> 
                    <?php echo $count_locked; ?>
                </span> bị khóa
            <?php else: ?>
                <span class="trend-up">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    Ổn định
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Card 2: Câu lạc bộ -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Câu lạc bộ</span>
            <div class="stat-icon-box bg-green">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>
            </div>
        </div>
        <div class="stat-number"><?php echo number_format($count_clubs); ?></div>
        <div class="stat-trend">
            <?php if ($count_no_leader > 0): ?>
                <span class="trend-down">⚠️ <?php echo $count_no_leader; ?></span> thiếu Leader
            <?php else: ?>
                <span class="trend-up">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    Tăng trưởng
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Card 3: Sự kiện -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Sự kiện</span>
            <div class="stat-icon-box bg-orange">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
        </div>
        <div class="stat-number"><?php echo number_format($count_events); ?></div>
        <div class="stat-trend"><span class="trend-neutral">- Hoạt động</span></div>
    </div>

    <!-- Card 4: Trạng thái -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Trạng thái</span>
            <div class="stat-icon-box bg-blue">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
        </div>
        <div class="stat-number text-blue-600">Online</div>
        <div class="stat-trend">
            <span class="trend-up">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                100%
            </span> Uptime
        </div>
    </div>
</div>

<!-- 3. BIỂU ĐỒ (Charts) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Chart 2 -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Top CLB Đông Nhất</h3>
        <div id="topClubsChart" style="min-height: 300px;"></div>
    </div>
</div>

<!-- 4. DANH SÁCH THÀNH VIÊN MỚI -->
<div class="table-card">
    <div class="table-header">
        <h4 class="text-lg font-bold text-gray-800 m-0">Thành viên mới gia nhập</h4>
    </div>
    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>HỌ TÊN</th>
                    <th>CLB THAM GIA</th>
                    <th>NGÀY THAM GIA</th>
                    <th>TRẠNG THÁI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($new_members)): ?>
                    <tr><td colspan="4" class="text-center text-gray-400 py-8">Chưa có dữ liệu.</td></tr>
                <?php endif; ?>

                <?php 
                $i = 0;
                $colors = ['bg-avt-1', 'bg-avt-2', 'bg-avt-1', 'bg-avt-2'];
                foreach ($new_members as $mem): 
                    $i++;
                    $initial = mb_substr($mem['full_name'], 0, 1);
                    $bg = $colors[$i % 2];
                ?>
                <tr>
                    <td>
                        <div class="flex items-center">
                            <div class="avatar-initial <?php echo $bg; ?>"><?php echo $initial; ?></div>
                            <div>
                                <div class="font-semibold text-gray-800"><?php echo htmlspecialchars($mem['full_name']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($mem['email']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="font-medium text-slate-600"><?php echo htmlspecialchars($mem['club_name']); ?></td>
                    <td><?php echo $mem['joined_at'] ? date('d/m/Y', strtotime($mem['joined_at'])) : '---'; ?></td>
                    <td>
                        <?php if ($mem['status'] == 'approved'): ?>
                            <span class="badge-soft badge-green">Chính thức</span>
                        <?php else: ?>
                            <span class="badge-soft badge-orange">Chờ duyệt</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 5. JAVASCRIPT CHART (Chạy sau khi DOM load) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Dữ liệu từ PHP (đã ép kiểu số)
    var memberData = <?php echo json_encode($member_counts); ?>;
    var monthLabels = <?php echo json_encode($months); ?>;
    var clubData = <?php echo json_encode($club_counts); ?>;
    var clubLabels = <?php echo json_encode($club_names); ?>;

    // Chart 1: Area
    if (document.querySelector("#memberGrowthChart")) {
        var options1 = {
            series: [{ name: 'Thành viên', data: memberData }],
            chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            colors: ['#10b981'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2, stops: [0, 90, 100] } },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { categories: monthLabels, axisBorder: { show: false }, axisTicks: { show: false } },
            grid: { borderColor: '#f1f5f9' }
        };
        new ApexCharts(document.querySelector("#memberGrowthChart"), options1).render();
    }

    // Chart 2: Donut
    if (document.querySelector("#topClubsChart") && clubData.length > 0) {
        var options2 = {
            series: clubData,
            labels: clubLabels,
            chart: { type: 'donut', height: 320, fontFamily: 'Inter, sans-serif' },
            colors: ['#3b82f6', '#10b981', '#f97316', '#8b5cf6', '#ef4444'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: false }
        };
        new ApexCharts(document.querySelector("#topClubsChart"), options2).render();
    } else if (document.querySelector("#topClubsChart")) {
        document.querySelector("#topClubsChart").innerHTML = "<p class='text-center text-gray-400 py-10'>Chưa có dữ liệu CLB</p>";
    }
});
</script>
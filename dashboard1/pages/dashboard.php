<?php
// pages/dashboard.php

// وظيفة لجلب إحصائيات الخدمات من قاعدة البيانات
function getServicesStats() {
    global $database;
    
    $stats = [
        'total' => 0,
        'active' => 0,
        'inactive' => 0,
        'by_type' => []
    ];
    
    // إجمالي الخدمات
    $result = $database->query("SELECT COUNT(*) as total FROM services");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total'] = $row['total'] ?? 0;
    }
    
    // الخدمات النشطة
    $result = $database->query("SELECT COUNT(*) as active FROM services WHERE is_active = 1");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['active'] = $row['active'] ?? 0;
    }
    
    // الخدمات غير النشطة
    $stats['inactive'] = $stats['total'] - $stats['active'];
    
    // الخدمات حسب النوع بناءً على الأيقونات
    $result = $database->query("
        SELECT 
            CASE 
                WHEN icon LIKE '%building%' THEN 'أنظمة ERP'
                WHEN icon LIKE '%mobile-alt%' THEN 'تطبيقات الجوال'
                WHEN icon LIKE '%laptop-code%' THEN 'تطوير الويب'
                WHEN icon LIKE '%tools%' THEN 'حلول مخصصة'
                ELSE 'خدمات أخرى'
            END as type,
            COUNT(*) as count
        FROM services 
        WHERE is_active = 1
        GROUP BY type
    ");
    
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $stats['by_type'][$row['type']] = (int)$row['count'];
        }
    }
    
    return $stats;
}

// وظيفة لجلب إحصائيات العملاء من قاعدة البيانات
function getClientsStats() {
    global $database;
    
    $stats = [
        'total' => 0,
        'by_month' => [],
        'recent' => []
    ];
    
    // إجمالي العملاء
    $result = $database->query("SELECT COUNT(*) as total FROM clients");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total'] = $row['total'] ?? 0;
    }
    
    // العملاء حسب الشهر (آخر 6 أشهر)
    $result = $database->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            DATE_FORMAT(created_at, '%b') as month_name_short,
            COUNT(*) as count
        FROM clients 
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b')
        ORDER BY month ASC
    ");
    
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $stats['by_month'][] = [
                'month' => $row['month'],
                'month_name' => $row['month_name_short'],
                'count' => (int)$row['count']
            ];
        }
    }
    
    // أحدث العملاء
    $result = $database->query("
        SELECT name, DATE_FORMAT(created_at, '%Y-%m-%d') as date 
        FROM clients 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $stats['recent'][] = $row;
        }
    }
    
    return $stats;
}

// وظيفة لجلب إحصائيات طلبات التواصل من قاعدة البيانات
function getContactsStats() {
    global $database;
    
    $stats = [
        'total' => 0,
        'by_month' => [],
        'recent' => []
    ];
    
    // إجمالي طلبات التواصل
    $result = $database->query("SELECT COUNT(*) as total FROM contacts");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total'] = $row['total'] ?? 0;
    }
    
    // طلبات التواصل حسب الشهر (آخر 6 أشهر)
    $result = $database->query("
        SELECT 
            DATE_FORMAT(create_at, '%Y-%m') as month,
            DATE_FORMAT(create_at, '%b') as month_name_short,
            COUNT(*) as count
        FROM contacts 
        WHERE create_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(create_at, '%Y-%m'), DATE_FORMAT(create_at, '%b')
        ORDER BY month ASC
    ");
    
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $stats['by_month'][] = [
                'month' => $row['month'],
                'month_name' => $row['month_name_short'],
                'count' => (int)$row['count']
            ];
        }
    }
    
    // أحدث طلبات التواصل
    $result = $database->query("
        SELECT name, email, DATE_FORMAT(create_at, '%Y-%m-%d %H:%i') as date 
        FROM contacts 
        ORDER BY create_at DESC 
        LIMIT 5
    ");
    
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $stats['recent'][] = $row;
        }
    }
    
    return $stats;
}

// وظيفة لجلب إحصائيات المستخدمين من قاعدة البيانات
function getUsersStats() {
    global $database;
    
    $stats = [
        'total' => 0,
        'by_role' => [],
        'active' => 0
    ];
    
    // إجمالي المستخدمين
    $result = $database->query("SELECT COUNT(*) as total FROM users");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total'] = $row['total'] ?? 0;
    }
    
    // المستخدمين حسب الدور
    $result = $database->query("
        SELECT 
            CASE role
                WHEN 'admin' THEN 'مدير'
                WHEN 'editor' THEN 'محرر'
                WHEN 'viewer' THEN 'مشاهد'
                ELSE role
            END as role_name,
            COUNT(*) as count
        FROM users 
        WHERE is_active = 1
        GROUP BY role
    ");
    
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $stats['by_role'][$row['role_name']] = (int)$row['count'];
        }
    }
    
    // المستخدمين النشطين
    $result = $database->query("SELECT COUNT(*) as active FROM users WHERE is_active = 1");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['active'] = $row['active'] ?? 0;
    }
    
    return $stats;
}

// وظيفة جديدة: جلب إحصائيات من جدول statistics
function getStatisticsData() {
    global $database;
    
    $stats = [
        'total' => 0,
        'data' => [],
        'recent' => []
    ];
    
    // إجمالي الإحصائيات
    $result = $database->query("SELECT COUNT(*) as total FROM statistics");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total'] = $row['total'] ?? 0;
    }
    
    // جلب جميع الإحصائيات
    $result = $database->query("
        SELECT id, title, value, icon, 
               DATE_FORMAT(created_at, '%Y-%m-%d') as created_date
        FROM statistics 
        ORDER BY value DESC
    ");
    
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $stats['data'][] = [
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'value' => (int)$row['value'],
                'icon' => $row['icon'],
                'created_date' => $row['created_date']
            ];
        }
    }
    
    // أحدث الإحصائيات (آخر 5)
    $result = $database->query("
        SELECT title, value, DATE_FORMAT(created_at, '%Y-%m-%d') as date 
        FROM statistics 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $stats['recent'][] = $row;
        }
    }
    
    return $stats;
}

// جلب جميع الإحصائيات
$services_stats = getServicesStats();
$clients_stats = getClientsStats();
$contacts_stats = getContactsStats();
$users_stats = getUsersStats();
$statistics_data = getStatisticsData(); // الإحصائيات الجديدة

// تصحيح: جلب أسماء الأشهر العربية
$arabic_months = [
    'Jan' => 'يناير', 'Feb' => 'فبراير', 'Mar' => 'مارس', 'Apr' => 'أبريل',
    'May' => 'مايو', 'Jun' => 'يونيو', 'Jul' => 'يوليو', 'Aug' => 'أغسطس',
    'Sep' => 'سبتمبر', 'Oct' => 'أكتوبر', 'Nov' => 'نوفمبر', 'Dec' => 'ديسمبر'
];

// تحويل أسماء الأشهر إلى العربية
foreach ($clients_stats['by_month'] as &$month) {
    $month['month_name_ar'] = $arabic_months[$month['month_name']] ?? $month['month_name'];
}
unset($month);

foreach ($contacts_stats['by_month'] as &$month) {
    $month['month_name_ar'] = $arabic_months[$month['month_name']] ?? $month['month_name'];
}
unset($month);
?>

<div id="dashboard" class="content-page active">
    <!-- إشعارات -->
    <div id="dashboardAlerts"></div>
    
    <!-- بطاقات الإحصائيات الرئيسية -->
    <div class="stats-cards">
        <div class="card stat-card">
            <div class="stat-info">
                <h3 id="totalClients"><?php echo $clients_stats['total']; ?></h3>
                <p>إجمالي العملاء</p>
                <small>آخر 6 أشهر: <span id="recentClients"><?php echo array_sum(array_column($clients_stats['by_month'], 'count')); ?></span></small>
            </div>
            <div class="stat-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="card stat-card">
            <div class="stat-info">
                <h3 id="totalServices"><?php echo $services_stats['total']; ?></h3>
                <p>إجمالي الخدمات</p>
                <small>نشطة: <span id="activeServices"><?php echo $services_stats['active']; ?></span></small>
            </div>
            <div class="stat-icon bg-success">
                <i class="fas fa-cogs"></i>
            </div>
        </div>
        <div class="card stat-card">
            <div class="stat-info">
                <h3 id="totalContacts"><?php echo $contacts_stats['total']; ?></h3>
                <p>طلبات التواصل</p>
                <small>آخر 6 أشهر: <span id="recentContacts"><?php echo array_sum(array_column($contacts_stats['by_month'], 'count')); ?></span></small>
            </div>
            <div class="stat-icon bg-warning">
                <i class="fas fa-envelope"></i>
            </div>
        </div>
        <div class="card stat-card">
            <div class="stat-info">
                <h3 id="totalStatistics"><?php echo $statistics_data['total']; ?></h3>
                <p>إحصائيات الموقع</p>
                <small>إجمالي الإحصائيات</small>
            </div>
            <div class="stat-icon bg-info">
                <i class="fas fa-chart-bar"></i>
            </div>
        </div>
    </div>

    <!-- الصف الأول: مخططات الخدمات والإحصائيات -->
    <div class="charts">
        <!-- مخطط توزيع الخدمات حسب النوع (كعكي) -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">توزيع الخدمات حسب النوع</div>
                <div class="chart-actions">
                    <select id="servicesChartType" onchange="updateChartType('servicesChart', this.value)">
                        <option value="pie">كعكي</option>
                        <option value="doughnut">دائري</option>
                    </select>
                </div>
            </div>
            <div class="chart-canvas">
                <canvas id="servicesChart"></canvas>
            </div>
            <div style="text-align: center; margin-top: 10px; color: #666; font-size: 0.9rem;">
                إجمالي الخدمات: <?php echo $services_stats['total']; ?> | نشطة: <?php echo $services_stats['active']; ?>
            </div>
        </div>

        <!-- مخطط الإحصائيات (أعمدة) -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">إحصائيات الموقع</div>
                <div class="chart-actions">
                    <select id="statisticsChartType" onchange="updateStatisticsChart(this.value)">
                        <option value="bar">أعمدة</option>
                        <option value="line">خطي</option>
                    </select>
                </div>
            </div>
            <div class="chart-canvas">
                <canvas id="statisticsChart"></canvas>
            </div>
            <div style="text-align: center; margin-top: 10px; color: #666; font-size: 0.9rem;">
                إجمالي الإحصائيات: <?php echo $statistics_data['total']; ?>
            </div>
        </div>
    </div>

    <!-- الصف الثاني: مخططات التواصل والعملاء الشهرية -->
    <div class="charts">
        <!-- مخطط طلبات التواصل الشهري (خطي) -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">طلبات التواصل الشهرية</div>
                <div class="chart-actions">
                    <select id="messagesChartPeriod" onchange="updateChartData('messages', this.value)">
                        <option value="6months">آخر 6 أشهر</option>
                        <option value="year">هذه السنة</option>
                    </select>
                </div>
            </div>
            <div class="chart-canvas">
                <canvas id="messagesChart"></canvas>
            </div>
            <div style="text-align: center; margin-top: 10px; color: #666; font-size: 0.9rem;">
                إجمالي الطلبات: <?php echo $contacts_stats['total']; ?>
            </div>
        </div>

        <!-- مخطط العملاء الشهري (أعمدة) -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">العملاء الشهريين</div>
                <div class="chart-actions">
                    <select id="clientsChartPeriod" onchange="updateChartData('clients', this.value)">
                        <option value="6months">آخر 6 أشهر</option>
                        <option value="year">هذه السنة</option>
                    </select>
                </div>
            </div>
            <div class="chart-canvas">
                <canvas id="clientsChart"></canvas>
            </div>
            <div style="text-align: center; margin-top: 10px; color: #666; font-size: 0.9rem;">
                إجمالي العملاء: <?php echo $clients_stats['total']; ?>
            </div>
        </div>
    </div>

    <!-- الصف الثالث: مخططات المستخدمين والمقارنة -->
    <div class="charts">
        <!-- مخطط توزيع المستخدمين حسب الدور (دائري) -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">توزيع المستخدمين حسب الدور</div>
                <div class="chart-actions">
                    <select id="usersChartType" onchange="updateChartType('usersChart', this.value)">
                        <option value="doughnut">دائري</option>
                        <option value="pie">كعكي</option>
                    </select>
                </div>
            </div>
            <div class="chart-canvas">
                <canvas id="usersChart"></canvas>
            </div>
            <div style="text-align: center; margin-top: 10px; color: #666; font-size: 0.9rem;">
                إجمالي المستخدمين: <?php echo $users_stats['total']; ?> | نشطين: <?php echo $users_stats['active']; ?>
            </div>
        </div>

        <!-- مخطط حالة الخدمات (خطي) -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">حالة الخدمات</div>
                <div class="chart-actions">
                    <select id="servicesStatusType" onchange="updateServicesStatusChart(this.value)">
                        <option value="line">خطي</option>
                        <option value="bar">أعمدة</option>
                    </select>
                </div>
            </div>
            <div class="chart-canvas">
                <canvas id="servicesStatusChart"></canvas>
            </div>
            <div style="text-align: center; margin-top: 10px; color: #666; font-size: 0.9rem;">
                النسبة: <?php echo round(($services_stats['active'] / max(1, $services_stats['total'])) * 100, 1); ?>% نشطة
            </div>
        </div>
    </div>

    <!-- الصف الرابع: مخططات إضافية -->
    <div class="charts">
        <!-- مخطط مقارنة بين العملاء والتواصل (أعمدة مزدوجة) -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">مقارنة العملاء وطلبات التواصل (آخر 6 أشهر)</div>
                <div class="chart-actions">
                    <select id="comparisonChartType" onchange="updateComparisonChart(this.value)">
                        <option value="bar">أعمدة</option>
                        <option value="line">خطي مزدوج</option>
                    </select>
                </div>
            </div>
            <div class="chart-canvas">
                <canvas id="comparisonChart"></canvas>
            </div>
            <div style="text-align: center; margin-top: 10px; color: #666; font-size: 0.9rem;">
                متوسط/شهر: عملاء: <?php echo count($clients_stats['by_month']) > 0 ? round(array_sum(array_column($clients_stats['by_month'], 'count')) / count($clients_stats['by_month']), 1) : 0; ?> | 
                تواصل: <?php echo count($contacts_stats['by_month']) > 0 ? round(array_sum(array_column($contacts_stats['by_month'], 'count')) / count($contacts_stats['by_month']), 1) : 0; ?>
            </div>
        </div>

        <!-- مخطط توزيع الإحصائيات حسب القيمة (أعمدة) -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">توزيع إحصائيات الموقع حسب القيمة</div>
            </div>
            <div class="chart-canvas">
                <canvas id="statisticsDistributionChart"></canvas>
            </div>
            <div style="text-align: center; margin-top: 10px; color: #666; font-size: 0.9rem;">
                أعلى قيمة: <?php echo count($statistics_data['data']) > 0 ? max(array_column($statistics_data['data'], 'value')) : 0; ?>
            </div>
        </div>
    </div>

    <!-- معلومات سريعة -->
    <div class="stats-cards" style="margin-top: 20px;">
        <!-- بطاقة أحدث العملاء -->
        <div class="card">
            <div style="padding: 15px;">
                <h4 style="color: #d30909; margin-bottom: 10px; display: flex; justify-content: space-between;">
                    <span>أحدث العملاء</span>
                    <span style="font-size: 0.8rem; color: #666;">(<?php echo count($clients_stats['recent']); ?>)</span>
                </h4>
                <div style="max-height: 180px; overflow-y: auto;">
                    <?php if (!empty($clients_stats['recent'])): ?>
                    <?php foreach($clients_stats['recent'] as $client): ?>
                    <div style="padding: 8px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: bold; font-size: 0.9rem;"><?php echo htmlspecialchars($client['name']); ?></div>
                            <div style="font-size: 0.75rem; color: #666;"><?php echo $client['date']; ?></div>
                        </div>
                        <span class="status status-active" style="font-size: 0.7rem;">نشط</span>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div style="text-align: center; padding: 30px; color: #999;">
                        <i class="fas fa-users" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px;"></i><br>
                        لا توجد عملاء حديثين
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- بطاقة أحدث طلبات التواصل -->
        <div class="card">
            <div style="padding: 15px;">
                <h4 style="color: #d30909; margin-bottom: 10px; display: flex; justify-content: space-between;">
                    <span>أحدث طلبات التواصل</span>
                    <span style="font-size: 0.8rem; color: #666;">(<?php echo count($contacts_stats['recent']); ?>)</span>
                </h4>
                <div style="max-height: 180px; overflow-y: auto;">
                    <?php if (!empty($contacts_stats['recent'])): ?>
                    <?php foreach($contacts_stats['recent'] as $contact): ?>
                    <div style="padding: 8px 0; border-bottom: 1px solid #eee;">
                        <div style="font-weight: bold; font-size: 0.9rem;"><?php echo htmlspecialchars($contact['name']); ?></div>
                        <div style="font-size: 0.75rem; color: #666; direction: ltr; text-align: right;"><?php echo $contact['email']; ?></div>
                        <div style="font-size: 0.75rem; color: #999;"><?php echo $contact['date']; ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div style="text-align: center; padding: 30px; color: #999;">
                        <i class="fas fa-envelope" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px;"></i><br>
                        لا توجد طلبات تواصل حديثة
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- بطاقة أحدث الإحصائيات -->
        <div class="card">
            <div style="padding: 15px;">
                <h4 style="color: #d30909; margin-bottom: 10px; display: flex; justify-content: space-between;">
                    <span>أحدث الإحصائيات</span>
                    <span style="font-size: 0.8rem; color: #666;">(<?php echo count($statistics_data['recent']); ?>)</span>
                </h4>
                <div style="max-height: 180px; overflow-y: auto;">
                    <?php if (!empty($statistics_data['recent'])): ?>
                    <?php foreach($statistics_data['recent'] as $stat): ?>
                    <div style="padding: 8px 0; border-bottom: 1px solid #eee;">
                        <div style="font-weight: bold; font-size: 0.9rem;"><?php echo htmlspecialchars($stat['title']); ?></div>
                        <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                            <span style="color: #d30909; font-weight: bold;"><?php echo $stat['value']; ?></span>
                            <span style="font-size: 0.75rem; color: #666;"><?php echo $stat['date']; ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div style="text-align: center; padding: 30px; color: #999;">
                        <i class="fas fa-chart-bar" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px;"></i><br>
                        لا توجد إحصائيات حديثة
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- بطاقة إحصائيات سريعة -->
        <div class="card">
            <div style="padding: 15px;">
                <h4 style="color: #d30909; margin-bottom: 10px;">إحصائيات سريعة</h4>
                <div style="font-size: 0.9rem;">
                    <div style="margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                        <span style="color: #666;">نسبة نمو العملاء:</span>
                        <span style="float: left; color: #d30909; font-weight: bold;">
                            <?php 
                            $clients_growth = 0;
                            if (count($clients_stats['by_month']) >= 2) {
                                $first = reset($clients_stats['by_month'])['count'];
                                $last = end($clients_stats['by_month'])['count'];
                                if ($first > 0) {
                                    $clients_growth = (($last - $first) / $first) * 100;
                                }
                            }
                            echo round($clients_growth, 1) . '%';
                            ?>
                        </span>
                    </div>
                    <div style="margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                        <span style="color: #666;">متوسط طلبات التواصل/شهر:</span>
                        <span style="float: left; color: #d30909; font-weight: bold;">
                            <?php 
                            $avg_contacts = count($contacts_stats['by_month']) > 0 ? 
                                array_sum(array_column($contacts_stats['by_month'], 'count')) / count($contacts_stats['by_month']) : 0;
                            echo round($avg_contacts, 1);
                            ?>
                        </span>
                    </div>
                    <div style="margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                        <span style="color: #666;">معدل التحويل (تواصل → عميل):</span>
                        <span style="float: left; color: #d30909; font-weight: bold;">
                            <?php 
                            $conversion_rate = ($clients_stats['total'] > 0 && $contacts_stats['total'] > 0) ? 
                                ($clients_stats['total'] / $contacts_stats['total'] * 100) : 0;
                            echo round($conversion_rate, 1) . '%';
                            ?>
                        </span>
                    </div>
                    <div style="margin-bottom: 8px;">
                        <span style="color: #666;">نسبة الخدمات النشطة:</span>
                        <span style="float: left; color: #d30909; font-weight: bold;">
                            <?php 
                            $active_services_rate = ($services_stats['total'] > 0) ? 
                                ($services_stats['active'] / $services_stats['total'] * 100) : 0;
                            echo round($active_services_rate, 1) . '%';
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// تدرجات اللون الأحمر
const redPalette = [
    '#d30909', // أحمر داكن
    '#ff3838', // أحمر فاتح
    '#ff6b6b', // أحمر وردي
    '#ff9f9f', // أحمر فاتح جداً
    '#ff5252', // أحمر متوسط
    '#c62828', // أحمر داكن
    '#ef5350', // أحمر زاهي
    '#ff8a80'  // أحمر فاتح
];

// تخزين المخططات
let charts = {};

// تهيئة الرسوم البيانية عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    console.log('جاري تهيئة المخططات...');
    
    // 1. مخطط توزيع الخدمات
    initServicesChart();
    
    // 2. مخطط الإحصائيات
    initStatisticsChart();
    
    // 3. مخطط طلبات التواصل
    initMessagesChart();
    
    // 4. مخطط العملاء
    initClientsChart();
    
    // 5. مخطط المستخدمين
    initUsersChart();
    
    // 6. مخطط حالة الخدمات
    initServicesStatusChart();
    
    // 7. مخطط المقارنة
    initComparisonChart();
    
    // 8. مخطط توزيع الإحصائيات
    initStatisticsDistributionChart();
    
    console.log('تم تهيئة جميع المخططات بنجاح');
});

// 1. مخطط توزيع الخدمات حسب النوع
function initServicesChart() {
    const ctx = document.getElementById('servicesChart').getContext('2d');
    
    // البيانات من PHP
    const servicesData = <?php echo json_encode($services_stats['by_type'], JSON_UNESCAPED_UNICODE); ?>;
    
    let labels = Object.keys(servicesData);
    let data = Object.values(servicesData);
    
    if (labels.length === 0) {
        labels = ['أنظمة ERP', 'تطبيقات الجوال', 'تطوير الويب', 'حلول مخصصة'];
        data = [2, 1, 1, 0];
    }
    
    charts.services = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: redPalette.slice(0, labels.length),
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: true,
                    labels: {
                        padding: 15,
                        font: {
                            family: 'Segoe UI',
                            size: 11
                        }
                    }
                },
                tooltip: {
                    rtl: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${value} خدمة (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// 2. مخطط الإحصائيات
function initStatisticsChart() {
    const ctx = document.getElementById('statisticsChart').getContext('2d');
    
    // البيانات من PHP
    const statisticsData = <?php echo json_encode($statistics_data['data'], JSON_UNESCAPED_UNICODE); ?>;
    
    let labels = [];
    let data = [];
    
    if (statisticsData.length > 0) {
        labels = statisticsData.map(item => {
            // تقصير العنوان إذا كان طويلاً
            return item.title.length > 15 ? item.title.substring(0, 15) + '...' : item.title;
        });
        data = statisticsData.map(item => item.value);
    } else {
        // بيانات افتراضية من جدول statistics
        labels = ['سنوات الخبرة', 'المشاريع', 'رضا العملاء'];
        data = [19, 100, 90];
    }
    
    console.log('بيانات الإحصائيات:', { labels, data });
    
    charts.statistics = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'القيمة',
                data: data,
                backgroundColor: redPalette[0],
                borderColor: redPalette[1],
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    rtl: true,
                    callbacks: {
                        label: function(context) {
                            const value = context.raw || 0;
                            const title = statisticsData[context.dataIndex]?.title || labels[context.dataIndex];
                            return `${title}: ${value}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// 3. مخطط طلبات التواصل الشهري
function initMessagesChart() {
    const ctx = document.getElementById('messagesChart').getContext('2d');
    
    // البيانات من PHP
    const messagesData = <?php echo json_encode($contacts_stats['by_month'], JSON_UNESCAPED_UNICODE); ?>;
    
    let labels = [];
    let data = [];
    
    if (messagesData.length > 0) {
        labels = messagesData.map(item => item.month_name_ar || item.month_name);
        data = messagesData.map(item => item.count);
    } else {
        labels = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
        data = [0, 0, 0, 0, 0, 0];
    }
    
    charts.messages = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'طلبات التواصل',
                data: data,
                borderColor: redPalette[0],
                backgroundColor: 'rgba(211, 9, 9, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: redPalette[0],
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    rtl: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// 4. مخطط العملاء الشهري
function initClientsChart() {
    const ctx = document.getElementById('clientsChart').getContext('2d');
    
    // البيانات من PHP
    const clientsData = <?php echo json_encode($clients_stats['by_month'], JSON_UNESCAPED_UNICODE); ?>;
    
    let labels = [];
    let data = [];
    
    if (clientsData.length > 0) {
        labels = clientsData.map(item => item.month_name_ar || item.month_name);
        data = clientsData.map(item => item.count);
    } else {
        labels = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
        data = [0, 0, 0, 0, 0, 0];
    }
    
    charts.clients = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'عدد العملاء',
                data: data,
                backgroundColor: redPalette[0],
                borderColor: redPalette[1],
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// 5. مخطط توزيع المستخدمين حسب الدور
function initUsersChart() {
    const ctx = document.getElementById('usersChart').getContext('2d');
    
    // البيانات من PHP
    const usersData = <?php echo json_encode($users_stats['by_role'], JSON_UNESCAPED_UNICODE); ?>;
    
    let labels = Object.keys(usersData);
    let data = Object.values(usersData);
    
    if (labels.length === 0) {
        labels = ['مدير', 'محرر', 'مشاهد'];
        data = [1, 0, 0];
    }
    
    charts.users = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [redPalette[0], redPalette[1], redPalette[2]],
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: true
                }
            }
        }
    });
}

// 6. مخطط حالة الخدمات (خطي)
function initServicesStatusChart() {
    const ctx = document.getElementById('servicesStatusChart').getContext('2d');
    
    const active = <?php echo $services_stats['active']; ?>;
    const inactive = <?php echo $services_stats['inactive']; ?>;
    
    charts.servicesStatus = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['الخدمات النشطة', 'الخدمات غير النشطة'],
            datasets: [{
                label: 'عدد الخدمات',
                data: [active, inactive],
                borderColor: redPalette[0],
                backgroundColor: 'rgba(211, 9, 9, 0.1)',
                borderWidth: 3,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: redPalette[0],
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// 7. مخطط المقارنة بين العملاء والتواصل
function initComparisonChart() {
    const ctx = document.getElementById('comparisonChart').getContext('2d');
    
    const clientsData = <?php echo json_encode($clients_stats['by_month'], JSON_UNESCAPED_UNICODE); ?>;
    const messagesData = <?php echo json_encode($contacts_stats['by_month'], JSON_UNESCAPED_UNICODE); ?>;
    
    let labels = [];
    let clients = [];
    let messages = [];
    
    const commonMonths = 6;
    
    if (clientsData.length > 0) {
        const recentClients = clientsData.slice(-commonMonths);
        labels = recentClients.map(item => item.month_name_ar || item.month_name);
        clients = recentClients.map(item => item.count);
    } else {
        labels = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
        clients = [0, 0, 0, 0, 0, 0];
    }
    
    if (messagesData.length > 0) {
        const recentMessages = messagesData.slice(-commonMonths);
        messages = recentMessages.map(item => item.count);
        if (messages.length < labels.length) {
            messages = messages.concat(new Array(labels.length - messages.length).fill(0));
        }
    } else {
        messages = [0, 0, 0, 0, 0, 0];
    }
    
    charts.comparison = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'العملاء',
                    data: clients,
                    backgroundColor: redPalette[0],
                    borderColor: redPalette[0],
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'طلبات التواصل',
                    data: messages,
                    backgroundColor: redPalette[1],
                    borderColor: redPalette[1],
                    borderWidth: 1,
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    rtl: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// 8. مخطط توزيع الإحصائيات حسب القيمة
function initStatisticsDistributionChart() {
    const ctx = document.getElementById('statisticsDistributionChart').getContext('2d');
    
    const statisticsData = <?php echo json_encode($statistics_data['data'], JSON_UNESCAPED_UNICODE); ?>;
    
    // ترتيب الإحصائيات تنازلياً حسب القيمة
    const sortedData = [...statisticsData].sort((a, b) => b.value - a.value);
    
    let labels = [];
    let data = [];
    
    if (sortedData.length > 0) {
        labels = sortedData.map(item => {
            return item.title.length > 12 ? item.title.substring(0, 12) + '...' : item.title;
        });
        data = sortedData.map(item => item.value);
    } else {
        labels = ['سنوات الخبرة', 'المشاريع', 'رضا العملاء'];
        data = [19, 100, 90];
    }
    
    charts.statisticsDistribution = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'القيمة',
                data: data,
                backgroundColor: redPalette,
                borderColor: redPalette.map(color => color.replace('0.7', '1')),
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 20
                    }
                }
            }
        }
    });
}

// دوال التحديث
function updateChartType(chartId, type) {
    if (charts[chartId]) {
        charts[chartId].config.type = type;
        charts[chartId].update();
        showAlert(`تم تغيير نوع المخطط إلى: ${type === 'pie' ? 'كعكي' : 'دائري'}`, 'success');
    }
}

function updateStatisticsChart(type) {
    if (charts.statistics) {
        charts.statistics.config.type = type;
        charts.statistics.update();
        showAlert(`تم تغيير نوع مخطط الإحصائيات إلى: ${type === 'bar' ? 'أعمدة' : 'خطي'}`, 'success');
    }
}

function updateServicesStatusChart(type) {
    if (charts.servicesStatus) {
        charts.servicesStatus.config.type = type;
        charts.servicesStatus.update();
        showAlert(`تم تغيير نوع مخطط حالة الخدمات إلى: ${type === 'line' ? 'خطي' : 'أعمدة'}`, 'success');
    }
}

function updateComparisonChart(type) {
    if (charts.comparison) {
        charts.comparison.config.type = type;
        charts.comparison.update();
        showAlert(`تم تغيير نوع مخطط المقارنة إلى: ${type === 'bar' ? 'أعمدة' : 'خطي'}`, 'success');
    }
}

function updateChartData(type, period) {
    showAlert(`تم تحديث بيانات ${type === 'messages' ? 'طلبات التواصل' : 'العملاء'} للفترة: ${period === '6months' ? 'آخر 6 أشهر' : 'هذه السنة'}`, 'success');
}

// دالة لعرض التنبيهات
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'error'}`;
    alertDiv.textContent = message;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.padding = '15px';
    alertDiv.style.borderRadius = '5px';
    alertDiv.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
    alertDiv.style.minWidth = '300px';

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transition = 'opacity 0.5s';
        setTimeout(() => {
            alertDiv.remove();
        }, 500);
    }, 3000);
}
</script>
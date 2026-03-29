<?php
// pages/quote_requests.php

// استيراد ملفات التكوين
require_once '../includes/config.php';
require_once '../includes/database1.php';

// إنشاء اتصال بقاعدة البيانات
$database = new DatabaseConnection();
$conn = $database->getConnection();

// وظائف للتعامل مع طلبات عرض السعر
function getQuoteRequests($conn, $filters = []) {
    
    $where = ['1=1'];
    $params = [];
    
    // فلترة حسب الحالة
    if (!empty($filters['status'])) {
        $where[] = "status = ?";
        $params[] = $filters['status'];
    }
    
    // فلترة حسب الخدمة
    if (!empty($filters['service_type'])) {
        $where[] = "service_type = ?";
        $params[] = $filters['service_type'];
    }
    
    // فلترة حسب التاريخ
    if (!empty($filters['date_from'])) {
        $where[] = "DATE(created_at) >= ?";
        $params[] = $filters['date_from'];
    }
    
    if (!empty($filters['date_to'])) {
        $where[] = "DATE(created_at) <= ?";
        $params[] = $filters['date_to'];
    }
    
    // فلترة حسب الكلمة المفتاحية
    if (!empty($filters['search'])) {
        $where[] = "(reference LIKE ? OR full_name LIKE ? OR email LIKE ? OR company_name LIKE ?)";
        $searchTerm = "%" . $filters['search'] . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $whereClause = implode(' AND ', $where);
    
    // جلب إحصائيات الطلبات
    $statsQuery = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as reviewed,
        SUM(CASE WHEN status = 'quoted' THEN 1 ELSE 0 END) as quoted,
        SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM quote_requests WHERE $whereClause";
    
    $stats = [];
    
    try {
        $stmt = $conn->prepare($statsQuery);
        if ($params) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("خطأ في جلب إحصائيات طلبات عرض السعر: " . $e->getMessage());
    }
    
    // جلب الطلبات
    $orderBy = !empty($filters['order_by']) ? $filters['order_by'] : 'created_at DESC';
    $limit = !empty($filters['limit']) ? (int)$filters['limit'] : 100;
    $offset = !empty($filters['offset']) ? (int)$filters['offset'] : 0;
    
    $query = "SELECT * FROM quote_requests WHERE $whereClause ORDER BY $orderBy LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $requests = [];
    
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row = processQuoteRequestRow($row);
            $requests[] = $row;
        }
    } catch (Exception $e) {
        error_log("خطأ في جلب طلبات عرض السعر: " . $e->getMessage());
    }
    
    return [
        'stats' => $stats ?: [],
        'requests' => $requests,
        'total' => $stats['total'] ?? 0
    ];
}

// وظيفة لمعالجة صف طلب عرض السعر
function processQuoteRequestRow($row) {
    // تحويل المرفقات من JSON
    $row['attachments_array'] = json_decode($row['attachments'] ?? '[]', true);
    $row['features_array'] = !empty($row['features']) ? explode(',', $row['features']) : [];
    
    // تحديد اسم الخدمة بالعربية
    $serviceNames = [
        'erp' => 'أنظمة ERP',
        'mobile' => 'تطبيقات الجوال',
        'web' => 'تطوير الويب',
        'custom' => 'حلول مخصصة'
    ];
    $row['service_name'] = $serviceNames[$row['service_type']] ?? $row['service_type'];
    
    // تحديد حالة الطلب بالعربية
    $statusNames = [
        'pending' => 'قيد الانتظار',
        'reviewed' => 'تمت المراجعة',
        'quoted' => 'تم عرض السعر',
        'accepted' => 'مقبول',
        'rejected' => 'مرفوض'
    ];
    $row['status_name'] = $statusNames[$row['status']] ?? $row['status'];
    
    // تحديد نطاق المشروع
    $scopeNames = [
        'small' => 'صغير',
        'medium' => 'متوسط',
        'large' => 'كبير',
        'not-sure' => 'غير محدد'
    ];
    $row['scope_name'] = $scopeNames[$row['project_scope']] ?? $row['project_scope'];
    
    return $row;
}

// وظيفة لتحديث حالة الطلب
function updateQuoteStatus($conn, $id, $status, $notes = null, $quoted_price = null) {
    
    $query = "UPDATE quote_requests SET status = ?, updated_at = NOW()";
    $params = [$status];
    
    if ($notes !== null && $notes !== '') {
        $query .= ", notes = CONCAT(IFNULL(notes, ''), '\n', ?)";
        $params[] = date('Y-m-d H:i') . ': ' . $notes;
    }
    
    if ($quoted_price !== null) {
        $query .= ", quoted_price = ?";
        $params[] = $quoted_price;
    }
    
    $query .= " WHERE id = ?";
    $params[] = $id;
    
    try {
        $stmt = $conn->prepare($query);
        $result = $stmt->execute($params);
        
        return $result;
    } catch (Exception $e) {
        error_log("خطأ في تحديث حالة طلب عرض السعر: " . $e->getMessage());
        return false;
    }
}

// دالة لجلب تفاصيل الطلب
function getQuoteDetails($conn, $id) {
    try {
        $query = "SELECT * FROM quote_requests WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return processQuoteRequestRow($row);
        }
        return null;
    } catch (Exception $e) {
        error_log("خطأ في جلب تفاصيل الطلب: " . $e->getMessage());
        return null;
    }
}

// دالة لإرسال البريد الإلكتروني (محاكاة للاختبار)
function sendQuoteEmail($conn, $id) {
    try {
        // جلب بيانات الطلب
        $query = "SELECT email, full_name, reference FROM quote_requests WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $quote = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($quote) {
            // محاكاة إرسال البريد (في التطبيق الحقيقي استخدم PHPMailer أو ما شابه)
            error_log("طلب إرسال بريد للطلب ID: $id - البريد: " . $quote['email']);
            
            // هنا يمكنك إضافة كود إرسال البريد الفعلي
            // return mail($quote['email'], 'Subject', 'Message');
            
            return true;
        }
        return false;
    } catch (Exception $e) {
        error_log("خطأ في إرسال البريد: " . $e->getMessage());
        return false;
    }
}

// تحديد الفلاتر
$filters = [
    'status' => $_GET['status'] ?? '',
    'service_type' => $_GET['service'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
    'search' => $_GET['search'] ?? '',
    'order_by' => $_GET['order_by'] ?? 'created_at DESC'
];

// جلب الطلبات مع تطبيق الفلاتر
$data = getQuoteRequests($conn, $filters);
$requests = $data['requests'];
$stats = $data['stats'];
$total = $data['total'];

// تهيئة رسائل الخطأ والنجاح
$success_message = '';
$error_message = '';

// معالجة الطلبات الواردة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'update_status') {
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';
        $notes = $_POST['notes'] ?? '';
        $quoted_price = !empty($_POST['quoted_price']) ? floatval($_POST['quoted_price']) : null;
        
        if ($id && $status) {
            if (updateQuoteStatus($conn, $id, $status, $notes, $quoted_price)) {
                $success_message = "تم تحديث حالة الطلب بنجاح";
                
                // إعادة تحميل البيانات بعد التحديث
                $data = getQuoteRequests($conn, $filters);
                $requests = $data['requests'];
                $stats = $data['stats'];
                $total = $data['total'];
            } else {
                $error_message = "حدث خطأ في تحديث حالة الطلب";
            }
        } else {
            $error_message = "بيانات غير مكتملة";
        }
    }
}
?>

<div id="quote_requests" class="content-page <?php echo (isset($current_page) && $current_page == 'quote_requests') ? 'active' : ''; ?>">
    <!-- رأس الصفحة -->
    <div class="page-header">
        <div class="header-left">
            <h2><i class="fas fa-file-invoice-dollar"></i> طلبات عرض السعر</h2>
            <p>إدارة وعرض جميع طلبات عروض الأسعار الواردة من الموقع</p>
        </div>
        <div class="header-right">
            <div class="header-stats">
                <div class="stat-item stat-total">
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total'] ?? 0; ?></h3>
                        <p>إجمالي الطلبات</p>
                    </div>
                </div>
                
                <div class="stat-item stat-pending">
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['pending'] ?? 0; ?></h3>
                        <p>قيد الانتظار</p>
                    </div>
                </div>
                
                <div class="stat-item stat-quoted">
                    <div class="stat-icon bg-success">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['quoted'] ?? 0; ?></h3>
                        <p>تم عرض السعر</p>
                    </div>
                </div>
                
                <div class="stat-item stat-accepted">
                    <div class="stat-icon bg-info">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['accepted'] ?? 0; ?></h3>
                        <p>مقبول</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إشعارات -->
    <div id="quoteAlerts">
        <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- أدوات الفلترة والبحث -->
    <div class="card filters-card">
        <div class="card-header">
            <h3><i class="fas fa-filter"></i> أدوات البحث والفلترة</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="" class="filters-form">
                <input type="hidden" name="page" value="quote_requests">
                
                <div class="filters-grid">
                    <div class="form-group">
                        <label><i class="fas fa-search"></i> بحث سريع</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>" 
                               placeholder="بحث بالرقم المرجعي، الاسم، البريد...">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> حالة الطلب</label>
                        <select name="status">
                            <option value="">جميع الحالات</option>
                            <option value="pending" <?php echo ($filters['status'] == 'pending') ? 'selected' : ''; ?>>قيد الانتظار</option>
                            <option value="reviewed" <?php echo ($filters['status'] == 'reviewed') ? 'selected' : ''; ?>>تمت المراجعة</option>
                            <option value="quoted" <?php echo ($filters['status'] == 'quoted') ? 'selected' : ''; ?>>تم عرض السعر</option>
                            <option value="accepted" <?php echo ($filters['status'] == 'accepted') ? 'selected' : ''; ?>>مقبول</option>
                            <option value="rejected" <?php echo ($filters['status'] == 'rejected') ? 'selected' : ''; ?>>مرفوض</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-cogs"></i> نوع الخدمة</label>
                        <select name="service">
                            <option value="">جميع الخدمات</option>
                            <option value="erp" <?php echo ($filters['service_type'] == 'erp') ? 'selected' : ''; ?>>أنظمة ERP</option>
                            <option value="mobile" <?php echo ($filters['service_type'] == 'mobile') ? 'selected' : ''; ?>>تطبيقات الجوال</option>
                            <option value="web" <?php echo ($filters['service_type'] == 'web') ? 'selected' : ''; ?>>تطوير الويب</option>
                            <option value="custom" <?php echo ($filters['service_type'] == 'custom') ? 'selected' : ''; ?>>حلول مخصصة</option>
                        </select>
                    </div>
                </div>
                
                <div class="filters-grid">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> من تاريخ</label>
                        <input type="date" name="date_from" value="<?php echo $filters['date_from']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> إلى تاريخ</label>
                        <input type="date" name="date_to" value="<?php echo $filters['date_to']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-sort"></i> ترتيب حسب</label>
                        <select name="order_by">
                            <option value="created_at DESC" <?php echo ($filters['order_by'] == 'created_at DESC') ? 'selected' : ''; ?>>أحدث الطلبات</option>
                            <option value="created_at ASC" <?php echo ($filters['order_by'] == 'created_at ASC') ? 'selected' : ''; ?>>أقدم الطلبات</option>
                            <option value="full_name ASC" <?php echo ($filters['order_by'] == 'full_name ASC') ? 'selected' : ''; ?>>الاسم (أ-ي)</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> تطبيق الفلتر
                    </button>
                    <a href="?page=quote_requests" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول الطلبات -->
    <div class="data-table">
        <div class="table-header">
            <div class="header-content">
                <h3 class="table-title"><i class="fas fa-table"></i> قائمة الطلبات (<?php echo $total; ?> طلب)</h3>
                <div class="table-summary">
                    <span class="summary-item">
                        <i class="fas fa-layer-group"></i> <?php echo $total; ?> طلب
                    </span>
                    <span class="summary-item">
                        <i class="fas fa-filter"></i> <?php 
                        $activeFilters = array_filter($filters, function($value) {
                            return !empty($value) && $value != 'created_at DESC';
                        });
                        echo count($activeFilters); ?> فلتر
                    </span>
                </div>
            </div>
            <div class="table-actions">
                <button onclick="exportToExcel()" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </button>
                <button onclick="printTable()" class="btn btn-secondary btn-sm">
                    <i class="fas fa-print"></i> طباعة
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th width="120">الرقم المرجعي</th>
                        <th>العميل</th>
                        <th width="120">الخدمة</th>
                        <th width="100">النطاق</th>
                        <th width="120">الحالة</th>
                        <th width="120">التاريخ</th>
                        <th width="120">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            <div class="no-results-message">
                                <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 10px;"></i>
                                <h4>لا توجد طلبات لعرضها</h4>
                                <p>لم يتم العثور على طلبات تطابق معايير البحث الخاصة بك</p>
                                <a href="?page=quote_requests" class="btn btn-primary">
                                    <i class="fas fa-redo"></i> عرض جميع الطلبات
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach($requests as $index => $request): ?>
                    <tr>
                        <td>
                            <div class="row-number"><?php echo $index + 1; ?></div>
                        </td>
                        <td>
                            <div class="reference-code">
                                <i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($request['reference']); ?>
                            </div>
                            <?php if ($request['quoted_price']): ?>
                            <div class="price-tag">
                                <i class="fas fa-money-bill-wave"></i> <?php echo number_format($request['quoted_price'], 2); ?> ر.س
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="client-info">
                                <div class="client-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="client-details">
                                    <div class="client-name"><?php echo htmlspecialchars($request['full_name']); ?></div>
                                    <div class="client-meta">
                                        <span class="client-email">
                                            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($request['email']); ?>
                                        </span>
                                        <?php if ($request['phone']): ?>
                                        <span class="client-phone">
                                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($request['phone']); ?>
                                        </span>
                                        <?php endif; ?>
                                        <?php if ($request['company_name']): ?>
                                        <span class="client-company">
                                            <i class="fas fa-building"></i> <?php echo htmlspecialchars($request['company_name']); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="service-cell">
                                <?php
                                $serviceIcons = [
                                    'erp' => 'fa-chart-line',
                                    'mobile' => 'fa-mobile-alt',
                                    'web' => 'fa-globe',
                                    'custom' => 'fa-cogs'
                                ];
                                $serviceIcon = $serviceIcons[$request['service_type']] ?? 'fa-cog';
                                ?>
                                <div class="service-icon">
                                    <i class="fas <?php echo $serviceIcon; ?>"></i>
                                </div>
                                <span class="service-label"><?php echo htmlspecialchars($request['service_name']); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="scope-cell">
                                <span class="scope-badge scope-<?php echo $request['project_scope']; ?>">
                                    <?php echo htmlspecialchars($request['scope_name']); ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="status-cell">
                                <?php
                                $statusIcons = [
                                    'pending' => 'fa-clock',
                                    'reviewed' => 'fa-eye',
                                    'quoted' => 'fa-file-invoice-dollar',
                                    'accepted' => 'fa-check-circle',
                                    'rejected' => 'fa-times-circle'
                                ];
                                $statusIcon = $statusIcons[$request['status']] ?? 'fa-circle';
                                ?>
                                <div class="status-indicator status-<?php echo $request['status']; ?>">
                                    <i class="fas <?php echo $statusIcon; ?>"></i>
                                </div>
                                <div class="status-details">
                                    <span class="status-label"><?php echo htmlspecialchars($request['status_name']); ?></span>
                                    <?php if ($request['updated_at'] != $request['created_at']): ?>
                                    <small class="status-time"><?php echo date('H:i', strtotime($request['updated_at'])); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="date-cell">
                                <div class="date-day"><?php echo date('Y-m-d', strtotime($request['created_at'])); ?></div>
                                <div class="date-time"><?php echo date('H:i', strtotime($request['created_at'])); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <!-- زر العرض -->
                                <button onclick="viewQuoteDetails(<?php echo $request['id']; ?>)" 
                                        class="btn btn-sm btn-view" title="عرض تفاصيل الطلب">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <!-- زر تحديث الحالة -->
                                <button onclick="editQuoteStatus(<?php echo $request['id']; ?>)" 
                                        class="btn btn-sm btn-edit" title="تحديث حالة الطلب">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <!-- زر إرسال البريد الإلكتروني -->
                                <button onclick="sendQuoteEmail(<?php echo $request['id']; ?>)" 
                                        class="btn btn-sm btn-reply" title="إرسال بريد إلكتروني">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- صفحات -->
        <?php if ($total > 100): ?>
        <div class="pagination">
            <div class="pagination-info">
                عرض <strong>1-100</strong> من <strong><?php echo $total; ?></strong> طلب
            </div>
            <div class="pagination-controls">
                <button class="btn btn-sm" disabled>
                    <i class="fas fa-chevron-right"></i>
                </button>
                <span class="current-page">1</span>
                <button class="btn btn-sm">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- نافذة عرض التفاصيل -->
<div id="quoteDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-file-alt"></i> تفاصيل طلب عرض السعر</h3>
            <button class="close-modal" onclick="closeModal('quoteDetailsModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="quoteDetailsContent">
            <!-- سيتم تحميل المحتوى هنا -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('quoteDetailsModal')">
                <i class="fas fa-times"></i> إغلاق
            </button>
        </div>
    </div>
</div>

<!-- نافذة تحديث الحالة -->
<div id="updateStatusModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-edit"></i> تحديث حالة الطلب</h3>
            <button class="close-modal" onclick="closeModal('updateStatusModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="updateStatusForm" method="POST" class="modal-form">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id" id="quote_id">
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> الحالة الجديدة</label>
                    <div class="status-options">
                        <label class="status-option">
                            <input type="radio" name="status" value="pending">
                            <div class="status-option-content">
                                <i class="fas fa-clock"></i>
                                <span>قيد الانتظار</span>
                            </div>
                        </label>
                        
                        <label class="status-option">
                            <input type="radio" name="status" value="reviewed">
                            <div class="status-option-content">
                                <i class="fas fa-eye"></i>
                                <span>تمت المراجعة</span>
                            </div>
                        </label>
                        
                        <label class="status-option">
                            <input type="radio" name="status" value="quoted">
                            <div class="status-option-content">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>تم عرض السعر</span>
                            </div>
                        </label>
                        
                        <label class="status-option">
                            <input type="radio" name="status" value="accepted">
                            <div class="status-option-content">
                                <i class="fas fa-check-circle"></i>
                                <span>مقبول</span>
                            </div>
                        </label>
                        
                        <label class="status-option">
                            <input type="radio" name="status" value="rejected">
                            <div class="status-option-content">
                                <i class="fas fa-times-circle"></i>
                                <span>مرفوض</span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-money-bill-wave"></i> السعر المقترح (اختياري)</label>
                    <div class="input-with-icon">
                        <i class="fas fa-riyal-sign"></i>
                        <input type="number" name="quoted_price" id="quoted_price" 
                               placeholder="أدخل السعر المقترح" step="0.01">
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-sticky-note"></i> ملاحظات</label>
                    <textarea name="notes" id="quote_notes" rows="4" 
                              placeholder="أدخل ملاحظات إضافية..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ التغييرات
                    </button>
                    <button type="button" class="btn btn-secondary" 
                            onclick="closeModal('updateStatusModal')">
                        <i class="fas fa-times"></i> إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* أنماط خاصة بصفحة طلبات عرض السعر */

/* رأس الصفحة */
.page-header {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 20px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-left h2 {
    color: var(--dark-color);
    font-size: 24px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.header-left h2 i {
    color: var(--primary-color);
}

.header-left p {
    color: var(--gray-color);
    font-size: 14px;
}

.header-right {
    display: flex;
    gap: 15px;
}

.header-stats {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.stat-item {
    background: white;
    border-radius: var(--border-radius);
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
    min-width: 180px;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
}

.stat-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-info h3 {
    font-size: 1.8rem;
    margin-bottom: 5px;
    color: var(--dark-color);
}

.stat-info p {
    color: var(--gray-color);
    font-size: 0.9rem;
}

/* بطاقة الفلترة */
.filters-card {
    margin-bottom: 20px;
}

.filters-card .card-header {
    background: linear-gradient(135deg, var(--primary-color), #a80707);
    color: white;
}

.filters-card .card-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.filters-card .form-group {
    margin-bottom: 0;
}

.filters-card .form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--dark-color);
    font-weight: 500;
}

.filters-card .form-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    padding-top: 15px;
    border-top: 1px solid var(--border);
}

/* الجدول */
.data-table {
    margin-top: 20px;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.table-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--dark-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.table-summary {
    display: flex;
    gap: 15px;
}

.summary-item {
    background: var(--light-color);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    color: var(--gray-color);
    display: flex;
    align-items: center;
    gap: 6px;
}

.table-actions {
    display: flex;
    gap: 10px;
}

/* خلايا الجدول */
.row-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: var(--light-color);
    border-radius: 50%;
    font-weight: 600;
    color: var(--gray-color);
}

.reference-code {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: var(--primary-color);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 5px;
}

.price-tag {
    background: rgba(255, 193, 7, 0.1);
    color: #ff9800;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.client-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.client-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--light-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 20px;
}

.client-details {
    flex: 1;
}

.client-name {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 4px;
    font-size: 14px;
}

.client-meta {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.client-meta span {
    font-size: 12px;
    color: var(--gray-color);
    display: flex;
    align-items: center;
    gap: 4px;
}

.service-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.service-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--light-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
}

.service-label {
    font-size: 14px;
    font-weight: 500;
    color: var(--dark-color);
}

.scope-cell {
    text-align: center;
}

.scope-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.scope-small {
    background: rgba(76, 175, 80, 0.1);
    color: #4caf50;
}

.scope-medium {
    background: rgba(255, 152, 0, 0.1);
    color: #ff9800;
}

.scope-large {
    background: rgba(156, 39, 176, 0.1);
    color: #9c27b0;
}

.scope-not-sure {
    background: rgba(158, 158, 158, 0.1);
    color: #9e9e9e;
}

.status-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-indicator {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.status-pending {
    background: #ff9800;
}

.status-reviewed {
    background: #2196f3;
}

.status-quoted {
    background: #4caf50;
}

.status-accepted {
    background: #2e7d32;
}

.status-rejected {
    background: #f44336;
}

.status-details {
    display: flex;
    flex-direction: column;
}

.status-label {
    font-weight: 500;
    font-size: 14px;
    color: var(--dark-color);
}

.status-time {
    font-size: 11px;
    color: var(--gray-color);
}

.date-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.date-day {
    font-weight: 500;
    font-size: 14px;
    color: var(--dark-color);
}

.date-time {
    font-size: 12px;
    color: var(--gray-color);
}

/* أزرار الإجراءات */
.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.btn-view {
    background: #2196f3;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
}

.btn-edit {
    background: #ff9800;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
}

.btn-reply {
    background: #9c27b0;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
}

.btn-view:hover {
    background: #1976d2;
}

.btn-edit:hover {
    background: #f57c00;
}

.btn-reply:hover {
    background: #7b1fa2;
}

/* الترقيم */
.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 1px solid var(--border);
    margin-top: 20px;
}

.pagination-info {
    font-size: 14px;
    color: var(--gray-color);
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.current-page {
    padding: 6px 12px;
    background: var(--primary-color);
    color: white;
    border-radius: 4px;
    font-weight: 500;
}

/* أنماط المودال */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    width: 90%;
    max-width: 700px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.modal-footer {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
    text-align: left;
}

.close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
}

.close-modal:hover {
    color: #d30909;
}

.modal-form .form-group {
    margin-bottom: 20px;
}

.status-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 10px;
    margin-top: 8px;
}

.status-option input[type="radio"] {
    display: none;
}

.status-option-content {
    padding: 12px;
    border: 2px solid #ddd;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}

.status-option input[type="radio"]:checked + .status-option-content {
    border-color: #d30909;
    background: rgba(211, 9, 9, 0.1);
}

.status-option-content i {
    font-size: 20px;
    color: #d30909;
}

.status-option-content span {
    font-size: 12px;
    font-weight: 500;
    color: #333;
}

.input-with-icon {
    position: relative;
}

.input-with-icon i {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.input-with-icon input {
    width: 100%;
    padding: 10px 40px 10px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.modal-form textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
}

.modal-form .form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}

/* رسالة لا توجد نتائج */
.no-results-message {
    text-align: center;
    padding: 40px 20px;
}

.no-results-message h4 {
    color: #666;
    margin-bottom: 10px;
}

.no-results-message p {
    color: #666;
    margin-bottom: 20px;
    font-size: 14px;
}

/* تأثيرات */
tr {
    transition: background-color 0.3s;
}

tr:hover {
    background: rgba(211, 9, 9, 0.05) !important;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-sm i {
    font-size: 14px;
}

/* استجابة للشاشات الصغيرة */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .header-stats {
        justify-content: center;
    }
    
    .stat-item {
        min-width: calc(50% - 10px);
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .table-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .header-content {
        flex-direction: column;
        gap: 10px;
    }
    
    .table-summary {
        justify-content: center;
    }
    
    .client-info {
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }
    
    .client-meta span {
        justify-content: center;
    }
    
    .status-options {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 5px;
    }
}

@media (max-width: 480px) {
    .header-stats {
        flex-direction: column;
    }
    
    .stat-item {
        min-width: 100%;
    }
    
    .action-buttons {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .action-buttons .btn {
        margin: 2px;
    }
}
</style>

<script>
// دوال JavaScript
function viewQuoteDetails(id) {
    // البحث عن الطلب في البيانات الحالية
    const requests = <?php echo json_encode($requests); ?>;
    const request = requests.find(r => r.id == id);
    
    const modalContent = document.getElementById('quoteDetailsContent');
    
    if (request) {
        displayQuoteDetails(request);
        document.getElementById('quoteDetailsModal').style.display = 'flex';
    } else {
        // إظهار رسالة خطأ
        modalContent.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-exclamation-triangle fa-2x" style="color: #f44336;"></i>
                <p>لم يتم العثور على تفاصيل الطلب</p>
                <p style="font-size: 14px; color: #666; margin-top: 10px;">
                    الطلب غير موجود أو تم حذفه
                </p>
                <button class="btn btn-secondary" onclick="closeModal('quoteDetailsModal')" style="margin-top: 20px;">
                    <i class="fas fa-times"></i> إغلاق
                </button>
            </div>
        `;
        document.getElementById('quoteDetailsModal').style.display = 'flex';
    }
}

function displayQuoteDetails(quote) {
    const modalContent = document.getElementById('quoteDetailsContent');
    
    // تنسيق البيانات للعرض
    const statusNames = {
        'pending': 'قيد الانتظار',
        'reviewed': 'تمت المراجعة',
        'quoted': 'تم عرض السعر',
        'accepted': 'مقبول',
        'rejected': 'مرفوض'
    };
    
    const serviceNames = {
        'erp': 'أنظمة ERP',
        'mobile': 'تطبيقات الجوال',
        'web': 'تطوير الويب',
        'custom': 'حلول مخصصة'
    };
    
    const scopeNames = {
        'small': 'صغير',
        'medium': 'متوسط',
        'large': 'كبير',
        'not-sure': 'غير محدد'
    };
    
    const html = `
        <div class="quote-details-container">
            <div class="detail-section">
                <h4><i class="fas fa-info-circle"></i> المعلومات الأساسية</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <strong>الرقم المرجعي:</strong>
                        <span>${quote.reference || 'غير محدد'}</span>
                    </div>
                    <div class="detail-item">
                        <strong>حالة الطلب:</strong>
                        <span class="status-badge status-${quote.status}">
                            ${statusNames[quote.status] || quote.status}
                        </span>
                    </div>
                    <div class="detail-item">
                        <strong>تاريخ الطلب:</strong>
                        <span>${quote.created_at || 'غير محدد'}</span>
                    </div>
                    ${quote.quoted_price ? `
                    <div class="detail-item">
                        <strong>السعر المقترح:</strong>
                        <span class="price-value">${quote.quoted_price} ر.س</span>
                    </div>` : ''}
                </div>
            </div>
            
            <div class="detail-section">
                <h4><i class="fas fa-user"></i> معلومات العميل</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <strong>الاسم الكامل:</strong>
                        <span>${quote.full_name || 'غير محدد'}</span>
                    </div>
                    ${quote.company_name ? `
                    <div class="detail-item">
                        <strong>اسم الشركة:</strong>
                        <span>${quote.company_name}</span>
                    </div>` : ''}
                    <div class="detail-item">
                        <strong>البريد الإلكتروني:</strong>
                        <span><a href="mailto:${quote.email}">${quote.email || 'غير محدد'}</a></span>
                    </div>
                    <div class="detail-item">
                        <strong>رقم الهاتف:</strong>
                        <span>${quote.phone || 'غير محدد'}</span>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h4><i class="fas fa-project-diagram"></i> تفاصيل المشروع</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <strong>الخدمة المطلوبة:</strong>
                        <span>${serviceNames[quote.service_type] || quote.service_type}</span>
                    </div>
                    <div class="detail-item">
                        <strong>نطاق المشروع:</strong>
                        <span>${scopeNames[quote.project_scope] || quote.project_scope}</span>
                    </div>
                    ${quote.budget_range ? `
                    <div class="detail-item">
                        <strong>الميزانية المتوقعة:</strong>
                        <span>${quote.budget_range}</span>
                    </div>` : ''}
                    ${quote.timeline ? `
                    <div class="detail-item">
                        <strong>الجدول الزمني:</strong>
                        <span>${quote.timeline}</span>
                    </div>` : ''}
                </div>
            </div>
            
            ${quote.project_description ? `
            <div class="detail-section">
                <h4><i class="fas fa-file-alt"></i> وصف المشروع</h4>
                <div class="description-box">
                    ${quote.project_description.replace(/\n/g, '<br>')}
                </div>
            </div>` : ''}
            
            ${quote.features_array && quote.features_array.length > 0 ? `
            <div class="detail-section">
                <h4><i class="fas fa-star"></i> المميزات المطلوبة</h4>
                <div class="features-grid">
                    ${quote.features_array.map(feature => {
                        const featureNames = {
                            'responsive-design': 'تصميم متجاوب',
                            'multi-language': 'دعم متعدد اللغات',
                            'payment-gateway': 'بوابة دفع إلكتروني',
                            'admin-panel': 'لوحة تحكم متقدمة',
                            'api-integration': 'تكامل مع API',
                            'technical-support': 'دعم فني لمدة سنة'
                        };
                        return `
                        <div class="feature-item">
                            <i class="fas fa-check" style="color: #4caf50;"></i>
                            <span>${featureNames[feature] || feature}</span>
                        </div>
                        `;
                    }).join('')}
                </div>
            </div>` : ''}
            
            ${quote.notes ? `
            <div class="detail-section">
                <h4><i class="fas fa-sticky-note"></i> ملاحظات</h4>
                <div class="notes-box">
                    ${quote.notes.replace(/\n/g, '<br>')}
                </div>
            </div>` : ''}
        </div>
        
        <style>
            .quote-details-container {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            
            .detail-section {
                margin-bottom: 25px;
                border-bottom: 1px solid #eee;
                padding-bottom: 15px;
            }
            
            .detail-section:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }
            
            .detail-section h4 {
                color: #2c3e50;
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .detail-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 15px;
            }
            
            .detail-item {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            
            .detail-item strong {
                color: #34495e;
                font-weight: 600;
                font-size: 14px;
            }
            
            .detail-item span {
                color: #2c3e50;
                font-size: 15px;
            }
            
            .status-badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 500;
            }
            
            .status-pending {
                background: rgba(255, 152, 0, 0.1);
                color: #f57c00;
            }
            
            .status-reviewed {
                background: rgba(33, 150, 243, 0.1);
                color: #1976d2;
            }
            
            .status-quoted {
                background: rgba(76, 175, 80, 0.1);
                color: #388e3c;
            }
            
            .status-accepted {
                background: rgba(46, 125, 50, 0.1);
                color: #2e7d32;
            }
            
            .status-rejected {
                background: rgba(244, 67, 54, 0.1);
                color: #d32f2f;
            }
            
            .price-value {
                color: #27ae60;
                font-weight: bold;
            }
            
            .description-box, .notes-box {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                line-height: 1.6;
            }
            
            .notes-box {
                background: #fff8e1;
                border-right: 4px solid #ffd54f;
            }
            
            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 10px;
            }
            
            .feature-item {
                display: flex;
                align-items: center;
                gap: 8px;
                background: #f8f9fa;
                padding: 10px;
                border-radius: 6px;
            }
        </style>
    `;
    
    modalContent.innerHTML = html;
}

function editQuoteStatus(id) {
    document.getElementById('quote_id').value = id;
    
    // البحث عن الطلب في البيانات الحالية
    const requests = <?php echo json_encode($requests); ?>;
    const request = requests.find(r => r.id == id);
    
    if (request) {
        // تعيين القيم في النموذج
        if (request.status) {
            const radio = document.querySelector(`input[name="status"][value="${request.status}"]`);
            if (radio) {
                radio.checked = true;
            }
        }
        if (request.quoted_price) {
            document.getElementById('quoted_price').value = request.quoted_price;
        }
        if (request.notes) {
            document.getElementById('quote_notes').value = request.notes;
        }
    }
    
    document.getElementById('updateStatusModal').style.display = 'flex';
}

function sendQuoteEmail(id) {
    // إظهار رسالة تأكيد
    if (confirm('هل تريد إرسال بريد إلكتروني للعميل؟')) {
        showAlert('جاري إرسال البريد الإلكتروني...', 'info');
        
        // البحث عن الطلب في البيانات الحالية
        const requests = <?php echo json_encode($requests); ?>;
        const request = requests.find(r => r.id == id);
        
        if (request && request.email) {
            // إنشاء رابط البريد
            const mailtoLink = `mailto:${request.email}?subject=طلب عرض سعر - ${request.reference}&body=عزيزي ${request.full_name}،%0D%0A%0D%0A`;
            
            // فتح بريد العميل
            window.open(mailtoLink, '_blank');
            
            showAlert('تم فتح بريد العميل. يمكنك الآن إرسال البريد.', 'success');
        } else {
            showAlert('لم يتم العثور على بريد العميل', 'error');
        }
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// إغلاق المودال عند النقر خارج المحتوى
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
};

// دالة لعرض التنبيهات
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}-circle"></i>
        ${message}
    `;
    
    const container = document.getElementById('quoteAlerts');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 500);
    }, 3000);
}

// معالجة إرسال نموذج تحديث الحالة
document.getElementById('updateStatusForm').addEventListener('submit', function(e) {
    // النموذج سيرسل البيانات مباشرة إلى الصفحة الحالية
    // الصفحة ستعالج البيانات وتعيد تحميل نفسها
});

/// دالة تصدير Excel
function exportToExcel() {
    // إظهار رسالة تحميل
    showAlert('جاري تجهيز ملف Excel للتصدير...', 'info');
    
    // 1. جمع معايير البحث الحالية
    let exportUrl = 'api/export_excel.php?';
    
    // الحصول على معاملات URL الحالية
    const urlParams = new URLSearchParams(window.location.search);
    
    // 2. إضافة معاملات البحث إلى رابط التصدير
    const params = {
        'status': urlParams.get('status') || '',
        'service': urlParams.get('service') || '',
        'date_from': urlParams.get('date_from') || '',
        'date_to': urlParams.get('date_to') || '',
        'search': urlParams.get('search') || '',
        'order_by': urlParams.get('order_by') || 'created_at DESC'
    };
    
    // 3. بناء query string
    const queryString = new URLSearchParams(params).toString();
    exportUrl += queryString;
    
    console.log('رابط التصدير:', exportUrl); // للتصحيح
    
    // 4. طريقة 1: فتح الرابط في نافذة جديدة (الأفضل)
    const downloadWindow = window.open(exportUrl, '_blank');
    
    // 5. طريقة 2: إنشاء رابط تحميل مخفي
    const downloadLink = document.createElement('a');
    downloadLink.href = exportUrl;
    downloadLink.target = '_blank';
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
    
    // 6. تحديث رسالة النجاح
    setTimeout(() => {
        if (downloadWindow && !downloadWindow.closed) {
            showAlert('تم بدء تحميل ملف Excel بنجاح ✓', 'success');
        } else {
            showAlert('تم إنشاء ملف التصدير، سيبدأ التحميل تلقائياً', 'success');
        }
    }, 1500);
}
function printTable() {
    const printContent = document.querySelector('.data-table').cloneNode(true);
    
    // إزالة أزرار الإجراءات عند الطباعة
    const actionButtons = printContent.querySelectorAll('.action-buttons');
    actionButtons.forEach(btn => btn.remove());
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>طلبات عرض السعر - طباعة</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif;
                        padding: 20px; 
                        direction: rtl;
                        color: #333;
                    }
                    h1 { 
                        text-align: center; 
                        color: #2c3e50; 
                        margin-bottom: 20px;
                        border-bottom: 2px solid #2c3e50;
                        padding-bottom: 10px;
                    }
                    .print-header {
                        text-align: center;
                        margin-bottom: 30px;
                        padding-bottom: 20px;
                        border-bottom: 2px solid #dee2e6;
                    }
                    .print-info {
                        text-align: center;
                        margin-bottom: 20px;
                        color: #666;
                        font-size: 14px;
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin-top: 20px;
                        font-size: 12px;
                    }
                    th { 
                        background-color: #f8f9fa; 
                        padding: 10px; 
                        border: 1px solid #dee2e6; 
                        text-align: right;
                        font-weight: 600;
                        color: #2c3e50;
                    }
                    td { 
                        padding: 8px; 
                        border: 1px solid #dee2e6; 
                        text-align: right;
                        vertical-align: top;
                    }
                    @media print {
                        body { padding: 10px; }
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h1>سراج سوفت - طلبات عرض السعر</h1>
                    <div class="print-info">
                        <p>تاريخ الطباعة: ${new Date().toLocaleDateString('ar-SA')}</p>
                        <p>إجمالي السجلات: <?php echo $total; ?></p>
                    </div>
                </div>
                ${printContent.innerHTML}
            </body>
        </html>
    `);
    printWindow.document.close();
    
    // الانتظار قليلاً قبل الطباعة لضمان تحميل الأنماط
    setTimeout(() => {
        printWindow.print();
        // إغلاق النافذة بعد الطباعة
        setTimeout(() => {
            printWindow.close();
        }, 1000);
    }, 500);
}

// إضافة تأثيرات عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.stat-item');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s, transform 0.5s';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
        row.style.transition = 'opacity 0.3s, transform 0.3s';
        
        setTimeout(() => {
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, 200 + index * 50);
    });
});
</script>
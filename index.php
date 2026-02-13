<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// تحديد الصفحة المطلوبة
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$current_page = $page;

// قائمة الصفحات المسموح بها
$allowed_pages = [
    'dashboard', 'statistics', 'services', 'clients', 
    'users', 'quote_requests', 'messages'
];

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
    $current_page = 'dashboard';
}

// التحقق من وجود جدول
function checkTableExists($table) {
    global $database;
    $result = $database->query("SHOW TABLES LIKE '$table'");
    return $result->num_rows > 0;
}

// التحقق من وجود أعمدة status قبل الاستعلام
function checkColumnExists($table, $column) {
    global $database;
    $result = $database->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}

// جرس الإشعارات مع معالجة الأخطاء
$unread_contacts = 0;
$total_services = 0;
$total_clients = 0;

try {
    // التحقق من وجود جدول contacts
    if (checkTableExists('contacts')) {
        // التحقق من وجود أعمدة status في جدول contacts
        if (checkColumnExists('contacts', 'status')) {
            $result = $database->query("SELECT COUNT(*) as count FROM contacts WHERE status = 'unread'");
            if ($result) {
                $unread_contacts = $result->fetch_assoc()['count'];
            }
        } else {
            // استعلام بديل إذا لم يوجد العمود status
            // التحقق أولاً من وجود عمود created_at
            if (checkColumnExists('contacts', 'created_at')) {
                $result = $database->query("SELECT COUNT(*) as count FROM contacts WHERE DATE(created_at) = CURDATE()");
                if ($result) {
                    $unread_contacts = $result->fetch_assoc()['count'];
                }
            } else {
                // إذا لم يوجد created_at، احسب كل الرسائل
                $result = $database->query("SELECT COUNT(*) as count FROM contacts");
                if ($result) {
                    $unread_contacts = $result->fetch_assoc()['count'];
                }
            }
        }
    } else {
        // الجدول غير موجود، إرجاع 0
        $unread_contacts = 0;
        error_log("Table 'contacts' does not exist");
    }
} catch (Exception $e) {
    // معالجة الأخطاء
    error_log("Error fetching unread contacts: " . $e->getMessage());
    $unread_contacts = 0;
}

// إذا كنت تريد الحصول على إحصائيات إضافية
try {
    // إجمالي الخدمات - استعلام مبسط أولاً للتأكد
    if (checkTableExists('services')) {
        // لنجرب استعلام بسيط أولاً
        $result = $database->query("SELECT COUNT(*) as count FROM services");
        if ($result) {
            $row = $result->fetch_assoc();
            $total_services = $row['count'];
            error_log("Services count from simple query: " . $total_services);
        } else {
            error_log("Services query failed");
        }
        
        // إذا كان 0، جرب استعلام آخر
        if ($total_services == 0) {
            if (checkColumnExists('services', 'status')) {
                $result = $database->query("SELECT COUNT(*) as count FROM services WHERE status = 'active'");
                if ($result) {
                    $row = $result->fetch_assoc();
                    $total_services = $row['count'];
                    error_log("Services count with status filter: " . $total_services);
                }
            }
        }
    } else {
        error_log("Table 'services' does not exist");
    }
} catch (Exception $e) {
    error_log("Error fetching services count: " . $e->getMessage());
}

try {
    // إجمالي العملاء
    if (checkTableExists('clients')) {
        // استعلام بسيط أولاً
        $result = $database->query("SELECT COUNT(*) as count FROM clients");
        if ($result) {
            $total_clients = $result->fetch_assoc()['count'];
        }
        
        // إذا كان 0، جرب استعلام آخر
        if ($total_clients == 0) {
            if (checkColumnExists('clients', 'status')) {
                $result = $database->query("SELECT COUNT(*) as count FROM clients WHERE status = 'active'");
                if ($result) {
                    $total_clients = $result->fetch_assoc()['count'];
                }
            }
        }
    } else {
        error_log("Table 'clients' does not exist");
    }
} catch (Exception $e) {
    error_log("Error fetching clients count: " . $e->getMessage());
}

// تصحيح للخطأ - إذا كان لا يزال 0، قم بتعيين قيمة افتراضية
if ($total_services == 0) {
    $total_services = 0; // يمكنك وضع قيمة افتراضية هنا
}

if ($total_clients == 0) {
    $total_clients = 0; // يمكنك وضع قيمة افتراضية هنا
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم سراج سوفت - <?php echo $current_page; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .user-profile:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .user-profile span {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }
        
        .user-profile i {
            font-size: 16px;
            color: #d30909;
        }
        
        .user-role {
            font-size: 12px;
            color: #666;
            margin-right: 5px;
        }
        
        /* أنماط إضافية لزر التواصل في الشريط العلوي */
        .contacts-notification {
            position: relative;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #333;
            text-decoration: none;
            background: transparent;
            border: none;
            font-family: inherit;
            font-size: 14px;
        }
        
        .contacts-notification:hover {
            background: rgba(211, 9, 9, 0.1);
            color: #d30909;
        }
        
        .contacts-notification i {
            font-size: 18px;
        }
        
        .contacts-notification .notification-badge {
            background: #d30909;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        .contacts-notification span.text {
            font-size: 14px;
            font-weight: 500;
        }
        
        /* تمييز الزر النشط */
        .contacts-notification.active {
            background: rgba(211, 9, 9, 0.1);
            color: #d30909;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- الشريط الجانبي -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="assets/img/icon.png" alt="سراج سوفت" onerror="this.src='https://via.placeholder.com/50'">
                <h2>سراج سوفت</h2>
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li><a href="index.php?page=dashboard" class="<?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>" data-page="dashboard"><i class="fas fa-home"></i> <span>لوحة التحكم</span></a></li>
                    <li><a href="index.php?page=statistics" class="<?php echo ($current_page == 'statistics') ? 'active' : ''; ?>" data-page="statistics"><i class="fas fa-chart-bar"></i> <span>إدارة الإحصائيات</span></a></li>
                    <li><a href="index.php?page=services" class="<?php echo ($current_page == 'services') ? 'active' : ''; ?>" data-page="services"><i class="fas fa-building"></i> <span>إدارة الخدمات</span></a></li>
                    <li><a href="index.php?page=clients" class="<?php echo ($current_page == 'clients') ? 'active' : ''; ?>" data-page="clients"><i class="fas fa-users"></i> <span>العملاء</span></a></li>
                    <li><a href="index.php?page=quote_requests" class="<?php echo ($current_page == 'quote_requests') ? 'active' : ''; ?>" data-page="quote_requests"><i class="fas fa-file-invoice-dollar"></i> <span>طلبات عرض السعر</span></a></li>
                    <li><a href="index.php?page=users" class="<?php echo ($current_page == 'users') ? 'active' : ''; ?>" data-page="users"><i class="fas fa-user-cog"></i> <span>المستخدمون</span></a></li>
                    <li><a href="logout.php" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> <span>تسجيل الخروج</span></a></li>
                </ul>
            </nav>
        </aside>

        <!-- المحتوى الرئيسي -->
        <main class="main-content">
            <!-- الشريط العلوي -->
            <div class="topbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="بحث..." id="searchInput">
                </div>
                <div class="user-area">
                    <!-- زر التواصل في الشريط العلوي -->
                    <button class="contacts-notification <?php echo ($current_page == 'messages') ? 'active' : ''; ?>" id="messagesBtn">
                        <i class="fas fa-envelope"></i>
                        <span class="notification-badge"><?php echo $unread_contacts; ?></span>
                        <span class="text">رسائل التواصل</span>
                    </button>
                    
                    <div class="notification" title="الخدمات النشطة">
                        <i class="fas fa-building"></i>
                        <span class="notification-badge"><?php echo $total_services; ?></span>
                    </div>
                    <div class="notification" title="العملاء النشطين">
                        <i class="fas fa-users"></i>
                        <span class="notification-badge"><?php echo $total_clients; ?></span>
                    </div>
                    <div class="user-profile">
                        <i class="fas fa-user-circle"></i>
                        <span>
                            <?php echo htmlspecialchars($_SESSION['username'] ?? 'مستخدم'); ?>
                            <span class="user-role">(<?php echo $_SESSION['role'] ?? 'مستخدم'; ?>)</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- المحتوى الديناميكي -->
            <div id="content-area">
                <?php
                $page_file = "pages/{$page}.php";
                if (file_exists($page_file)) {
                    // تمرير متغير database للصفحة
                    include $page_file;
                } else {
                    echo "<div class='alert alert-error'>الصفحة غير موجودة</div>";
                    include 'pages/dashboard.php';
                }
                ?>
            </div>
        </main>
    </div>

    <!-- نافذة منبثقة لعرض رسالة -->
    <div class="modal" id="messageModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">عرض الرسالة</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body" id="messageModalBody">
                <!-- محتوى الرسالة سيتم إضافته هنا -->
            </div>
        </div>
    </div>

    <!-- نافذة منبثقة للرد على رسالة -->
    <div class="modal" id="replyModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">الرد على الرسالة</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="message-details" id="replyMessageDetails">
                    <!-- تفاصيل الرسالة الأصلية -->
                </div>
                <div class="reply-section">
                    <h4>الرد</h4>
                    <div class="form-group">
                        <textarea id="replyContent" placeholder="اكتب ردك هنا..." rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn" id="sendReplyBtn">إرسال الرد</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- نافذة منبثقة لإضافة/تعديل خدمة -->
    <div class="modal" id="serviceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="serviceModalTitle">إضافة خدمة جديدة</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="serviceForm" enctype="multipart/form-data">
                    <input type="hidden" id="serviceId" name="id">
                    <div class="form-group">
                        <label for="serviceName">اسم الخدمة *</label>
                        <input type="text" id="serviceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="serviceDescription">وصف الخدمة *</label>
                        <textarea id="serviceDescription" name="description" required rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="serviceCategory">الفئة *</label>
                        <select id="serviceCategory" name="category" required>
                            <option value="">اختر الفئة</option>
                            <option value="أنظمة إدارة">أنظمة إدارة</option>
                            <option value="تطبيقات">تطبيقات</option>
                            <option value="مواقع">مواقع</option>
                            <option value="مخصص">مخصص</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="serviceIcon">أيقونة الخدمة *</label>
                        <select id="serviceIcon" name="icon" required>
                            <option value="">اختر الأيقونة</option>
                            <option value="fas fa-building">مبنى</option>
                            <option value="fas fa-mobile-alt">جوال</option>
                            <option value="fas fa-laptop-code">كمبيوتر</option>
                            <option value="fas fa-chart-line">رسم بياني</option>
                            <option value="fas fa-database">قاعدة بيانات</option>
                            <option value="fas fa-cloud">سحابة</option>
                            <option value="fas fa-shopping-cart">عربة تسوق</option>
                            <option value="fas fa-cogs">تروس</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="serviceOrder">ترتيب الخدمة *</label>
                        <input type="number" id="serviceOrder" name="order" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label for="serviceStatus">حالة الخدمة *</label>
                        <select id="serviceStatus" name="status" required>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn">حفظ الخدمة</button>
                        <button type="button" class="btn btn-delete close-modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- نافذة منبثقة لإضافة/تعديل إحصائية -->
    <div class="modal" id="statModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="statModalTitle">إضافة إحصائية جديدة</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="statForm">
                    <input type="hidden" id="statId" name="id">
                    <div class="form-group">
                        <label for="statTitle">عنوان الإحصائية *</label>
                        <input type="text" id="statTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="statValue">قيمة الإحصائية *</label>
                        <input type="text" id="statValue" name="value" required>
                    </div>
                    <div class="form-group">
                        <label for="statIcon">أيقونة الإحصائية *</label>
                        <select id="statIcon" name="icon" required>
                            <option value="">اختر الأيقونة</option>
                            <option value="fas fa-calendar-alt">تقويم</option>
                            <option value="fas fa-project-diagram">مشروع</option>
                            <option value="fas fa-smile">وجه مبتسم</option>
                            <option value="fas fa-users">مستخدمين</option>
                            <option value="fas fa-code">كود</option>
                            <option value="fas fa-rocket">صاروخ</option>
                            <option value="fas fa-award">جائزة</option>
                            <option value="fas fa-gem">ألماسة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="statColor">لون الإحصائية *</label>
                        <select id="statColor" name="color" required>
                            <option value="red">أحمر</option>
                            <option value="blue">أزرق</option>
                            <option value="green">أخضر</option>
                            <option value="orange">برتقالي</option>
                            <option value="purple">بنفسجي</option>
                            <option value="pink">وردي</option>
                            <option value="teal">تركواز</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="statStatus">حالة الإحصائية *</label>
                        <select id="statStatus" name="status" required>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn">حفظ الإحصائية</button>
                        <button type="button" class="btn btn-delete close-modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- نافذة منبثقة لإضافة/تعديل عميل -->
    <div class="modal" id="clientModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="clientModalTitle">إضافة عميل جديد</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="clientForm" enctype="multipart/form-data">
                    <input type="hidden" id="clientId" name="id">
                    <div class="form-group">
                        <label for="clientName">اسم العميل *</label>
                        <input type="text" id="clientName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="clientDescription">وصف العميل *</label>
                        <textarea id="clientDescription" name="description" required rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="clientLocation">موقع العميل *</label>
                        <input type="text" id="clientLocation" name="location" required>
                    </div>
                    <div class="form-group">
                        <label for="clientLogo">شعار العميل</label>
                        <div class="file-upload">
                            <input type="file" id="clientLogo" name="logo" accept="image/*">
                            <label for="clientLogo" class="file-upload-label">
                                <i class="fas fa-upload"></i> اختر صورة الشعار
                            </label>
                            <div class="file-name" id="clientLogoName">لم يتم اختيار ملف</div>
                        </div>
                        <div class="image-preview" id="clientLogoPreview"></div>
                    </div>
                    <div class="form-group">
                        <label for="clientOrder">ترتيب العميل *</label>
                        <input type="number" id="clientOrder" name="order" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label for="clientStatus">حالة العميل *</label>
                        <select id="clientStatus" name="status" required>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn">حفظ العميل</button>
                        <button type="button" class="btn btn-delete close-modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- نافذة منبثقة لإضافة/تعديل مستخدم -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="userModalTitle">إضافة مستخدم جديد</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="id">
                    <div class="form-group">
                        <label for="userUsername">اسم المستخدم *</label>
                        <input type="text" id="userUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="userEmail">البريد الإلكتروني *</label>
                        <input type="email" id="userEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="userPassword">كلمة المرور</label>
                        <input type="password" id="userPassword" name="password">
                        <small class="text-muted">اتركه فارغاً إذا كنت لا تريد تغيير كلمة المرور</small>
                    </div>
                    <div class="form-group">
                        <label for="userRole">الدور *</label>
                        <select id="userRole" name="role" required>
                            <option value="admin">مدير</option>
                            <option value="editor">محرر</option>
                            <option value="viewer">مشاهد</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="userStatus">حالة المستخدم *</label>
                        <select id="userStatus" name="status" required>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn">حفظ المستخدم</button>
                        <button type="button" class="btn btn-delete close-modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- نافذة منبثقة لتأكيد الحذف -->
    <div class="modal" id="deleteConfirmModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">تأكيد الحذف</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p id="deleteConfirmMessage">هل أنت متأكد من أنك تريد حذف هذا العنصر؟</p>
                <div class="form-group" style="text-align: left; margin-top: 20px;">
                    <button class="btn btn-delete" id="confirmDeleteBtn">نعم، احذف</button>
                    <button class="btn" id="cancelDeleteBtn">إلغاء</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/search.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script>
        // متغيرات عامة
        let currentDeleteItem = null;
        let currentDeleteType = null;
        
        // تهيئة التطبيق
        document.addEventListener('DOMContentLoaded', function() {
            // حدث زر التواصل في الشريط العلوي
            const messagesBtn = document.getElementById('messagesBtn');
            if (messagesBtn) {
                messagesBtn.addEventListener('click', function() {
                    window.location.href = 'index.php?page=messages';
                });
            }
            
            // تهيئة البحث
            initSearch();
            
            // تهيئة الرسوم البيانية
            initCharts();
            
            // أحداث النوافذ المنبثقة
            initModals();
            
            // تهيئة النماذج
            initForms();
            
            // تهيئة الجداول
            initTables();
        });
        
        // تهيئة البحث
        function initSearch() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    performSearch(searchTerm);
                });
            }
        }
        
        // تنفيذ البحث
        function performSearch(term) {
            const activePage = document.querySelector('#content-area');
            if (!activePage || term.trim() === '') {
                activePage.querySelectorAll('table tr').forEach(row => {
                    row.style.display = '';
                });
                return;
            }
            
            let found = false;
            
            // البحث في الجداول
            activePage.querySelectorAll('table').forEach(table => {
                const rows = table.querySelectorAll('tbody tr');
                let hasResults = false;
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(term)) {
                        row.style.display = '';
                        hasResults = true;
                        found = true;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // إظهار/إخفاء رأس الجدول
                const thead = table.querySelector('thead');
                if (thead) {
                    thead.style.display = hasResults ? '' : 'none';
                }
            });
            
            // عرض رسالة إذا لم توجد نتائج
            let message = activePage.querySelector('.no-results-message');
            if (!found && term.trim() !== '') {
                if (!message) {
                    message = document.createElement('div');
                    message.className = 'no-results-message';
                    message.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #6c757d;">
                            <i class="fas fa-search" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;"></i>
                            <h3 style="margin-bottom: 10px;">لا توجد نتائج</h3>
                            <p>لم نتمكن من العثور على أي نتائج تطابق "<strong>${term}</strong>"</p>
                        </div>
                    `;
                    activePage.appendChild(message);
                }
            } else if (message) {
                message.remove();
            }
        }
        
        // تهيئة الرسوم البيانية
        function initCharts() {
            if (document.getElementById('visitsChart')) {
                const visitsCtx = document.getElementById('visitsChart').getContext('2d');
                new Chart(visitsCtx, {
                    type: 'line',
                    data: {
                        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                        datasets: [{
                            label: 'عدد الزيارات',
                            data: [1200, 1900, 1500, 2200, 1800, 2500],
                            borderColor: '#d30909',
                            backgroundColor: 'rgba(211, 9, 9, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
            
            if (document.getElementById('servicesChart')) {
                const servicesCtx = document.getElementById('servicesChart').getContext('2d');
                new Chart(servicesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['أنظمة ERP', 'تطبيقات الجوال', 'تطوير الويب', 'حلول مخصصة'],
                        datasets: [{
                            data: [35, 25, 20, 20],
                            backgroundColor: ['#d30909', '#ff3838', '#ff6b6b', '#ff9f9f'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        }
        
        // تهيئة النوافذ المنبثقة
        function initModals() {
            // إغلاق النوافذ عند النقر خارجها
            window.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    e.target.style.display = 'none';
                }
            });
            
            // إغلاق النوافذ عند النقر على زر الإغلاق
            document.querySelectorAll('.close-modal').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.modal').style.display = 'none';
                });
            });
            
            // تأكيد الحذف
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', deleteItem);
            }
            
            if (cancelDeleteBtn) {
                cancelDeleteBtn.addEventListener('click', function() {
                    document.getElementById('deleteConfirmModal').style.display = 'none';
                });
            }
        }
        
        // تهيئة النماذج
        function initForms() {
            // معاينة الصور
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const previewId = this.id + 'Preview';
                        const preview = document.getElementById(previewId);
                        const fileName = this.id + 'Name';
                        const nameSpan = document.getElementById(fileName);
                        
                        if (nameSpan) {
                            nameSpan.textContent = file.name;
                        }
                        
                        if (preview) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.innerHTML = `<img src="${e.target.result}" alt="معاينة" style="max-width: 200px; max-height: 150px; border-radius: 5px;">`;
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                });
            });
            
            // حفظ الخدمة
            const serviceForm = document.getElementById('serviceForm');
            if (serviceForm) {
                serviceForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    
                    fetch('ajax/services_ajax.php?action=save', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showAlert(result.message, 'success');
                            document.getElementById('serviceModal').style.display = 'none';
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert(result.message, 'error');
                        }
                    })
                    .catch(error => showAlert('حدث خطأ: ' + error, 'error'));
                });
            }
            
            // حفظ الإحصائية
            const statForm = document.getElementById('statForm');
            if (statForm) {
                statForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    
                    fetch('ajax/statistics_ajax.php?action=save', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showAlert(result.message, 'success');
                            document.getElementById('statModal').style.display = 'none';
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert(result.message, 'error');
                        }
                    });
                });
            }
            
            // حفظ العميل
            const clientForm = document.getElementById('clientForm');
            if (clientForm) {
                clientForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    
                    fetch('ajax/clients_ajax.php?action=save', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showAlert(result.message, 'success');
                            document.getElementById('clientModal').style.display = 'none';
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert(result.message, 'error');
                        }
                    });
                });
            }
            
            // حفظ المستخدم
            const userForm = document.getElementById('userForm');
            if (userForm) {
                userForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    
                    fetch('ajax/users_ajax.php?action=save', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showAlert(result.message, 'success');
                            document.getElementById('userModal').style.display = 'none';
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert(result.message, 'error');
                        }
                    });
                });
            }
            
            // حفظ الإعدادات
            const settingsForm = document.getElementById('settingsForm');
            if (settingsForm) {
                const saveSettingsBtn = document.getElementById('saveSettingsBtn');
                if (saveSettingsBtn) {
                    saveSettingsBtn.addEventListener('click', function() {
                        const formData = new FormData(settingsForm);
                        
                        fetch('ajax/settings_ajax.php?action=save', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                showAlert(result.message, 'success');
                            } else {
                                showAlert(result.message, 'error');
                            }
                        });
                    });
                }
            }
            
            // حفظ إعدادات النظام
            const systemForm = document.getElementById('systemSettingsForm');
            if (systemForm) {
                const saveSystemSettingsBtn = document.getElementById('saveSystemSettingsBtn');
                if (saveSystemSettingsBtn) {
                    saveSystemSettingsBtn.addEventListener('click', function() {
                        const formData = new FormData(systemForm);
                        
                        fetch('ajax/system_settings_ajax.php?action=save', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                showAlert(result.message, 'success');
                            } else {
                                showAlert(result.message, 'error');
                            }
                        });
                    });
                }
            }
            
            // إرسال الرد
            const sendReplyBtn = document.getElementById('sendReplyBtn');
            if (sendReplyBtn) {
                sendReplyBtn.addEventListener('click', function() {
                    const replyContent = document.getElementById('replyContent').value;
                    if (!replyContent.trim()) {
                        showAlert('يرجى كتابة رد قبل الإرسال', 'error');
                        return;
                    }
                    
                    // هنا يمكن إضافة إرسال الرد عبر البريد الإلكتروني
                    showAlert('تم إرسال الرد بنجاح', 'success');
                    document.getElementById('replyModal').style.display = 'none';
                });
            }
        }
        
        // تهيئة الجداول
        function initTables() {
            // أحداث أزرار الحذف
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const type = this.getAttribute('data-type') || 'item';
                    showDeleteConfirmation(id, type);
                });
            });
            
            // أحداث أزرار التعديل
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const type = this.getAttribute('data-type') || 'item';
                    
                    if (type === 'service') {
                        editService(id);
                    } else if (type === 'statistic') {
                        editStatistic(id);
                    } else if (type === 'client') {
                        editClient(id);
                    } else if (type === 'user') {
                        editUser(id);
                    } else if (type === 'message') {
                        viewMessage(id);
                    }
                });
            });
            
            // أحداث أزرار الرد
            document.querySelectorAll('.reply-message').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    replyMessage(id);
                });
            });
            
            // أحداث أزرار تعيين كمقروء
            document.querySelectorAll('.mark-read').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    markAsRead(id);
                });
            });
        }
        
        // تحميل الخدمة للتعديل
        function editService(id) {
            fetch(`ajax/services_ajax.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(service => {
                    if (service.id) {
                        document.getElementById('serviceModalTitle').textContent = 'تعديل الخدمة';
                        document.getElementById('serviceId').value = service.id;
                        document.getElementById('serviceName').value = service.name;
                        document.getElementById('serviceDescription').value = service.description;
                        document.getElementById('serviceCategory').value = service.category;
                        document.getElementById('serviceIcon').value = service.icon;
                        document.getElementById('serviceOrder').value = service.display_order;
                        document.getElementById('serviceStatus').value = service.status;
                        document.getElementById('serviceModal').style.display = 'flex';
                    } else {
                        showAlert('حدث خطأ في جلب بيانات الخدمة', 'error');
                    }
                })
                .catch(error => showAlert('حدث خطأ: ' + error, 'error'));
        }
        
        // تحميل الإحصائية للتعديل
        function editStatistic(id) {
            fetch(`ajax/statistics_ajax.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(stat => {
                    if (stat.id) {
                        document.getElementById('statModalTitle').textContent = 'تعديل الإحصائية';
                        document.getElementById('statId').value = stat.id;
                        document.getElementById('statTitle').value = stat.title;
                        document.getElementById('statValue').value = stat.value;
                        document.getElementById('statIcon').value = stat.icon;
                        document.getElementById('statColor').value = stat.color;
                        document.getElementById('statStatus').value = stat.status;
                        document.getElementById('statModal').style.display = 'flex';
                    } else {
                        showAlert('حدث خطأ في جلب بيانات الإحصائية', 'error');
                    }
                })
                .catch(error => showAlert('حدث خطأ: ' + error, 'error'));
        }
        
        // تحميل العميل للتعديل
        function editClient(id) {
            fetch(`ajax/clients_ajax.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(client => {
                    if (client.id) {
                        document.getElementById('clientModalTitle').textContent = 'تعديل العميل';
                        document.getElementById('clientId').value = client.id;
                        document.getElementById('clientName').value = client.name;
                        document.getElementById('clientDescription').value = client.description;
                        document.getElementById('clientLocation').value = client.location;
                        document.getElementById('clientOrder').value = client.display_order;
                        document.getElementById('clientStatus').value = client.status;
                        
                        if (client.logo) {
                            document.getElementById('clientLogoPreview').innerHTML = 
                                `<img src="uploads/clients/${client.logo}" alt="الشعار الحالي" style="max-width: 200px; max-height: 150px; border-radius: 5px;">`;
                        }
                        
                        document.getElementById('clientModal').style.display = 'flex';
                    } else {
                        showAlert('حدث خطأ في جلب بيانات العميل', 'error');
                    }
                })
                .catch(error => showAlert('حدث خطأ: ' + error, 'error'));
        }
        
        // تحميل المستخدم للتعديل
        function editUser(id) {
            fetch(`ajax/users_ajax.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(user => {
                    if (user.id) {
                        document.getElementById('userModalTitle').textContent = 'تعديل المستخدم';
                        document.getElementById('userId').value = user.id;
                        document.getElementById('userUsername').value = user.username;
                        document.getElementById('userEmail').value = user.email;
                        document.getElementById('userRole').value = user.role;
                        document.getElementById('userStatus').value = user.status;
                        document.getElementById('userPassword').required = false;
                        document.getElementById('userModal').style.display = 'flex';
                    } else {
                        showAlert('حدث خطأ في جلب بيانات المستخدم', 'error');
                    }
                })
                .catch(error => showAlert('حدث خطأ: ' + error, 'error'));
        }
        
        // عرض الرسالة
        function viewMessage(id) {
            fetch(`ajax/messages_ajax.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(message => {
                    if (message.id) {
                        document.getElementById('messageModalBody').innerHTML = `
                            <div class="message-details">
                                <div class="message-meta">
                                    <span><strong>المرسل:</strong> ${message.name}</span>
                                    <span><strong>التاريخ:</strong> ${message.created_at}</span>
                                </div>
                                <div class="message-meta">
                                    <span><strong>البريد الإلكتروني:</strong> <a href="mailto:${message.email}">${message.email}</a></span>
                                    <span><strong>الهاتف:</strong> <a href="tel:${message.phone}">${message.phone}</a></span>
                                </div>
                                <div style="margin-top: 20px;">
                                    <strong>الرسالة:</strong>
                                    <p style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px;">${message.message}</p>
                                </div>
                            </div>
                            <div class="reply-section">
                                <button class="btn btn-reply" onclick="replyMessage(${id})">الرد على الرسالة</button>
                            </div>
                        `;
                        document.getElementById('messageModal').style.display = 'flex';
                        
                        // تحديث حالة الرسالة
                        const messageRow = document.querySelector(`.btn-edit[data-id="${id}"]`).closest('tr');
                        if (messageRow && messageRow.classList.contains('unread-message')) {
                            messageRow.classList.remove('unread-message');
                            const statusCell = messageRow.querySelector('.status');
                            statusCell.textContent = 'تم القراءة';
                            statusCell.className = 'status status-completed';
                        }
                    } else {
                        showAlert('حدث خطأ في جلب الرسالة', 'error');
                    }
                })
                .catch(error => showAlert('حدث خطأ: ' + error, 'error'));
        }
        
        // الرد على الرسالة
        function replyMessage(id) {
            fetch(`ajax/messages_ajax.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(message => {
                    if (message.id) {
                        document.getElementById('replyMessageDetails').innerHTML = `
                            <div class="message-meta">
                                <span><strong>المرسل:</strong> ${message.name}</span>
                                <span><strong>التاريخ:</strong> ${message.created_at}</span>
                            </div>
                            <div class="message-meta">
                                <span><strong>البريد الإلكتروني:</strong> ${message.email}</span>
                                <span><strong>الهاتف:</strong> ${message.phone}</span>
                            </div>
                            <div style="margin-top: 15px;">
                                <strong>الرسالة الأصلية:</strong>
                                <p style="background: white; padding: 10px; border-radius: 5px; margin-top: 5px;">${message.message}</p>
                            </div>
                        `;
                        document.getElementById('replyContent').value = '';
                        document.getElementById('replyModal').style.display = 'flex';
                    }
                })
                .catch(error => showAlert('حدث خطأ: ' + error, 'error'));
        }
        
        // تعيين كمقروء
        function markAsRead(id) {
            const formData = new FormData();
            formData.append('id', id);
            
            fetch('ajax/messages_ajax.php?action=mark_read', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showAlert(result.message, 'success');
                    const messageRow = document.querySelector(`.mark-read[data-id="${id}"]`).closest('tr');
                    if (messageRow) {
                        messageRow.classList.remove('unread-message');
                        const statusCell = messageRow.querySelector('.status');
                        statusCell.textContent = 'تم القراءة';
                        statusCell.className = 'status status-completed';
                    }
                } else {
                    showAlert(result.message, 'error');
                }
            });
        }
        
        // عرض تأكيد الحذف
        function showDeleteConfirmation(id, type) {
            currentDeleteItem = id;
            currentDeleteType = type;
            
            const messages = {
                'service': "هل أنت متأكد من أنك تريد حذف هذه الخدمة؟",
                'client': "هل أنت متأكد من أنك تريد حذف هذا العميل؟",
                'message': "هل أنت متأكد من أنك تريد حذف هذه الرسالة؟",
                'statistic': "هل أنت متأكد من أنك تريد حذف هذه الإحصائية؟",
                'user': "هل أنت متأكد من أنك تريد حذف هذا المستخدم؟"
            };
            
            document.getElementById('deleteConfirmMessage').textContent = 
                messages[type] || "هل أنت متأكد من أنك تريد حذف هذا العنصر؟";
            document.getElementById('deleteConfirmModal').style.display = 'flex';
        }
        
        // حذف العنصر
        function deleteItem() {
            if (!currentDeleteItem || !currentDeleteType) return;
            
            const formData = new FormData();
            formData.append('id', currentDeleteItem);
            
            fetch(`ajax/${currentDeleteType}s_ajax.php?action=delete`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showAlert(result.message, 'success');
                    document.getElementById('deleteConfirmModal').style.display = 'none';
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            })
            .catch(error => showAlert('حدث خطأ في الاتصال: ' + error, 'error'));
        }
        
        // عرض رسالة تنبيه
        function showAlert(message, type) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            
            Toast.fire({
                icon: type,
                title: message
            });
        }
    </script>
</body>
</html>
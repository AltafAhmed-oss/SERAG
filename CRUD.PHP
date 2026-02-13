<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم سراج سوفت - متكاملة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #d30909;
            --secondary-color: #ff3838;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
            --sidebar-width: 250px;
            --transition: all 0.3s ease;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: var(--dark-color);
            line-height: 1.6;
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* الشريط الجانبي */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--dark-color);
            color: var(--light-color);
            transition: var(--transition);
            height: 100vh;
            position: fixed;
            right: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-header img {
            height: 50px;
            margin-left: 10px;
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            margin: 0;
            color: white;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: var(--light-color);
            text-decoration: none;
            transition: var(--transition);
            font-size: 1rem;
            cursor: pointer;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-right: 4px solid var(--primary-color);
        }

        .sidebar-menu i {
            margin-left: 10px;
            width: 20px;
            text-align: center;
        }

        /* المحتوى الرئيسي */
        .main-content {
            flex: 1;
            margin-right: var(--sidebar-width);
            padding: 20px;
            transition: var(--transition);
        }

        /* الشريط العلوي */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
        }

        .search-box {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 30px;
            padding: 5px 15px;
        }

        .search-box input {
            border: none;
            background: transparent;
            padding: 8px;
            outline: none;
            width: 200px;
        }

        .user-area {
            display: flex;
            align-items: center;
        }

        .notification {
            position: relative;
            margin-left: 20px;
        }

        .notification i {
            font-size: 1.2rem;
            color: var(--gray-color);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            left: -5px;
            background-color: var(--secondary-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-left: 10px;
        }

        /* بطاقات الإحصائيات */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .bg-primary {
            background-color: var(--primary-color);
        }

        .bg-success {
            background-color: var(--secondary-color);
        }

        .bg-warning {
            background-color: var(--primary-color);
            opacity: 0.8;
        }

        .bg-info {
            background-color: var(--secondary-color);
            opacity: 0.8;
        }

        .stat-info h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: var(--gray-color);
            font-size: 0.9rem;
        }

        /* الرسوم البيانية */
        .charts {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .chart-actions select {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            outline: none;
        }

        .chart-canvas {
            height: 300px;
            width: 100%;
        }

        /* صفحات المحتوى */
        .content-page {
            display: none;
        }

        .content-page.active {
            display: block;
        }

        /* التكيف مع الشاشات الصغيرة */
        @media (max-width: 992px) {
            .charts {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .sidebar:hover {
                width: var(--sidebar-width);
            }
            
            .main-content {
                margin-right: 70px;
            }
            
            .sidebar-header h2, .sidebar-menu span {
                display: none;
            }
            
            .sidebar:hover .sidebar-header h2,
            .sidebar:hover .sidebar-menu span {
                display: block;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .search-box input {
                width: 150px;
            }
        }

        @media (max-width: 480px) {
            .topbar {
                flex-direction: column;
                gap: 15px;
            }
            
            .user-area {
                width: 100%;
                justify-content: space-between;
            }
            
            .search-box {
                width: 100%;
            }
            
            .search-box input {
                width: 100%;
            }
        }

        /* نماذج الإدخال */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group textarea {
            min-height: 120px;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: block;
            padding: 12px;
            border: 1px dashed #ddd;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            background-color: #f8f9fa;
        }

        .file-upload-label:hover {
            background-color: #e9ecef;
        }

        .file-name {
            margin-top: 5px;
            font-size: 0.9rem;
            color: var(--gray-color);
        }

        .image-preview {
            margin-top: 10px;
            text-align: center;
        }

        .image-preview img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1100;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* جداول البيانات */
        .data-table {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .service-icon-cell {
            width: 60px;
            text-align: center;
        }

        .service-icon-cell i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .client-logo-cell {
            width: 80px;
            text-align: center;
        }

        .client-logo-cell img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-completed, .status-active {
            background-color: rgba(211, 9, 9, 0.2);
            color: var(--primary-color);
        }

        .status-pending, .status-inactive {
            background-color: rgba(255, 56, 56, 0.2);
            color: var(--secondary-color);
        }

        .btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn:hover {
            background: #a80707;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-edit {
            background-color: var(--primary-color);
        }

        .btn-delete {
            background-color: var(--secondary-color);
        }

        .btn-add {
            background-color: var(--primary-color);
        }

        .btn-reply {
            background-color: var(--primary-color);
        }

        .btn-mark-read {
            background-color: var(--secondary-color);
        }

        .alert {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: none;
        }

        .alert-success {
            background-color: rgba(211, 9, 9, 0.2);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .alert-error {
            background-color: rgba(255, 56, 56, 0.2);
            color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
        }

        /* تنسيقات إضافية للخدمات */
        .service-description-cell {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .service-description-cell:hover {
            white-space: normal;
            overflow: visible;
        }

        .client-description-cell {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .client-description-cell:hover {
            white-space: normal;
            overflow: visible;
        }

        /* تنسيقات إضافية لرسائل التواصل */
        .message-content-cell {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .message-content-cell:hover {
            white-space: normal;
            overflow: visible;
        }

        .unread-message {
            background-color: rgba(211, 9, 9, 0.1);
            font-weight: bold;
        }

        .message-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: var(--border-radius);
            margin: 10px 0;
        }

        .message-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: var(--gray-color);
            font-size: 0.9rem;
        }

        .reply-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .reply-history {
            background-color: #f1f3f4;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
        }

        .reply-item {
            padding: 10px;
            margin-bottom: 10px;
            border-left: 3px solid var(--primary-color);
            background-color: white;
        }

        .reply-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: var(--gray-color);
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- الشريط الجانبي -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="assets/img/icon.png" alt="سراج سوفت">
                <h2>سراج سوفت</h2>
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li><a href="#" class="active" data-page="dashboard"><i class="fas fa-home"></i> <span>لوحة التحكم</span></a></li>
                    <li><a href="#" data-page="statistics"><i class="fas fa-chart-bar"></i> <span>إدارة الإحصائيات</span></a></li>
                    <li><a href="#" data-page="services"><i class="fas fa-building"></i> <span>إدارة الخدمات</span></a></li>
                    <li><a href="#" data-page="clients"><i class="fas fa-users"></i> <span>العملاء</span></a></li>
                    <li><a href="#" data-page="messages"><i class="fas fa-envelope"></i> <span>رسائل التواصل</span></a></li>
                    <li><a href="#" data-page="settings"><i class="fas fa-sliders-h"></i> <span>إعدادات الموقع</span></a></li>
                    <li><a href="#" data-page="users"><i class="fas fa-user-cog"></i> <span>المستخدمون</span></a></li>
                    <li><a href="#" data-page="system-settings"><i class="fas fa-cog"></i> <span>الإعدادات</span></a></li>
                    <li><a href="#" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> <span>تسجيل الخروج</span></a></li>
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
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </div>
                    <div class="notification">
                        <i class="fas fa-envelope"></i>
                        <span class="notification-badge">5</span>
                    </div>
                    <div class="user-profile">
                        <img src="https://via.placeholder.com/40" alt="صورة المستخدم">
                        <span>مدير النظام</span>
                    </div>
                </div>
            </div>

            <!-- صفحة لوحة التحكم -->
            <div id="dashboard" class="content-page active">
                <!-- بطاقات الإحصائيات -->
                <div class="stats-cards">
                    <div class="card stat-card">
                        <div class="stat-info">
                            <h3>٥٬٢٤٧</h3>
                            <p>عدد الزوار</p>
                        </div>
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-info">
                            <h3>١٬٢٥٤</h3>
                            <p>طلبات التواصل</p>
                        </div>
                        <div class="stat-icon bg-success">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-info">
                            <h3>٤٥٢</h3>
                            <p>العملاء</p>
                        </div>
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-info">
                            <h3>٢٤</h3>
                            <p>الخدمات</p>
                        </div>
                        <div class="stat-icon bg-info">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>

                <!-- الرسوم البيانية -->
                <div class="charts">
                    <div class="chart-container">
                        <div class="chart-header">
                            <div class="chart-title">إحصائيات الزيارات</div>
                            <div class="chart-actions">
                                <select id="visitsPeriod">
                                    <option value="week">أسبوعي</option>
                                    <option value="month">شهري</option>
                                    <option value="year">سنوي</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="visitsChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div class="chart-header">
                            <div class="chart-title">نسبة الخدمات</div>
                            <div class="chart-actions">
                                <select id="servicesPeriod">
                                    <option value="quarter">ربع سنوي</option>
                                    <option value="half">نصف سنوي</option>
                                    <option value="year">سنوي</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="servicesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- طلبات التواصل الأخيرة -->
                <div class="data-table">
                    <div class="table-header">
                        <div class="table-title">طلبات التواصل الأخيرة</div>
                        <a href="#" class="btn" data-page="messages">عرض الكل</a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>محمد أحمد</td>
                                    <td>mohamed@example.com</td>
                                    <td>+967777777777</td>
                                    <td>٢٠/١٠/٢٠٢٣</td>
                                    <td><span class="status status-pending">قيد المراجعة</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit view-message" data-id="1">عرض</button>
                                        <button class="btn btn-sm btn-delete delete-message" data-id="1">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>سارة عبدالله</td>
                                    <td>sara@example.com</td>
                                    <td>+967777777778</td>
                                    <td>٢١/١٠/٢٠٢٣</td>
                                    <td><span class="status status-completed">تم الرد</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit view-message" data-id="2">عرض</button>
                                        <button class="btn btn-sm btn-delete delete-message" data-id="2">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>علي حسن</td>
                                    <td>ali@example.com</td>
                                    <td>+967777777779</td>
                                    <td>٢٢/١٠/٢٠٢٣</td>
                                    <td><span class="status status-pending">قيد المراجعة</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit view-message" data-id="3">عرض</button>
                                        <button class="btn btn-sm btn-delete delete-message" data-id="3">حذف</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- الخدمات -->
                <div class="data-table">
                    <div class="table-header">
                        <div class="table-title">الخدمات</div>
                        <button class="btn" id="addServiceBtn">إضافة خدمة</button>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم الخدمة</th>
                                    <th>الفئة</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>أنظمة ERP</td>
                                    <td>أنظمة إدارة</td>
                                    <td><span class="status status-completed">نشط</span></td>
                                    <td>٠١/٠١/٢٠٢٣</td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-service" data-id="1">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-service" data-id="1">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>تطبيقات الجوال</td>
                                    <td>تطبيقات</td>
                                    <td><span class="status status-completed">نشط</span></td>
                                    <td>٠٢/٠١/٢٠٢٣</td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-service" data-id="2">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-service" data-id="2">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>تطوير الويب</td>
                                    <td>مواقع</td>
                                    <td><span class="status status-completed">نشط</span></td>
                                    <td>٠٣/٠١/٢٠٢٣</td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-service" data-id="3">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-service" data-id="3">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>حلول مخصصة</td>
                                    <td>مخصص</td>
                                    <td><span class="status status-pending">غير نشط</span></td>
                                    <td>٠٤/٠١/٢٠٢٣</td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-service" data-id="4">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-service" data-id="4">حذف</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- صفحة إدارة الإحصائيات -->
            <div id="statistics" class="content-page">
                <div class="alert alert-success" id="statSuccessAlert"></div>
                <div class="alert alert-error" id="statErrorAlert"></div>
                
                <div class="card">
                    <div class="table-header">
                        <div class="table-title">إدارة الإحصائيات</div>
                        <button class="btn btn-add" id="addStatBtn">إضافة إحصائية جديدة</button>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="statsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>العنوان</th>
                                    <th>القيمة</th>
                                    <th>الأيقونة</th>
                                    <th>اللون</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>سنوات من الخبرة</td>
                                    <td>20+</td>
                                    <td><i class="fas fa-calendar-alt"></i></td>
                                    <td><span class="status status-active">أحمر</span></td>
                                    <td><span class="status status-active">نشط</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-stat" data-id="1">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-stat" data-id="1">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>مشروع ناجح</td>
                                    <td>100+</td>
                                    <td><i class="fas fa-project-diagram"></i></td>
                                    <td><span class="status status-active">أحمر</span></td>
                                    <td><span class="status status-active">نشط</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-stat" data-id="2">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-stat" data-id="2">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>عميل راضٍ</td>
                                    <td>50+</td>
                                    <td><i class="fas fa-smile"></i></td>
                                    <td><span class="status status-active">أحمر</span></td>
                                    <td><span class="status status-active">نشط</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-stat" data-id="3">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-stat" data-id="3">حذف</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- صفحة إدارة الخدمات -->
            <div id="services" class="content-page">
                <div class="alert alert-success" id="serviceSuccessAlert"></div>
                <div class="alert alert-error" id="serviceErrorAlert"></div>
                
                <div class="card">
                    <div class="table-header">
                        <div class="table-title">إدارة الخدمات</div>
                        <button class="btn btn-add" id="addServiceBtn2">إضافة خدمة جديدة</button>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="servicesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="service-icon-cell">الأيقونة</th>
                                    <th>اسم الخدمة</th>
                                    <th class="service-description-cell">الوصف</th>
                                    <th>الفئة</th>
                                    <th>الترتيب</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td class="service-icon-cell">
                                        <i class="fas fa-building"></i>
                                    </td>
                                    <td>أنظمة ERP</td>
                                    <td class="service-description-cell">أنظمة مالية، موارد بشرية، إدارة مخازن متكاملة وشاملة لجميع احتياجات مؤسستك. صممت خصيصًا لتلبي متطلبات الأعمال بمختلف أحجامها.</td>
                                    <td>أنظمة إدارة</td>
                                    <td>1</td>
                                    <td><span class="status status-active">نشط</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-service" data-id="1">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-service" data-id="1">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td class="service-icon-cell">
                                        <i class="fas fa-mobile-alt"></i>
                                    </td>
                                    <td>تطبيقات الجوال</td>
                                    <td class="service-description-cell">تطبيقات iOS و Android عالية الأداء مع واجهات مستخدم جذابة وسهلة الاستخدام. نضمن تجربة مستخدم فريدة وسلسة.</td>
                                    <td>تطبيقات</td>
                                    <td>2</td>
                                    <td><span class="status status-active">نشط</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-service" data-id="2">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-service" data-id="2">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td class="service-icon-cell">
                                        <i class="fas fa-laptop-code"></i>
                                    </td>
                                    <td>تطوير الويب</td>
                                    <td class="service-description-cell">مواقع إلكترونية ومتاجر تفاعلية بتقنيات حديثة وتصاميم متجاوبة. نبتكر حلولاً رقمية تعزز حضورك على الإنترنت.</td>
                                    <td>مواقع</td>
                                    <td>3</td>
                                    <td><span class="status status-inactive">غير نشط</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-service" data-id="3">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-service" data-id="3">حذف</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- صفحة العملاء -->
            <div id="clients" class="content-page">
                <div class="alert alert-success" id="clientSuccessAlert"></div>
                <div class="alert alert-error" id="clientErrorAlert"></div>
                
                <div class="card">
                    <div class="table-header">
                        <div class="table-title">إدارة العملاء</div>
                        <button class="btn btn-add" id="addClientBtn">إضافة عميل جديد</button>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="clientsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="client-logo-cell">الشعار</th>
                                    <th>اسم العميل</th>
                                    <th class="client-description-cell">الوصف</th>
                                    <th>الموقع</th>
                                    <th>الترتيب</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td class="client-logo-cell">
                                        <img src="https://via.placeholder.com/50" alt="شعار العميل">
                                    </td>
                                    <td>شركة التقنية المتطورة</td>
                                    <td class="client-description-cell">إحدى الشركات الرائدة في مجال التقنية والحلول البرمجية المتكاملة. تقدم خدماتها لعملاء من مختلف القطاعات.</td>
                                    <td>صنعاء</td>
                                    <td>1</td>
                                    <td><span class="status status-active">نشط</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-client" data-id="1">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-client" data-id="1">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td class="client-logo-cell">
                                        <img src="https://via.placeholder.com/50" alt="شعار العميل">
                                    </td>
                                    <td>مؤسسة النهضة التجارية</td>
                                    <td class="client-description-cell">مؤسسة تجارية رائدة تعمل في مجال الاستيراد والتصدير ولديها شبكة واسعة من العلاقات التجارية.</td>
                                    <td>عدن</td>
                                    <td>2</td>
                                    <td><span class="status status-active">نشط</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-client" data-id="2">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-client" data-id="2">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td class="client-logo-cell">
                                        <img src="https://via.placeholder.com/50" alt="شعار العميل">
                                    </td>
                                    <td>مجموعة الأعمال المتحدة</td>
                                    <td class="client-description-cell">مجموعة شركات تعمل في عدة مجالات منها العقارات، السياحة، والخدمات اللوجستية.</td>
                                    <td>تعز</td>
                                    <td>3</td>
                                    <td><span class="status status-inactive">غير نشط</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit edit-client" data-id="3">تعديل</button>
                                        <button class="btn btn-sm btn-delete delete-client" data-id="3">حذف</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- صفحة رسائل التواصل -->
            <div id="messages" class="content-page">
                <div class="alert alert-success" id="messageSuccessAlert"></div>
                <div class="alert alert-error" id="messageErrorAlert"></div>
                
                <div class="card">
                    <div class="table-header">
                        <div class="table-title">رسائل التواصل</div>
                        <div>
                            <button class="btn btn-mark-read" id="markAllReadBtn">تعيين الكل كمقروء</button>
                            <button class="btn btn-delete" id="deleteAllMessagesBtn">حذف الكل</button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="messagesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th class="message-content-cell">الرسالة</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="unread-message">
                                    <td>1</td>
                                    <td>محمد أحمد</td>
                                    <td>mohamed@example.com</td>
                                    <td>+967777777777</td>
                                    <td class="message-content-cell">أرغب في الحصول على معلومات إضافية حول خدمات تطوير تطبيقات الجوال التي تقدمونها، وما هي التكلفة التقريبية لمشروع تطبيق بمواصفات محددة.</td>
                                    <td>٢٠/١٠/٢٠٢٣</td>
                                    <td><span class="status status-pending">غير مقروء</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit view-message" data-id="1">عرض</button>
                                        <button class="btn btn-sm btn-reply reply-message" data-id="1">رد</button>
                                        <button class="btn btn-sm btn-delete delete-message" data-id="1">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>سارة عبدالله</td>
                                    <td>sara@example.com</td>
                                    <td>+967777777778</td>
                                    <td class="message-content-cell">شكرًا لكم على الخدمات المميزة التي تقدمونها، أود الاستفسار عن إمكانية تطوير نظام إداري متكامل لشركتنا الناشئة.</td>
                                    <td>٢١/١٠/٢٠٢٣</td>
                                    <td><span class="status status-completed">تم الرد</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit view-message" data-id="2">عرض</button>
                                        <button class="btn btn-sm btn-reply reply-message" data-id="2">رد</button>
                                        <button class="btn btn-sm btn-delete delete-message" data-id="2">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>علي حسن</td>
                                    <td>ali@example.com</td>
                                    <td>+967777777779</td>
                                    <td class="message-content-cell">أحتاج إلى مساعدة في تحسين موقعنا الإلكتروني الحالي وجعله أكثر تفاعلية وسرعة في التحميل، هل يمكنكم تقديم عرض سعر؟</td>
                                    <td>٢٢/١٠/٢٠٢٣</td>
                                    <td><span class="status status-completed">تم الرد</span></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit view-message" data-id="3">عرض</button>
                                        <button class="btn btn-sm btn-reply reply-message" data-id="3">رد</button>
                                        <button class="btn btn-sm btn-delete delete-message" data-id="3">حذف</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
  <!-- صفحة إعدادات الموقع -->
            <div id="settings" class="content-page">
                <div class="alert alert-success" id="settingsSuccessAlert"></div>
                <div class="alert alert-error" id="settingsErrorAlert"></div>
                
                <div class="card">
                    <div class="table-header">
                        <div class="table-title">إعدادات الموقع</div>
                        <button class="btn" id="saveSettingsBtn">حفظ الإعدادات</button>
                    </div>
                    
                    <div class="form-container">
                        <form id="settingsForm">
                            <div class="form-group">
                                <label for="siteName">اسم الموقع</label>
                                <input type="text" id="siteName" name="siteName" value="سراج سوفت" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="siteDescription">وصف الموقع</label>
                                <textarea id="siteDescription" name="siteDescription" required>شركة رائدة في مجال البرمجيات وتطوير الويب وتطبيقات الجوال، نقدم حلولاً تقنية متكاملة للشركات والمؤسسات.</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="siteKeywords">الكلمات المفتاحية</label>
                                <input type="text" id="siteKeywords" name="siteKeywords" value="برمجة, تطوير ويب, تطبيقات جوال, أنظمة إدارة, سراج سوفت" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="siteLogo">شعار الموقع</label>
                                <div class="file-upload">
                                    <input type="file" id="siteLogo" name="siteLogo" accept="image/*">
                                    <label for="siteLogo" class="file-upload-label">
                                        <i class="fas fa-upload"></i> اختر ملف الشعار
                                    </label>
                                    <div class="file-name" id="siteLogoName">لم يتم اختيار ملف</div>
                                </div>
                                <div class="image-preview" id="siteLogoPreview">
                                    <img src="https://via.placeholder.com/200x100" alt="شعار الموقع الحالي">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="siteFavicon">الأيقونة المفضلة (Favicon)</label>
                                <div class="file-upload">
                                    <input type="file" id="siteFavicon" name="siteFavicon" accept="image/*">
                                    <label for="siteFavicon" class="file-upload-label">
                                        <i class="fas fa-upload"></i> اختر ملف الأيقونة
                                    </label>
                                    <div class="file-name" id="siteFaviconName">لم يتم اختيار ملف</div>
                                </div>
                                <div class="image-preview" id="siteFaviconPreview">
                                    <img src="https://via.placeholder.com/32x32" alt="الأيقونة الحالية">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="contactEmail">البريد الإلكتروني للتواصل</label>
                                <input type="email" id="contactEmail" name="contactEmail" value="info@sirajsoft.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contactPhone">هاتف التواصل</label>
                                <input type="text" id="contactPhone" name="contactPhone" value="+967777777777" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contactAddress">عنوان الشركة</label>
                                <input type="text" id="contactAddress" name="contactAddress" value="صنعاء، اليمن" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="facebookUrl">رابط فيسبوك</label>
                                <input type="url" id="facebookUrl" name="facebookUrl" value="https://facebook.com/sirajsoft">
                            </div>
                            
                            <div class="form-group">
                                <label for="twitterUrl">رابط تويتر</label>
                                <input type="url" id="twitterUrl" name="twitterUrl" value="https://twitter.com/sirajsoft">
                            </div>
                            
                            <div class="form-group">
                                <label for="linkedinUrl">رابط لينكد إن</label>
                                <input type="url" id="linkedinUrl" name="linkedinUrl" value="https://linkedin.com/company/sirajsoft">
                            </div>
                            
                            <div class="form-group">
                                <label for="instagramUrl">رابط إنستغرام</label>
                                <input type="url" id="instagramUrl" name="instagramUrl" value="https://instagram.com/sirajsoft">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- صفحة إدارة المستخدمين -->
            <div id="users" class="content-page">
                <div class="alert alert-success" id="userSuccessAlert"></div>
                <div class="alert alert-error" id="userErrorAlert"></div>
                
                <div class="card">
                    <div class="table-header">
                        <div class="table-title">إدارة المستخدمين</div>
                        <button class="btn btn-add" id="addUserBtn">إضافة مستخدم جديد</button>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="usersTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم المستخدم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الدور</th>
                                    <th>الحالة</th>
                                    <th>آخر نشاط</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>مدير النظام</td>
                                    <td>admin@sirajsoft.com</td>
                                    <td>مدير</td>
                                    <td><span class="status status-active">نشط</span></td>
                                    <td>٢٠/١٠/٢٠٢٣</td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit" data-id="1">تعديل</button>
                                        <button class="btn btn-sm btn-delete" data-id="1">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>أحمد محمد</td>
                                    <td>ahmed@sirajsoft.com</td>
                                    <td>محرر</td>
                                    <td><span class="status status-active">نشط</span></td>
                                    <td>١٩/١٠/٢٠٢٣</td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit" data-id="2">تعديل</button>
                                        <button class="btn btn-sm btn-delete" data-id="2">حذف</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>فاطمة علي</td>
                                    <td>fatima@sirajsoft.com</td>
                                    <td>مشرف</td>
                                    <td><span class="status status-inactive">غير نشط</span></td>
                                    <td>١٥/١٠/٢٠٢٣</td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-edit" data-id="3">تعديل</button>
                                        <button class="btn btn-sm btn-delete" data-id="3">حذف</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- صفحة الإعدادات -->
            <div id="system-settings" class="content-page">
                <div class="alert alert-success" id="systemSuccessAlert"></div>
                <div class="alert alert-error" id="systemErrorAlert"></div>
                
                <div class="card">
                    <div class="table-header">
                        <div class="table-title">إعدادات النظام</div>
                        <button class="btn" id="saveSystemSettingsBtn">حفظ الإعدادات</button>
                    </div>
                    
                    <div class="form-container">
                        <form id="systemSettingsForm">
                            <div class="form-group">
                                <label for="maintenanceMode">وضع الصيانة</label>
                                <select id="maintenanceMode" name="maintenanceMode">
                                    <option value="0">معطل</option>
                                    <option value="1">مفعل</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="registrationEnabled">تفعيل التسجيل</label>
                                <select id="registrationEnabled" name="registrationEnabled">
                                    <option value="1">مفعل</option>
                                    <option value="0">معطل</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="defaultLanguage">اللغة الافتراضية</label>
                                <select id="defaultLanguage" name="defaultLanguage">
                                    <option value="ar">العربية</option>
                                    <option value="en">الإنجليزية</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="timezone">المنطقة الزمنية</label>
                                <select id="timezone" name="timezone">
                                    <option value="Asia/Aden">(GMT+3) اليمن، السعودية</option>
                                    <option value="Asia/Riyadh">(GMT+3) الرياض</option>
                                    <option value="Asia/Dubai">(GMT+4) الإمارات</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="dateFormat">تنسيق التاريخ</label>
                                <select id="dateFormat" name="dateFormat">
                                    <option value="d/m/Y">يوم/شهر/سنة (٢٠/١٠/٢٠٢٣)</option>
                                    <option value="Y-m-d">سنة-شهر-يوم (٢٠٢٣-١٠-٢٠)</option>
                                    <option value="m/d/Y">شهر/يوم/سنة (١٠/٢٠/٢٠٢٣)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="itemsPerPage">عدد العناصر في الصفحة</label>
                                <input type="number" id="itemsPerPage" name="itemsPerPage" value="10" min="5" max="100">
                            </div>
                            
                            <div class="form-group">
                                <label for="adminEmail">بريد المدير</label>
                                <input type="email" id="adminEmail" name="adminEmail" value="admin@sirajsoft.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="smtpHost">خادم البريد (SMTP)</label>
                                <input type="text" id="smtpHost" name="smtpHost" value="mail.sirajsoft.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="smtpPort">منفذ SMTP</label>
                                <input type="number" id="smtpPort" name="smtpPort" value="587">
                            </div>
                            
                            <div class="form-group">
                                <label for="smtpUsername">اسم مستخدم SMTP</label>
                                <input type="text" id="smtpUsername" name="smtpUsername" value="noreply@sirajsoft.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="smtpPassword">كلمة مرور SMTP</label>
                                <input type="password" id="smtpPassword" name="smtpPassword" value="********">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

            <!-- صفحات أخرى -->
            <div id="settings" class="content-page">
                <h2>إعدادات الموقع</h2>
                <p>هذه صفحة إعدادات الموقع. يمكنك تعديل الإعدادات العامة للموقع من هنا.</p>
            </div>

            <div id="users" class="content-page">
                <h2>المستخدمون</h2>
                <p>هذه صفحة إدارة المستخدمين. يمكنك إضافة وتعديل وحذف المستخدمين من هنا.</p>
            </div>

            <div id="system-settings" class="content-page">
                <h2>الإعدادات</h2>
                <p>هذه صفحة الإعدادات العامة للنظام. يمكنك تعديل إعدادات النظام من هنا.</p>
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
                        <textarea id="replyContent" placeholder="اكتب ردك هنا..."></textarea>
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
                <form id="serviceForm">
                    <input type="hidden" id="serviceId">
                    <div class="form-group">
                        <label for="serviceName">اسم الخدمة</label>
                        <input type="text" id="serviceName" required>
                    </div>
                    <div class="form-group">
                        <label for="serviceDescription">وصف الخدمة</label>
                        <textarea id="serviceDescription" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="serviceCategory">الفئة</label>
                        <select id="serviceCategory" required>
                            <option value="">اختر الفئة</option>
                            <option value="أنظمة إدارة">أنظمة إدارة</option>
                            <option value="تطبيقات">تطبيقات</option>
                            <option value="مواقع">مواقع</option>
                            <option value="مخصص">مخصص</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="serviceIcon">أيقونة الخدمة</label>
                        <select id="serviceIcon" required>
                            <option value="">اختر الأيقونة</option>
                            <option value="fas fa-building">مبنى</option>
                            <option value="fas fa-mobile-alt">جوال</option>
                            <option value="fas fa-laptop-code">كمبيوتر</option>
                            <option value="fas fa-chart-line">رسم بياني</option>
                            <option value="fas fa-database">قاعدة بيانات</option>
                            <option value="fas fa-cloud">سحابة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="serviceOrder">ترتيب الخدمة</label>
                        <input type="number" id="serviceOrder" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="serviceStatus">حالة الخدمة</label>
                        <select id="serviceStatus" required>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn">حفظ الخدمة</button>
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
                    <input type="hidden" id="statId">
                    <div class="form-group">
                        <label for="statTitle">عنوان الإحصائية</label>
                        <input type="text" id="statTitle" required>
                    </div>
                    <div class="form-group">
                        <label for="statValue">قيمة الإحصائية</label>
                        <input type="text" id="statValue" required>
                    </div>
                    <div class="form-group">
                        <label for="statIcon">أيقونة الإحصائية</label>
                        <select id="statIcon" required>
                            <option value="">اختر الأيقونة</option>
                            <option value="fas fa-calendar-alt">تقويم</option>
                            <option value="fas fa-project-diagram">مشروع</option>
                            <option value="fas fa-smile">وجه مبتسم</option>
                            <option value="fas fa-users">مستخدمين</option>
                            <option value="fas fa-code">كود</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="statColor">لون الإحصائية</label>
                        <select id="statColor" required>
                            <option value="red">أحمر</option>
                            <option value="blue">أزرق</option>
                            <option value="green">أخضر</option>
                            <option value="orange">برتقالي</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="statStatus">حالة الإحصائية</label>
                        <select id="statStatus" required>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn">حفظ الإحصائية</button>
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
                <form id="clientForm">
                    <input type="hidden" id="clientId">
                    <div class="form-group">
                        <label for="clientName">اسم العميل</label>
                        <input type="text" id="clientName" required>
                    </div>
                    <div class="form-group">
                        <label for="clientDescription">وصف العميل</label>
                        <textarea id="clientDescription" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="clientLocation">موقع العميل</label>
                        <input type="text" id="clientLocation" required>
                    </div>
                    <div class="form-group">
                        <label for="clientLogo">شعار العميل</label>
                        <div class="file-upload">
                            <input type="file" id="clientLogo" accept="image/*">
                            <label for="clientLogo" class="file-upload-label">اختر صورة الشعار</label>
                            <div class="file-name" id="clientLogoName">لم يتم اختيار ملف</div>
                        </div>
                        <div class="image-preview" id="clientLogoPreview"></div>
                    </div>
                    <div class="form-group">
                        <label for="clientOrder">ترتيب العميل</label>
                        <input type="number" id="clientOrder" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="clientStatus">حالة العميل</label>
                        <select id="clientStatus" required>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn">حفظ العميل</button>
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

    <script>
        // بيانات تجريبية
        const sampleData = {
            services: [
                { id: 1, name: "أنظمة ERP", description: "أنظمة مالية، موارد بشرية، إدارة مخازن متكاملة وشاملة لجميع احتياجات مؤسستك. صممت خصيصًا لتلبي متطلبات الأعمال بمختلف أحجامها.", category: "أنظمة إدارة", icon: "fas fa-building", order: 1, status: "active" },
                { id: 2, name: "تطبيقات الجوال", description: "تطبيقات iOS و Android عالية الأداء مع واجهات مستخدم جذابة وسهلة الاستخدام. نضمن تجربة مستخدم فريدة وسلسة.", category: "تطبيقات", icon: "fas fa-mobile-alt", order: 2, status: "active" },
                { id: 3, name: "تطوير الويب", description: "مواقع إلكترونية ومتاجر تفاعلية بتقنيات حديثة وتصاميم متجاوبة. نبتكر حلولاً رقمية تعزز حضورك على الإنترنت.", category: "مواقع", icon: "fas fa-laptop-code", order: 3, status: "inactive" }
            ],
            stats: [
                { id: 1, title: "سنوات من الخبرة", value: "20+", icon: "fas fa-calendar-alt", color: "red", status: "active" },
                { id: 2, title: "مشروع ناجح", value: "100+", icon: "fas fa-project-diagram", color: "red", status: "active" },
                { id: 3, title: "عميل راضٍ", value: "50+", icon: "fas fa-smile", color: "red", status: "active" }
            ],
            clients: [
                { id: 1, name: "شركة التقنية المتطورة", description: "إحدى الشركات الرائدة في مجال التقنية والحلول البرمجية المتكاملة. تقدم خدماتها لعملاء من مختلف القطاعات.", location: "صنعاء", logo: "https://via.placeholder.com/50", order: 1, status: "active" },
                { id: 2, name: "مؤسسة النهضة التجارية", description: "مؤسسة تجارية رائدة تعمل في مجال الاستيراد والتصدير ولديها شبكة واسعة من العلاقات التجارية.", location: "عدن", logo: "https://via.placeholder.com/50", order: 2, status: "active" },
                { id: 3, name: "مجموعة الأعمال المتحدة", description: "مجموعة شركات تعمل في عدة مجالات منها العقارات، السياحة، والخدمات اللوجستية.", location: "تعز", logo: "https://via.placeholder.com/50", order: 3, status: "inactive" }
            ],
            messages: [
                { id: 1, name: "محمد أحمد", email: "mohamed@example.com", phone: "+967777777777", message: "أرغب في الحصول على معلومات إضافية حول خدمات تطوير تطبيقات الجوال التي تقدمونها، وما هي التكلفة التقريبية لمشروع تطبيق بمواصفات محددة.", date: "٢٠/١٠/٢٠٢٣", status: "unread" },
                { id: 2, name: "سارة عبدالله", email: "sara@example.com", phone: "+967777777778", message: "شكرًا لكم على الخدمات المميزة التي تقدمونها، أود الاستفسار عن إمكانية تطوير نظام إداري متكامل لشركتنا الناشئة.", date: "٢١/١٠/٢٠٢٣", status: "read" },
                { id: 3, name: "علي حسن", email: "ali@example.com", phone: "+967777777779", message: "أحتاج إلى مساعدة في تحسين موقعنا الإلكتروني الحالي وجعله أكثر تفاعلية وسرعة في التحميل، هل يمكنكم تقديم عرض سعر؟", date: "٢٢/١٠/٢٠٢٣", status: "read" }
            ]
        };

        // متغيرات لتتبع العناصر المراد حذفها
        let currentDeleteItem = null;
        let currentDeleteType = null;

        // تهيئة الرسوم البيانية
        document.addEventListener('DOMContentLoaded', function() {
            // رسم بياني للزيارات
            const visitsCtx = document.getElementById('visitsChart').getContext('2d');
            const visitsChart = new Chart(visitsCtx, {
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
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
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
 // تغيير الصفحات في القائمة الجانبية
        document.querySelectorAll('.sidebar-menu a').forEach(item => {
            item.addEventListener('click', function(e) {
                if (this.id === 'logoutBtn') {
                    e.preventDefault();
                    if (confirm('هل أنت متأكد من تسجيل الخروج؟')) {
                        alert('تم تسجيل الخروج بنجاح');
                        // هنا يمكن توجيه المستخدم لصفحة تسجيل الدخول
                    }
                    return;
                }
                
                e.preventDefault();
                const pageId = this.getAttribute('data-page');
                
                // إزالة النشاط من جميع العناصر
                document.querySelectorAll('.sidebar-menu a').forEach(link => {
                    link.classList.remove('active');
                });
                
                // إضافة النشاط للعنصر الحالي
                this.classList.add('active');
                
                // إخفاء جميع الصفحات
                document.querySelectorAll('.content-page').forEach(page => {
                    page.classList.remove('active');
                });
                
                // إظهار الصفحة المحددة
                document.getElementById(pageId).classList.add('active');
            });
        });

        // البحث
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            // هنا يمكن إضافة منطق البحث حسب الحاجة
            console.log('بحث عن:', searchTerm);
        });

            // رسم بياني للخدمات
            const servicesCtx = document.getElementById('servicesChart').getContext('2d');
            const servicesChart = new Chart(servicesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['أنظمة ERP', 'تطبيقات الجوال', 'تطوير الويب', 'حلول مخصصة'],
                    datasets: [{
                        data: [35, 25, 20, 20],
                        backgroundColor: [
                            '#d30909',
                            '#ff3838',
                            '#ff6b6b',
                            '#ff9f9f'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
// وظيفة البحث التلقائي
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.trim().toLowerCase();
    
    // إذا كان حقل البحث فارغًا، عرض كل العناصر
    if (searchTerm === '') {
        document.querySelectorAll('.content-page, .card, table, tr').forEach(el => {
            el.style.display = '';
        });
        document.querySelectorAll('.no-results').forEach(el => el.remove());
        document.querySelectorAll('.search-highlight').forEach(el => {
            const parent = el.parentNode;
            parent.replaceChild(document.createTextNode(el.textContent), el);
            parent.normalize();
        });
        return;
    }
    
    let foundInCurrentPage = false;
    
    // البحث في الصفحة النشطة فقط
    const activePage = document.querySelector('.content-page.active');
    if (activePage) {
        // البحث في الجداول داخل الصفحة النشطة
        activePage.querySelectorAll('table').forEach(table => {
            let hasMatch = false;
            table.querySelectorAll('tr').forEach((row, index) => {
                // تخطي رأس الجدول
                if (index === 0) {
                    row.style.display = '';
                    return;
                }
                
                const rowContent = row.textContent.toLowerCase();
                if (rowContent.includes(searchTerm)) {
                    row.style.display = '';
                    hasMatch = true;
                    foundInCurrentPage = true;
                    
                    // إبراز النص المطابق
                    row.querySelectorAll('td').forEach(cell => {
                        const originalContent = cell.innerHTML;
                        const cellText = cell.textContent;
                        if (cellText.toLowerCase().includes(searchTerm)) {
                            const regex = new RegExp(`(${searchTerm})`, 'gi');
                            cell.innerHTML = originalContent.replace(regex, '<span class="search-highlight">$1</span>');
                        }
                    });
                } else {
                    row.style.display = 'none';
                }
            });
            
            // إظهار رسالة إذا لم توجد نتائج في هذا الجدول
            const tableContainer = table.closest('.table-responsive') || table.parentElement;
            let noResultsMsg = tableContainer.querySelector('.no-results');
            
            if (!hasMatch) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.className = 'no-results';
                    noResultsMsg.textContent = 'لا توجد نتائج تطابق بحثك';
                    tableContainer.appendChild(noResultsMsg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        });
        
        // البحث في بطاقات الإحصائيات
        activePage.querySelectorAll('.stat-card').forEach(card => {
            const cardContent = card.textContent.toLowerCase();
            if (cardContent.includes(searchTerm)) {
                card.style.display = '';
                foundInCurrentPage = true;
                
                // إبراز النص المطابق
                card.querySelectorAll('.stat-info h3, .stat-info p').forEach(el => {
                    const originalContent = el.innerHTML;
                    const elText = el.textContent;
                    if (elText.toLowerCase().includes(searchTerm)) {
                        const regex = new RegExp(`(${searchTerm})`, 'gi');
                        el.innerHTML = originalContent.replace(regex, '<span class="search-highlight">$1</span>');
                    }
                });
            } else {
                card.style.display = 'none';
            }
        });
        
        // إذا لم يتم العثور على أي نتائج في الصفحة النشطة
        if (!foundInCurrentPage) {
            const mainContent = activePage.querySelector('.stats-cards') || activePage;
            let noResultsMsg = mainContent.querySelector('.no-results');
            
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-results';
                noResultsMsg.textContent = 'لا توجد نتائج تطابق بحثك في هذه الصفحة';
                noResultsMsg.style.textAlign = 'center';
                noResultsMsg.style.padding = '20px';
                noResultsMsg.style.color = '#6c757d';
                noResultsMsg.style.fontStyle = 'italic';
                
                if (activePage.querySelector('.stats-cards')) {
                    activePage.insertBefore(noResultsMsg, activePage.querySelector('.stats-cards'));
                } else {
                    activePage.appendChild(noResultsMsg);
                }
            }
        } else {
            // إزالة رسالة عدم العثور على نتائج إذا وجدنا نتائج
            const noResultsMsg = activePage.querySelector('.no-results');
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    }
});
            // إضافة أحداث للروابط في الشريط الجانبي
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.id === 'logoutBtn') {
                        e.preventDefault();
                        showAlert('تم تسجيل الخروج بنجاح', 'success');
                        return;
                    }
                    
                    e.preventDefault();
                    const pageId = this.getAttribute('data-page');
                    showPage(pageId);
                    
                    // تحديث الحالة النشطة في القائمة
                    document.querySelectorAll('.sidebar-menu a').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });

            // البحث
            document.getElementById('searchInput').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                // يمكن إضافة وظيفة البحث هنا
                console.log('بحث عن:', searchTerm);
            });

            // إضافة أحداث لأزرار الحذف
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const type = this.classList.contains('delete-service') ? 'service' :
                                this.classList.contains('delete-stat') ? 'stat' :
                                this.classList.contains('delete-client') ? 'client' :
                                this.classList.contains('delete-message') ? 'message' : 'item';
                    
                    showDeleteConfirmation(id, type);
                });
            });

            // إضافة أحداث لأزرار التعديل
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const type = this.classList.contains('edit-service') ? 'service' :
                                this.classList.contains('edit-stat') ? 'stat' :
                                this.classList.contains('edit-client') ? 'client' :
                                this.classList.contains('view-message') ? 'message' : 'item';
                    
                    if (type === 'message') {
                        showMessage(id);
                    } else if (type === 'service') {
                        editService(id);
                    } else if (type === 'stat') {
                        editStat(id);
                    } else if (type === 'client') {
                        editClient(id);
                    }
                });
            });

            // إضافة أحداث لأزرار الرد
            document.querySelectorAll('.reply-message').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    showReplyModal(id);
                });
            });

            // إضافة أحداث لأزرار الإضافة
            document.getElementById('addServiceBtn').addEventListener('click', function() {
                addService();
            });
            
            document.getElementById('addServiceBtn2').addEventListener('click', function() {
                addService();
            });
            
            document.getElementById('addStatBtn').addEventListener('click', function() {
                addStat();
            });
            
            document.getElementById('addClientBtn').addEventListener('click', function() {
                addClient();
            });

            // إغلاق النوافذ المنبثقة
            document.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.modal').style.display = 'none';
                });
            });

            // إغلاق النوافذ المنبثقة بالنقر خارجها
            window.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    e.target.style.display = 'none';
                }
            });

            // تأكيد الحذف
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                if (currentDeleteItem && currentDeleteType) {
                    deleteItem(currentDeleteItem, currentDeleteType);
                    document.getElementById('deleteConfirmModal').style.display = 'none';
                }
            });

            // إلغاء الحذف
            document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
                document.getElementById('deleteConfirmModal').style.display = 'none';
            });

            // إرسال الرد
            document.getElementById('sendReplyBtn').addEventListener('click', function() {
                const replyContent = document.getElementById('replyContent').value;
                if (replyContent.trim() === '') {
                    alert('يرجى كتابة رد قبل الإرسال');
                    return;
                }
                
                // هنا يمكن إضافة كود إرسال الرد
                showAlert('تم إرسال الرد بنجاح', 'success');
                document.getElementById('replyModal').style.display = 'none';
                document.getElementById('replyContent').value = '';
            });

            // معالجة نموذج الخدمة
            document.getElementById('serviceForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveService();
            });

            // معالجة نموذج الإحصائية
            document.getElementById('statForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveStat();
            });

            // معالجة نموذج العميل
            document.getElementById('clientForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveClient();
            });

            // معاينة صورة الشعار
            document.getElementById('clientLogo').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    document.getElementById('clientLogoName').textContent = file.name;
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('clientLogoPreview');
                        preview.innerHTML = `<img src="${e.target.result}" alt="معاينة الشعار">`;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // تعيين كل الرسائل كمقروءة
            document.getElementById('markAllReadBtn').addEventListener('click', function() {
                document.querySelectorAll('.unread-message').forEach(row => {
                    row.classList.remove('unread-message');
                    const statusCell = row.querySelector('.status');
                    statusCell.textContent = 'تم الرد';
                    statusCell.className = 'status status-completed';
                });
                showAlert('تم تعيين جميع الرسائل كمقروءة', 'success');
            });

            // حذف كل الرسائل
            document.getElementById('deleteAllMessagesBtn').addEventListener('click', function() {
                showDeleteConfirmation('all', 'all_messages');
            });
        });

        // عرض صفحة محددة
        function showPage(pageId) {
            document.querySelectorAll('.content-page').forEach(page => {
                page.classList.remove('active');
            });
            document.getElementById(pageId).classList.add('active');
        }

        // عرض رسالة تأكيد الحذف
        function showDeleteConfirmation(id, type) {
            currentDeleteItem = id;
            currentDeleteType = type;
            
            let message = "هل أنت متأكد من أنك تريد حذف هذا العنصر؟";
            
            if (type === 'all_messages') {
                message = "هل أنت متأكد من أنك تريد حذف جميع الرسائل؟ لا يمكن التراجع عن هذا الإجراء.";
            } else if (type === 'service') {
                message = "هل أنت متأكد من أنك تريد حذف هذه الخدمة؟";
            } else if (type === 'stat') {
                message = "هل أنت متأكد من أنك تريد حذف هذه الإحصائية؟";
            } else if (type === 'client') {
                message = "هل أنت متأكد من أنك تريد حذف هذا العميل؟";
            } else if (type === 'message') {
                message = "هل أنت متأكد من أنك تريد حذف هذه الرسالة؟";
            }
            
            document.getElementById('deleteConfirmMessage').textContent = message;
            document.getElementById('deleteConfirmModal').style.display = 'flex';
        }

        // حذف العنصر
        function deleteItem(id, type) {
            // في التطبيق الحقيقي، هنا سيتم إرسال طلب AJAX لحذف العنصر من قاعدة البيانات
            console.log(`حذف ${type} برقم ${id}`);
            
            let alertId = '';
            let successMessage = '';
            
            if (type === 'all_messages') {
                // حذف جميع الرسائل
                document.querySelectorAll('#messagesTable tbody tr').forEach(row => {
                    row.remove();
                });
                successMessage = 'تم حذف جميع الرسائل بنجاح';
                alertId = 'messageSuccessAlert';
            } else {
                // حذف عنصر محدد
                let selector = '';
                
                if (type === 'service') {
                    selector = `.delete-service[data-id="${id}"]`;
                    successMessage = 'تم حذف الخدمة بنجاح';
                    alertId = 'serviceSuccessAlert';
                } else if (type === 'stat') {
                    selector = `.delete-stat[data-id="${id}"]`;
                    successMessage = 'تم حذف الإحصائية بنجاح';
                    alertId = 'statSuccessAlert';
                } else if (type === 'client') {
                    selector = `.delete-client[data-id="${id}"]`;
                    successMessage = 'تم حذف العميل بنجاح';
                    alertId = 'clientSuccessAlert';
                } else if (type === 'message') {
                    selector = `.delete-message[data-id="${id}"]`;
                    successMessage = 'تم حذف الرسالة بنجاح';
                    alertId = 'messageSuccessAlert';
                }
                
                const element = document.querySelector(selector);
                if (element) {
                    const row = element.closest('tr');
                    if (row) {
                        row.remove();
                    }
                }
            }
            
            showAlert(successMessage, 'success', alertId);
        }

        // عرض رسالة
        function showMessage(id) {
            const message = sampleData.messages.find(m => m.id == id);
            if (message) {
                const modalBody = document.getElementById('messageModalBody');
                modalBody.innerHTML = `
                    <div class="message-details">
                        <div class="message-meta">
                            <span><strong>المرسل:</strong> ${message.name}</span>
                            <span><strong>التاريخ:</strong> ${message.date}</span>
                        </div>
                        <div class="message-meta">
                            <span><strong>البريد الإلكتروني:</strong> ${message.email}</span>
                            <span><strong>الهاتف:</strong> ${message.phone}</span>
                        </div>
                        <div>
                            <strong>الرسالة:</strong>
                            <p>${message.message}</p>
                        </div>
                    </div>
                    <div class="reply-section">
                        <button class="btn btn-reply" onclick="showReplyModal(${id})">الرد على الرسالة</button>
                    </div>
                `;
                
                document.getElementById('messageModal').style.display = 'flex';
                
                // تحديث حالة الرسالة إلى مقروءة
                const messageRow = document.querySelector(`.view-message[data-id="${id}"]`).closest('tr');
                if (messageRow) {
                    messageRow.classList.remove('unread-message');
                    const statusCell = messageRow.querySelector('.status');
                    statusCell.textContent = 'تم الرد';
                    statusCell.className = 'status status-completed';
                }
            }
        }

        // عرض نافذة الرد
        function showReplyModal(id) {
            const message = sampleData.messages.find(m => m.id == id);
            if (message) {
                const replyDetails = document.getElementById('replyMessageDetails');
                replyDetails.innerHTML = `
                    <div class="message-meta">
                        <span><strong>المرسل:</strong> ${message.name}</span>
                        <span><strong>التاريخ:</strong> ${message.date}</span>
                    </div>
                    <div class="message-meta">
                        <span><strong>البريد الإلكتروني:</strong> ${message.email}</span>
                        <span><strong>الهاتف:</strong> ${message.phone}</span>
                    </div>
                    <div>
                        <strong>الرسالة الأصلية:</strong>
                        <p>${message.message}</p>
                    </div>
                `;
                
                document.getElementById('replyModal').style.display = 'flex';
            }
        }

        // إضافة خدمة جديدة
        function addService() {
            document.getElementById('serviceModalTitle').textContent = 'إضافة خدمة جديدة';
            document.getElementById('serviceForm').reset();
            document.getElementById('serviceId').value = '';
            document.getElementById('serviceModal').style.display = 'flex';
        }

        // تعديل خدمة
        function editService(id) {
            const service = sampleData.services.find(s => s.id == id);
            if (service) {
                document.getElementById('serviceModalTitle').textContent = 'تعديل الخدمة';
                document.getElementById('serviceId').value = service.id;
                document.getElementById('serviceName').value = service.name;
                document.getElementById('serviceDescription').value = service.description;
                document.getElementById('serviceCategory').value = service.category;
                document.getElementById('serviceIcon').value = service.icon;
                document.getElementById('serviceOrder').value = service.order;
                document.getElementById('serviceStatus').value = service.status;
                document.getElementById('serviceModal').style.display = 'flex';
            }
        }

        // حفظ الخدمة
        function saveService() {
            const id = document.getElementById('serviceId').value;
            const name = document.getElementById('serviceName').value;
            const description = document.getElementById('serviceDescription').value;
            const category = document.getElementById('serviceCategory').value;
            const icon = document.getElementById('serviceIcon').value;
            const order = document.getElementById('serviceOrder').value;
            const status = document.getElementById('serviceStatus').value;
            
            // في التطبيق الحقيقي، هنا سيتم إرسال البيانات إلى الخادم
            console.log('حفظ الخدمة:', { id, name, description, category, icon, order, status });
            
            document.getElementById('serviceModal').style.display = 'none';
            showAlert(id ? 'تم تعديل الخدمة بنجاح' : 'تم إضافة الخدمة بنجاح', 'success', 'serviceSuccessAlert');
        }

        // إضافة إحصائية جديدة
        function addStat() {
            document.getElementById('statModalTitle').textContent = 'إضافة إحصائية جديدة';
            document.getElementById('statForm').reset();
            document.getElementById('statId').value = '';
            document.getElementById('statModal').style.display = 'flex';
        }

        // تعديل إحصائية
        function editStat(id) {
            const stat = sampleData.stats.find(s => s.id == id);
            if (stat) {
                document.getElementById('statModalTitle').textContent = 'تعديل الإحصائية';
                document.getElementById('statId').value = stat.id;
                document.getElementById('statTitle').value = stat.title;
                document.getElementById('statValue').value = stat.value;
                document.getElementById('statIcon').value = stat.icon;
                document.getElementById('statColor').value = stat.color;
                document.getElementById('statStatus').value = stat.status;
                document.getElementById('statModal').style.display = 'flex';
            }
        }

        // حفظ الإحصائية
        function saveStat() {
            const id = document.getElementById('statId').value;
            const title = document.getElementById('statTitle').value;
            const value = document.getElementById('statValue').value;
            const icon = document.getElementById('statIcon').value;
            const color = document.getElementById('statColor').value;
            const status = document.getElementById('statStatus').value;
            
            // في التطبيق الحقيقي، هنا سيتم إرسال البيانات إلى الخادم
            console.log('حفظ الإحصائية:', { id, title, value, icon, color, status });
            
            document.getElementById('statModal').style.display = 'none';
            showAlert(id ? 'تم تعديل الإحصائية بنجاح' : 'تم إضافة الإحصائية بنجاح', 'success', 'statSuccessAlert');
        }

        // إضافة عميل جديد
        function addClient() {
            document.getElementById('clientModalTitle').textContent = 'إضافة عميل جديد';
            document.getElementById('clientForm').reset();
            document.getElementById('clientId').value = '';
            document.getElementById('clientLogoPreview').innerHTML = '';
            document.getElementById('clientLogoName').textContent = 'لم يتم اختيار ملف';
            document.getElementById('clientModal').style.display = 'flex';
        }

        // تعديل عميل
        function editClient(id) {
            const client = sampleData.clients.find(c => c.id == id);
            if (client) {
                document.getElementById('clientModalTitle').textContent = 'تعديل العميل';
                document.getElementById('clientId').value = client.id;
                document.getElementById('clientName').value = client.name;
                document.getElementById('clientDescription').value = client.description;
                document.getElementById('clientLocation').value = client.location;
                document.getElementById('clientOrder').value = client.order;
                document.getElementById('clientStatus').value = client.status;
                
                document.getElementById('clientLogoPreview').innerHTML = `<img src="${client.logo}" alt="معاينة الشعار">`;
                document.getElementById('clientLogoName').textContent = 'تم تحميل الصورة';
                
                document.getElementById('clientModal').style.display = 'flex';
            }
        }

        // حفظ العميل
        function saveClient() {
            const id = document.getElementById('clientId').value;
            const name = document.getElementById('clientName').value;
            const description = document.getElementById('clientDescription').value;
            const location = document.getElementById('clientLocation').value;
            const order = document.getElementById('clientOrder').value;
            const status = document.getElementById('clientStatus').value;
            
            // في التطبيق الحقيقي، هنا سيتم إرسال البيانات إلى الخادم
            console.log('حفظ العميل:', { id, name, description, location, order, status });
            
            document.getElementById('clientModal').style.display = 'none';
            showAlert(id ? 'تم تعديل العميل بنجاح' : 'تم إضافة العميل بنجاح', 'success', 'clientSuccessAlert');
        }

        // عرض رسالة تنبيه
        function showAlert(message, type, elementId = null) {
            let alertElement;
            
            if (elementId) {
                alertElement = document.getElementById(elementId);
            } else {
                // إذا لم يتم تحديد عنصر محدد، استخدم العنصر الأول من النوع المطلوب
                const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
                alertElement = document.querySelector(`.${alertClass}`);
            }
            
            if (alertElement) {
                alertElement.textContent = message;
                alertElement.style.display = 'block';
                
                // إخفاء الرسالة بعد 5 ثوانٍ
                setTimeout(() => {
                    alertElement.style.display = 'none';
                }, 5000);
            }
        }
    </script>
</body>
</html>
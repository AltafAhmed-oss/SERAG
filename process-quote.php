<?php
// process-quote.php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// تمكين تسجيل الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

header('Content-Type: application/json; charset=utf-8');

// تفعيل جلسة إذا لم تكن مفعلة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// التحقق من أن الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة غير مسموح بها']);
    exit;
}

// التحقق من CSRF token إذا كان موجوداً
if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'رمز التحقق غير صالح']);
        exit;
    }
}

// التحقق من البيانات المطلوبة
$required = ['fullName', 'email', 'phone', 'projectDescription'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'يرجى ملء جميع الحقول المطلوبة']);
        exit;
    }
}

// التحقق من الخدمة المختارة
if (empty($_POST['service'])) {
    echo json_encode(['success' => false, 'message' => 'يرجى اختيار الخدمة المطلوبة']);
    exit;
}

// تنظيف البيانات
$fullName = trim($_POST['fullName']);
$companyName = trim($_POST['companyName'] ?? '');
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$service = trim($_POST['service']);
$projectScope = trim($_POST['projectScope'] ?? '');
$budget = trim($_POST['budget'] ?? '');
$timeline = trim($_POST['timeline'] ?? '');
$projectDescription = trim($_POST['projectDescription']);

// جمع المميزات
$features = isset($_POST['features']) ? $_POST['features'] : [];
if (is_array($features)) {
    $featuresString = implode(', ', array_map('trim', $features));
} else {
    $featuresString = $features;
}

// التحقق من صحة البريد الإلكتروني
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'يرجى إدخال بريد إلكتروني صحيح']);
    exit;
}

// التحقق من صحة رقم الهاتف (10-20 رقم)
if (!preg_match('/^[0-9]{10,20}$/', preg_replace('/[^0-9]/', '', $phone))) {
    echo json_encode(['success' => false, 'message' => 'يرجى إدخال رقم هاتف صحيح (10-20 رقم)']);
    exit;
}

// إنشاء رقم مرجعي فريد
$reference = 'SRG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

try {
    // الاتصال بقاعدة البيانات
    $database = new Database();
    $db = $database->getConnection();
    
    // 1. التحقق من وجود الجدول وإنشاؤه إذا لم يكن موجوداً
    $tableExists = $db->query("SHOW TABLES LIKE 'quote_requests'")->rowCount() > 0;
    
    if (!$tableExists) {
        // إنشاء الجدول
        $sql = "CREATE TABLE IF NOT EXISTS quote_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reference VARCHAR(50) NOT NULL UNIQUE,
            full_name VARCHAR(255) NOT NULL,
            company_name VARCHAR(255),
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            service_type VARCHAR(100),
            project_scope VARCHAR(100),
            budget_range VARCHAR(100),
            timeline VARCHAR(100),
            features TEXT,
            project_description TEXT NOT NULL,
            attachments TEXT,
            status ENUM('pending', 'reviewed', 'quoted', 'accepted', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        $db->exec($sql);
    }
    
    // 2. التحقق من عدم تكرار الطلب لنفس البريد الإلكتروني في آخر 24 ساعة
    $checkDuplicateQuery = "SELECT COUNT(*) as count FROM quote_requests 
                           WHERE email = :email 
                           AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    
    $checkStmt = $db->prepare($checkDuplicateQuery);
    $checkStmt->bindParam(':email', $email);
    $checkStmt->execute();
    $duplicateCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($duplicateCount > 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'لديك طلب نشط تم إرساله مؤخراً. يمكنك إرسال طلب جديد بعد 24 ساعة.'
        ]);
        exit;
    }
    
    // 3. التعامل مع الملفات المرفقة
    $attachmentsData = [];
    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
        $attachments = $_FILES['attachments'];
        $uploadDir = 'uploads/quote_attachments/';
        
        // إنشاء المجلد إذا لم يكن موجوداً
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $maxSize = 10 * 1024 * 1024; // 10MB
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'zip'];
        
        foreach ($attachments['name'] as $key => $name) {
            $tmpName = $attachments['tmp_name'][$key];
            $size = $attachments['size'][$key];
            $error = $attachments['error'][$key];
            
            if ($error === UPLOAD_ERR_OK && $size <= $maxSize) {
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                
                if (in_array($extension, $allowedTypes)) {
                    $newFilename = $reference . '_' . time() . '_' . $key . '.' . $extension;
                    $uploadPath = $uploadDir . $newFilename;
                    
                    if (move_uploaded_file($tmpName, $uploadPath)) {
                        $attachmentsData[] = [
                            'original_name' => $name,
                            'saved_name' => $newFilename,
                            'path' => $uploadPath
                        ];
                    }
                }
            }
        }
    }
    
    $attachmentsString = json_encode($attachmentsData, JSON_UNESCAPED_UNICODE);
    
    // 4. إدخال البيانات في قاعدة البيانات
    $query = "INSERT INTO quote_requests 
              (reference, full_name, company_name, email, phone, service_type, project_scope, budget_range, 
               timeline, features, project_description, attachments, status) 
              VALUES 
              (:reference, :full_name, :company_name, :email, :phone, :service_type, :project_scope, 
               :budget_range, :timeline, :features, :project_description, :attachments, 'pending')";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':reference', $reference);
    $stmt->bindParam(':full_name', $fullName);
    $stmt->bindParam(':company_name', $companyName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':service_type', $service);
    $stmt->bindParam(':project_scope', $projectScope);
    $stmt->bindParam(':budget_range', $budget);
    $stmt->bindParam(':timeline', $timeline);
    $stmt->bindParam(':features', $featuresString);
    $stmt->bindParam(':project_description', $projectDescription);
    $stmt->bindParam(':attachments', $attachmentsString);
    
    if ($stmt->execute()) {
        // تسجيل النشاط
        logActivity("طلب عرض سعر جديد: $reference من $fullName");
        
        // إرسال بريد إلكتروني
        sendQuoteEmail($reference, $fullName, $email, $companyName, $phone, $service, $projectScope, $budget, $timeline, $featuresString, $projectDescription);
        
        // إرسال بريد للمسؤول
        sendAdminNotification($reference, $fullName, $email, $companyName, $phone, $service, $projectDescription);
        
        // تخزين في الجلسة للعرض في صفحة التأكيد
        $_SESSION['last_quote'] = [
            'reference' => $reference,
            'name' => $fullName,
            'email' => $email,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode([
            'success' => true, 
            'message' => 'تم إرسال طلبك بنجاح', 
            'reference' => $reference,
            'redirect' => 'quote-confirmation.php'
        ]);
    } else {
        $errorInfo = $stmt->errorInfo();
        throw new Exception('خطأ في قاعدة البيانات: ' . $errorInfo[2]);
    }
    
} catch (PDOException $e) {
    // التعامل مع أخطاء قاعدة البيانات
    if ($e->getCode() == '42S02') { // الجدول غير موجود
        echo json_encode([
            'success' => false, 
            'message' => 'حدث خطأ في النظام. يرجى المحاولة مرة أخرى لاحقاً.'
        ]);
    } elseif ($e->getCode() == '23000') { // تكرار فريد
        echo json_encode([
            'success' => false, 
            'message' => 'رقم المرجع مكرر. يرجى المحاولة مرة أخرى.'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'حدث خطأ في قاعدة البيانات: ' . $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'حدث خطأ: ' . $e->getMessage()
    ]);
}

// دالة تسجيل النشاط
function logActivity($message) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // التحقق من وجود جدول activity_log
        $tableExists = $db->query("SHOW TABLES LIKE 'activity_log'")->rowCount() > 0;
        
        if (!$tableExists) {
            $sql = "CREATE TABLE IF NOT EXISTS activity_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                action VARCHAR(100),
                details TEXT,
                ip_address VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
            
            $db->exec($sql);
        }
        
        $query = "INSERT INTO activity_log (user_id, action, details, ip_address) 
                  VALUES (:user_id, :action, :details, :ip)";
        $stmt = $db->prepare($query);
        
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $action = 'quote_request';
        $details = $message;
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':ip', $ip);
        
        $stmt->execute();
    } catch (Exception $e) {
        // تجاهل أخطاء تسجيل النشاط
    }
}

// دالة إرسال بريد للمستخدم
function sendQuoteEmail($reference, $name, $email, $company, $phone, $service, $scope, $budget, $timeline, $features, $description) {
    try {
        $subject = "طلب عرض سعر جديد - $reference";
        
        $message = "
        <html>
        <head>
            <title>طلب عرض سعر جديد</title>
        </head>
        <body>
            <h2>شكراً لتواصلك مع سراج سوفت</h2>
            <p>عزيزي/عزيزتي <strong>$name</strong>,</p>
            <p>لقد استلمنا طلب عرض السعر الخاص بمشروعك وسنقوم بمراجعته من قبل فريقنا المختص.</p>
            
            <h3>تفاصيل طلبك:</h3>
            <p><strong>رقم المرجع:</strong> $reference</p>
            <p><strong>الاسم:</strong> $name</p>
            <p><strong>البريد الإلكتروني:</strong> $email</p>
            <p><strong>رقم الهاتف:</strong> $phone</p>
            <p><strong>الخدمة المطلوبة:</strong> $service</p>
            <p><strong>نطاق المشروع:</strong> $scope</p>
            
            <p>سيقوم فريقنا بالاتصال بك خلال 24 ساعة عمل لتقديم عرض السعر المخصص لمشروعك.</p>
            
            <p>مع تحيات،<br>فريق سراج سوفت</p>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: سراج سوفت <noreply@seragsoft.com>" . "\r\n";
        
        @mail($email, $subject, $message, $headers);
        
    } catch (Exception $e) {
        // تجاهل أخطاء البريد الإلكتروني
    }
}

// دالة إرسال بريد للمسؤول
function sendAdminNotification($reference, $name, $email, $company, $phone, $service, $description) {
    try {
        $subject = "طلب عرض سعر جديد - $reference";
        
        $message = "
        <html>
        <head>
            <title>طلب عرض سعر جديد من $name</title>
        </head>
        <body>
            <h2>طلب عرض سعر جديد</h2>
            <p><strong>رقم المرجع:</strong> $reference</p>
            <p><strong>الاسم:</strong> $name</p>
            <p><strong>الشركة:</strong> $company</p>
            <p><strong>البريد الإلكتروني:</strong> $email</p>
            <p><strong>رقم الهاتف:</strong> $phone</p>
            <p><strong>الخدمة:</strong> $service</p>
            <p><strong>وصف المشروع:</strong><br>" . nl2br($description) . "</p>
            <p>يرجى مراجعة الطلب في لوحة التحكم.</p>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        $adminEmail = 'admin@seragsoft.com';
        @mail($adminEmail, $subject, $message, $headers);
        
    } catch (Exception $e) {
        // تجاهل أخطاء البريد الإلكتروني
    }
}
?>
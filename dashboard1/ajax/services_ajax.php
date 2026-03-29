<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح بالدخول']);
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        getService();
        break;
    case 'save':
        saveService();
        break;
    case 'delete':
        deleteService();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'إجراء غير معروف']);
}

function getService() {
    global $database;
    
    $id = intval($_GET['id'] ?? 0);
    if ($id == 0) {
        echo json_encode(['success' => false, 'message' => 'معرف غير صالح']);
        return;
    }
    
    // التحقق من وجود الجدول أولاً
    $table_check = $database->query("SHOW TABLES LIKE 'services'");
    if (!$table_check || $table_check->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'جدول الخدمات غير موجود']);
        return;
    }
    
    $result = $database->query("SELECT * FROM services WHERE id = $id");
    
    if ($result && $result->num_rows > 0) {
        $service = $result->fetch_assoc();
        echo json_encode($service);
    } else {
        echo json_encode(['success' => false, 'message' => 'الخدمة غير موجودة']);
    }
}

function saveService() {
    global $database;
    
    // التحقق من وجود الجدول أولاً
    $table_check = $database->query("SHOW TABLES LIKE 'services'");
    if (!$table_check || $table_check->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'جدول الخدمات غير موجود']);
        return;
    }
    
    $id = intval($_POST['id'] ?? 0);
    $title = $database->escape_string($_POST['title'] ?? '');
    $description = $database->escape_string($_POST['description'] ?? '');
    $icon = $database->escape_string($_POST['icon'] ?? 'fas fa-cog');
    $is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 1;
    
    if (empty($title) || empty($description)) {
        echo json_encode(['success' => false, 'message' => 'الرجاء ملء جميع الحقول المطلوبة']);
        return;
    }
    
    if ($id > 0) {
        // تحديث الخدمة
        $sql = "UPDATE services SET 
                title = '$title', 
                description = '$description', 
                icon = '$icon', 
                is_active = $is_active,
                updated_at = NOW()
                WHERE id = $id";
    } else {
        // إضافة خدمة جديدة
        // التحقق من وجود الأعمدة في الجدول
        $columns = $database->get_table_columns('services');
        
        if (in_array('is_active', $columns)) {
            $sql = "INSERT INTO services (title, description, icon, is_active, created_at, updated_at) 
                    VALUES ('$title', '$description', '$icon', $is_active, NOW(), NOW())";
        } else {
            $sql = "INSERT INTO services (title, description, icon, created_at, updated_at) 
                    VALUES ('$title', '$description', '$icon', NOW(), NOW())";
        }
    }
    
    if ($database->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'تم حفظ الخدمة بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء الحفظ: ' . $database->conn->error]);
    }
}

function deleteService() {
    global $database;
    
    $id = intval($_POST['id'] ?? 0);
    if ($id == 0) {
        echo json_encode(['success' => false, 'message' => 'معرف غير صالح']);
        return;
    }
    
    // التحقق من وجود الجدول أولاً
    $table_check = $database->query("SHOW TABLES LIKE 'services'");
    if (!$table_check || $table_check->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'جدول الخدمات غير موجود']);
        return;
    }
    
    $sql = "DELETE FROM services WHERE id = $id";
    
    if ($database->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'تم حذف الخدمة بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء الحذف: ' . $database->conn->error]);
    }
}
?>
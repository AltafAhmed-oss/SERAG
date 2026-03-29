<?php
// ajax/contacts_ajax.php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح بالدخول']);
    exit();
}

// التحقق من نوع المستخدم
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'editor') {
    echo json_encode(['success' => false, 'message' => 'ليس لديك صلاحية لهذا الإجراء']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        getContactMessage();
        break;
    case 'delete_all':
        deleteAllContactMessages();
        break;
    case 'mark_read':
        markContactAsRead();
        break;
    case 'delete_all_messages': // إضافة هذا الإجراء
        deleteAllContactMessages();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'إجراء غير معروف']);
}

function getContactMessage() {
    global $database;
    
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'معرف غير صالح']);
        return;
    }
    
    $id = intval($_GET['id']);
    
    // استخدام prepared statement لمنع SQL Injection
    $stmt = $database->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = $result->fetch_assoc();
            echo json_encode($message);
        } else {
            echo json_encode(['success' => false, 'message' => 'الرسالة غير موجودة']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ في جلب البيانات']);
    }
    
    $stmt->close();
}

function deleteAllContactMessages() {
    global $database;
    
    // التحقق من الصلاحية (فقط للمسؤولين)
    if ($_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'ليس لديك صلاحية لحذف جميع الرسائل']);
        exit();
    }
    
    // عد الرسائل قبل الحذف
    $count_result = $database->query("SELECT COUNT(*) as total FROM contacts");
    $count_row = $count_result->fetch_assoc();
    $total_messages = $count_row['total'];
    
    // حذف جميع الرسائل
    $sql = "DELETE FROM contacts";
    
    if ($database->query($sql)) {
        echo json_encode([
            'success' => true, 
            'message' => "تم حذف جميع الرسائل بنجاح ($total_messages رسالة)",
            'deleted_count' => $total_messages
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء حذف جميع الرسائل: ' . $database->error]);
    }
}

function markContactAsRead() {
    global $database;
    
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'معرف غير صالح']);
        return;
    }
    
    $id = intval($_POST['id']);
    $status = $_POST['status'] ?? 'read';
    
    // التحقق إذا كان الجدول يحتوي على عمود status
    $check_column = $database->query("SHOW COLUMNS FROM contacts LIKE 'status'");
    
    if ($check_column && $check_column->num_rows > 0) {
        // تحديث حالة الرسالة
        $stmt = $database->prepare("UPDATE contacts SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'تم تحديث حالة الرسالة']);
        } else {
            echo json_encode(['success' => false, 'message' => 'حدث خطأ في تحديث الحالة']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'جدول الرسائل لا يحتوي على عمود الحالة']);
    }
}
?>
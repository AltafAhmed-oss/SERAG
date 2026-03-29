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
        getUser();
        break;
    case 'save':
        saveUser();
        break;
    case 'delete':
        deleteUser();
        break;
    case 'change_status':
        changeUserStatus();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'إجراء غير معروف']);
}

function getUser() {
    global $database;
    
    $id = intval($_GET['id']);
    $result = $database->query("SELECT id, username, email, role, status FROM users WHERE id = $id");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // إخفاء كلمة المرور
        unset($user['password']);
        echo json_encode($user);
    } else {
        echo json_encode(['success' => false, 'message' => 'المستخدم غير موجود']);
    }
}

function saveUser() {
    global $database;
    
    $id = intval($_POST['id']);
    $username = $database->escape_string($_POST['username']);
    $email = $database->escape_string($_POST['email']);
    $role = $database->escape_string($_POST['role']);
    $status = $database->escape_string($_POST['status']);
    
    // التحقق من عدم تكرار اسم المستخدم أو البريد الإلكتروني
    $check_sql = "SELECT id FROM users WHERE (username = '$username' OR email = '$email') AND id != $id";
    $check_result = $database->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'اسم المستخدم أو البريد الإلكتروني موجود مسبقاً']);
        return;
    }
    
    if ($id > 0) {
        // تحديث المستخدم
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET 
                    username = '$username', 
                    email = '$email', 
                    password = '$password', 
                    role = '$role', 
                    status = '$status' 
                    WHERE id = $id";
        } else {
            $sql = "UPDATE users SET 
                    username = '$username', 
                    email = '$email', 
                    role = '$role', 
                    status = '$status' 
                    WHERE id = $id";
        }
    } else {
        // إضافة مستخدم جديد
        if (empty($_POST['password'])) {
            echo json_encode(['success' => false, 'message' => 'كلمة المرور مطلوبة']);
            return;
        }
        
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, role, status) 
                VALUES ('$username', '$email', '$password', '$role', '$status')";
    }
    
    if ($database->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'تم حفظ المستخدم بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء الحفظ']);
    }
}

function deleteUser() {
    global $database;
    
    $id = intval($_POST['id']);
    
    // منع حذف المستخدم الحالي
    if ($id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'لا يمكن حذف حسابك الخاص']);
        return;
    }
    
    $sql = "DELETE FROM users WHERE id = $id";
    
    if ($database->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'تم حذف المستخدم بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء الحذف']);
    }
}

function changeUserStatus() {
    global $database;
    
    $id = intval($_POST['id']);
    $status = $database->escape_string($_POST['status']);
    
    // منع تعطيل المستخدم الحالي
    if ($id == $_SESSION['user_id'] && $status == 'inactive') {
        echo json_encode(['success' => false, 'message' => 'لا يمكن تعطيل حسابك الخاص']);
        return;
    }
    
    $sql = "UPDATE users SET status = '$status' WHERE id = $id";
    
    if ($database->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'تم تغيير حالة المستخدم بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء تغيير الحالة']);
    }
}
?>
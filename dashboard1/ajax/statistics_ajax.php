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
        getStatistic();
        break;
    case 'save':
        saveStatistic();
        break;
    case 'delete':
        deleteStatistic();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'إجراء غير معروف']);
}

function getStatistic() {
    global $database;
    
    $id = intval($_GET['id']);
    $result = $database->query("SELECT * FROM statistics WHERE id = $id");
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['success' => false, 'message' => 'الإحصائية غير موجودة']);
    }
}

function saveStatistic() {
    global $database;
    
    $id = intval($_POST['id']);
    $title = $database->escape_string($_POST['title']);
    $value = $database->escape_string($_POST['value']);
    $icon = $database->escape_string($_POST['icon']);
    $color = $database->escape_string($_POST['color']);
    $status = $database->escape_string($_POST['status']);
    
    if ($id > 0) {
        // تحديث الإحصائية
        $sql = "UPDATE statistics SET 
                title = '$title', 
                value = '$value', 
                icon = '$icon', 
                color = '$color', 
                status = '$status' 
                WHERE id = $id";
    } else {
        // إضافة إحصائية جديدة
        $sql = "INSERT INTO statistics (title, value, icon, color, status) 
                VALUES ('$title', '$value', '$icon', '$color', '$status')";
    }
    
    if ($database->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'تم حفظ الإحصائية بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء الحفظ']);
    }
}

function deleteStatistic() {
    global $database;
    
    $id = intval($_POST['id']);
    $sql = "DELETE FROM statistics WHERE id = $id";
    
    if ($database->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'تم حذف الإحصائية بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء الحذف']);
    }
}
?>
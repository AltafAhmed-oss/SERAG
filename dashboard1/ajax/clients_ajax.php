<?php
// ajax/clients_ajax.php
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
        getClient();
        break;
    case 'save':
        saveClient();
        break;
    case 'delete':
        deleteClient();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'إجراء غير معروف']);
}

function getClient() {
    global $database;
    
    $id = intval($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'رقم العميل غير صحيح']);
        return;
    }
    
    $result = $database->query("SELECT * FROM clients WHERE id = $id");
    
    if ($result && $result->num_rows > 0) {
        $client = $result->fetch_assoc();
        
        // إرجاع البيانات مع نجاح العملية
        $response = [
            'success' => true,
            'id' => $client['id'],
            'name' => $client['name'] ?? '',
            'description' => $client['description'] ?? '',
            'location' => $client['location'] ?? '',
            'logo' => $client['logo'] ?? '',
            'display_order' => $client['display_order'] ?? 1,
            'status' => $client['status'] ?? 'active'
        ];
        
        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'العميل غير موجود']);
    }
}

function saveClient() {
    global $database;
    
    $id = intval($_POST['id'] ?? 0);
    $name = $database->escape_string($_POST['name'] ?? '');
    $description = $database->escape_string($_POST['description'] ?? '');
    $location = $database->escape_string($_POST['location'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 1);
    $status = $database->escape_string($_POST['status'] ?? 'active');
    
    // التحقق من البيانات المطلوبة
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'اسم العميل مطلوب']);
        return;
    }
    
    // معالجة رفع الصورة
    $logo = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $uploadDir = '../uploads/clients/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['logo']['name']);
        $targetPath = $uploadDir . $fileName;
        
        // التحقق من نوع الملف
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($fileInfo, $_FILES['logo']['tmp_name']);
        finfo_close($fileInfo);
        
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                $logo = $fileName;
            }
        }
    }
    
    // التحقق مما إذا كان ID موجوداً للتحديث أو إضافة جديد
    if ($id > 0) {
        // تحقق أولاً من وجود العميل
        $checkResult = $database->query("SELECT id FROM clients WHERE id = $id");
        if ($checkResult && $checkResult->num_rows > 0) {
            // تحديث العميل
            if ($logo) {
                // احتفظ بالشعار القديم أولاً لحذفه لاحقاً
                $oldLogoResult = $database->query("SELECT logo FROM clients WHERE id = $id");
                if ($oldLogoResult && $oldLogoRow = $oldLogoResult->fetch_assoc()) {
                    $oldLogo = $oldLogoRow['logo'];
                    if (!empty($oldLogo) && file_exists("../uploads/clients/" . $oldLogo)) {
                        unlink("../uploads/clients/" . $oldLogo);
                    }
                }
                
                $sql = "UPDATE clients SET 
                        name = '$name', 
                        description = '$description', 
                        location = '$location', 
                        logo = '$logo', 
                        display_order = $display_order, 
                        status = '$status' 
                        WHERE id = $id";
            } else {
                $sql = "UPDATE clients SET 
                        name = '$name', 
                        description = '$description', 
                        location = '$location', 
                        display_order = $display_order, 
                        status = '$status' 
                        WHERE id = $id";
            }
            $message = 'تم تحديث العميل بنجاح';
        } else {
            echo json_encode(['success' => false, 'message' => 'العميل غير موجود للتحديث']);
            return;
        }
    } else {
        // إضافة عميل جديد
        $sql = "INSERT INTO clients (name, description, location, logo, display_order, status) 
                VALUES ('$name', '$description', '$location', '$logo', $display_order, '$status')";
        $message = 'تم إضافة العميل بنجاح';
    }
    
    if ($database->query($sql)) {
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء الحفظ: ' . $database->error]);
    }
}

function deleteClient() {
    global $database;
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'رقم العميل غير صحيح']);
        return;
    }
    
    // احصل على الشعار أولاً لحذفه
    $result = $database->query("SELECT logo FROM clients WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        $logo = $row['logo'];
        if (!empty($logo) && file_exists("../uploads/clients/" . $logo)) {
            unlink("../uploads/clients/" . $logo);
        }
    }
    
    $sql = "DELETE FROM clients WHERE id = $id";
    
    if ($database->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'تم حذف العميل بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء الحذف: ' . $database->error]);
    }
}
?>
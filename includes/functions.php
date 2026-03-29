<?php
// دالة لتحميل الصور
function uploadImage($file, $target_dir) {
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // التحقق من أن الملف صورة
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["success" => false, "message" => "الملف ليس صورة."];
    }
    
    // التحقق من حجم الملف
    if ($file["size"] > MAX_FILE_SIZE) {
        return ["success" => false, "message" => "حجم الملف كبير جداً."];
    }
    
    // السماح بأنواع ملفات محددة
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return ["success" => false, "message" => "نوع الملف غير مدعوم."];
    }
    
    // إنشاء اسم فريد للملف
    $new_filename = uniqid() . '.' . $imageFileType;
    $target_path = $target_dir . $new_filename;
    
    // تحميل الملف
    if (move_uploaded_file($file["tmp_name"], $target_path)) {
        return ["success" => true, "filename" => $new_filename];
    } else {
        return ["success" => false, "message" => "حدث خطأ أثناء تحميل الملف."];
    }
}

// دالة للحماية من حقن SQL
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// دالة لإعادة توجيه المستخدم
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// دالة لعرض الرسائل
function displayMessage($type, $message) {
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
        ' . $message . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

require_once 'database.php';

class AdminFunctions {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // العملاء
    public function getClients() {
        $query = "SELECT * FROM clients ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addClient($name, $logo) {
        $query = "INSERT INTO clients (name, logo) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$name, $logo]);
    }
    
    public function updateClient($id, $name, $logo = null) {
        if ($logo) {
            $query = "UPDATE clients SET name = ?, logo = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$name, $logo, $id]);
        } else {
            $query = "UPDATE clients SET name = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$name, $id]);
        }
    }
    
    public function deleteClient($id) {
        $query = "DELETE FROM clients WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
    
    public function getClientLogo($id) {
        $query = "SELECT logo FROM clients WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }
    
    // الإحصائيات
    public function getStatistics() {
        $query = "SELECT * FROM statistics ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addStatistic($title, $value, $icon) {
        $query = "INSERT INTO statistics (title, value, icon) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$title, $value, $icon]);
    }
    
    public function updateStatistic($id, $title, $value, $icon) {
        $query = "UPDATE statistics SET title = ?, value = ?, icon = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$title, $value, $icon, $id]);
    }
    
    public function deleteStatistic($id) {
        $query = "DELETE FROM statistics WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
    // إضافة هذه الدوال داخل class AdminFunctions في functions.php

// دالة للحصول على الإحصائيات للداشبورد
public function getDashboardStats() {
    $stats = [];
    
    // عدد العملاء
    $query = "SELECT COUNT(*) as count FROM clients";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    $stats['total_clients'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // عدد الخدمات النشطة
    $query = "SELECT COUNT(*) as count FROM services WHERE is_active = 1";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    $stats['total_services'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // عدد الرسائل غير المقروءة
    // أولاً تحقق من وجود عمود status في جدول messages
    $query = "SHOW COLUMNS FROM messages LIKE 'status'";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $query = "SELECT COUNT(*) as count FROM messages WHERE status = 'unread'";
    } else {
        $query = "SELECT COUNT(*) as count FROM messages";
    }
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    $stats['total_messages'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // عدد المستخدمين النشطين
    $query = "SELECT COUNT(*) as count FROM users WHERE is_active = 1";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    return $stats;
}

// دالة للحصول على الرسائل الأخيرة
public function getRecentMessages($limit = 5) {
    $query = "SELECT * FROM messages ORDER BY created_at DESC LIMIT :limit";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// دالة للحصول على الخدمات الأخيرة
public function getRecentServices($limit = 5) {
    $query = "SELECT id, title as name, description, icon, is_active, created_at FROM services ORDER BY created_at DESC LIMIT :limit";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // إضافة حقل category افتراضي لكل خدمة
    foreach ($services as &$service) {
        $service['category'] = 'خدمات تقنية';
        $service['status'] = $service['is_active'] == 1 ? 'active' : 'inactive';
    }
    
    return $services;
}

// دالة للحصول على عملاء حديثين
public function getRecentClients($limit = 5) {
    $query = "SELECT * FROM clients ORDER BY created_at DESC LIMIT :limit";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}



?>
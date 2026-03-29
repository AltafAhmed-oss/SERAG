<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

echo "<h2>التحقق من هيكل جدول الخدمات</h2>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // التحقق من وجود الجدول
    $table_exists = $db->query("SHOW TABLES LIKE 'services'")->rowCount() > 0;
    
    if ($table_exists) {
        echo "<p style='color: green;'>✅ جدول الخدمات موجود</p>";
        
        // الحصول على أعمدة الجدول
        $columns = $db->query("SHOW COLUMNS FROM services")->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>أعمدة الجدول:</h3>";
        echo "<ul>";
        foreach ($columns as $column) {
            echo "<li>{$column['Field']} ({$column['Type']})</li>";
        }
        echo "</ul>";
        
        // التحقق من وجود عمود is_active
        $has_is_active = false;
        foreach ($columns as $column) {
            if ($column['Field'] == 'is_active') {
                $has_is_active = true;
                break;
            }
        }
        
        if ($has_is_active) {
            echo "<p style='color: green;'>✅ عمود is_active موجود</p>";
        } else {
            echo "<p style='color: red;'>❌ عمود is_active غير موجود</p>";
        }
        
        // عد الخدمات
        $services_count = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
        echo "<p>عدد الخدمات: $services_count</p>";
        
    } else {
        echo "<p style='color: red;'>❌ جدول الخدمات غير موجود</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>العودة إلى الصفحة الرئيسية</a></p>";
?>
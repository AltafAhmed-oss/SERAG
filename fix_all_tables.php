<?php
// fix_all_tables.php
require_once 'config/database.php';

echo "<h2>إصلاح جميع الجداول</h2>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
    th { background-color: #f2f2f2; }
</style>";

// قائمة بجميع الجداول والأعمدة المطلوبة
$tables_schema = [
    'users' => [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'username' => 'VARCHAR(50) NOT NULL UNIQUE',
        'email' => 'VARCHAR(100) NOT NULL UNIQUE',
        'password' => 'VARCHAR(255) NOT NULL',
        'role' => "ENUM('admin','editor','viewer') DEFAULT 'viewer'",
        'status' => "ENUM('active','inactive') DEFAULT 'active'",
        'last_login' => 'TIMESTAMP NULL',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'is_active' => 'TINYINT(1) DEFAULT 1'
    ],
    
    'services' => [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'name' => 'VARCHAR(100) NOT NULL',
        'description' => 'TEXT NOT NULL',
        'category' => 'VARCHAR(50) NOT NULL',
        'icon' => 'VARCHAR(50) NOT NULL',
        'display_order' => 'INT DEFAULT 1',
        'status' => "ENUM('active','inactive') DEFAULT 'active'",
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ],
    
    'clients' => [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'name' => 'VARCHAR(100) NOT NULL',
        'description' => 'TEXT NOT NULL',
        'location' => 'VARCHAR(100) NOT NULL',
        'logo' => 'VARCHAR(255)',
        'display_order' => 'INT DEFAULT 1',
        'status' => "ENUM('active','inactive') DEFAULT 'active'",
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ],
    
    'messages' => [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'name' => 'VARCHAR(100) NOT NULL',
        'email' => 'VARCHAR(100) NOT NULL',
        'phone' => 'VARCHAR(20)',
        'message' => 'TEXT NOT NULL',
        'status' => "ENUM('unread','read') DEFAULT 'unread'",
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ],
    
    'statistics' => [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'title' => 'VARCHAR(100) NOT NULL',
        'value' => 'VARCHAR(50) NOT NULL',
        'icon' => 'VARCHAR(50) NOT NULL',
        'color' => 'VARCHAR(20) NOT NULL',
        'status' => "ENUM('active','inactive') DEFAULT 'active'",
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ],
    
    'site_settings' => [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'setting_key' => 'VARCHAR(100) NOT NULL UNIQUE',
        'setting_value' => 'TEXT NOT NULL',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ]
];

// التحقق من وجود كل جدول وإصلاحه
foreach ($tables_schema as $table_name => $columns) {
    echo "<h3>جدول: $table_name</h3>";
    
    // التحقق من وجود الجدول
    $result = $database->query("SHOW TABLES LIKE '$table_name'");
    if ($result->num_rows == 0) {
        // إنشاء الجدول
        $sql = "CREATE TABLE $table_name (";
        $columns_sql = [];
        foreach ($columns as $col_name => $col_def) {
            $columns_sql[] = "$col_name $col_def";
        }
        $sql .= implode(", ", $columns_sql);
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if ($database->query($sql)) {
            echo "<p class='success'>✓ تم إنشاء جدول $table_name</p>";
        } else {
            echo "<p class='error'>✗ فشل إنشاء جدول $table_name: " . $database->conn->error . "</p>";
        }
    } else {
        echo "<p class='success'>✓ الجدول $table_name موجود</p>";
        
        // التحقق من أعمدة الجدول وإضافة المفقود
        $existing_columns = [];
        $result = $database->query("SHOW COLUMNS FROM $table_name");
        while ($row = $result->fetch_assoc()) {
            $existing_columns[$row['Field']] = true;
        }
        
        foreach ($columns as $col_name => $col_def) {
            if (!isset($existing_columns[$col_name])) {
                // إضافة العمود المفقود
                $sql = "ALTER TABLE $table_name ADD COLUMN $col_name $col_def";
                if ($database->query($sql)) {
                    echo "<p class='success'>✓ تم إضافة عمود $col_name</p>";
                } else {
                    echo "<p class='error'>✗ فشل إضافة عمود $col_name: " . $database->conn->error . "</p>";
                }
            } else {
                echo "<p class='warning'>- عمود $col_name موجود</p>";
            }
        }
    }
    
    // عرض محتويات الجدول
    echo "<h4>محتويات جدول $table_name:</h4>";
    $result = $database->query("SELECT * FROM $table_name LIMIT 5");
    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr>";
        // عرض أسماء الأعمدة
        $fields = $result->fetch_fields();
        foreach ($fields as $field) {
            echo "<th>" . $field->name . "</th>";
        }
        echo "</tr>";
        
        // عرض البيانات
        $result->data_seek(0); // إعادة تعيين المؤشر
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>لا توجد بيانات في هذا الجدول</p>";
    }
    
    echo "<hr>";
}

// إضافة بيانات اختبارية
echo "<h2>إضافة بيانات اختبارية</h2>";

// بيانات الخدمات
$services_data = [
    ['name' => 'أنظمة ERP', 'description' => 'أنظمة إدارة متكاملة للمؤسسات', 'category' => 'أنظمة إدارة', 'icon' => 'fas fa-building', 'display_order' => 1],
    ['name' => 'تطبيقات الجوال', 'description' => 'تطوير تطبيقات iOS و Android', 'category' => 'تطبيقات', 'icon' => 'fas fa-mobile-alt', 'display_order' => 2],
    ['name' => 'تطوير الويب', 'description' => 'تصميم وتطوير مواقع الويب', 'category' => 'مواقع', 'icon' => 'fas fa-laptop-code', 'display_order' => 3],
    ['name' => 'حلول مخصصة', 'description' => 'تطوير حلول برمجية مخصصة', 'category' => 'مخصص', 'icon' => 'fas fa-cogs', 'display_order' => 4]
];

foreach ($services_data as $service) {
    $name = $database->escape_string($service['name']);
    $desc = $database->escape_string($service['description']);
    $cat = $database->escape_string($service['category']);
    $icon = $database->escape_string($service['icon']);
    $order = $service['display_order'];
    
    $check = $database->query("SELECT id FROM services WHERE name = '$name'");
    if ($check->num_rows == 0) {
        $sql = "INSERT INTO services (name, description, category, icon, display_order, status) 
                VALUES ('$name', '$desc', '$cat', '$icon', $order, 'active')";
        if ($database->query($sql)) {
            echo "<p class='success'>✓ تم إضافة خدمة: $name</p>";
        }
    }
}

// بيانات العملاء
$clients_data = [
    ['name' => 'شركة التقنية المتطورة', 'description' => 'شركة رائدة في مجال التقنية', 'location' => 'صنعاء', 'display_order' => 1],
    ['name' => 'مؤسسة النهضة التجارية', 'description' => 'مؤسسة تجارية رائدة', 'location' => 'عدن', 'display_order' => 2],
    ['name' => 'مجموعة الأعمال المتحدة', 'description' => 'مجموعة شركات متعددة المجالات', 'location' => 'تعز', 'display_order' => 3]
];

foreach ($clients_data as $client) {
    $name = $database->escape_string($client['name']);
    $desc = $database->escape_string($client['description']);
    $loc = $database->escape_string($client['location']);
    $order = $client['display_order'];
    
    $check = $database->query("SELECT id FROM clients WHERE name = '$name'");
    if ($check->num_rows == 0) {
        $sql = "INSERT INTO clients (name, description, location, display_order, status) 
                VALUES ('$name', '$desc', '$loc', $order, 'active')";
        if ($database->query($sql)) {
            echo "<p class='success'>✓ تم إضافة عميل: $name</p>";
        }
    }
}

// بيانات الإحصائيات
$stats_data = [
    ['title' => 'سنوات من الخبرة', 'value' => '20+', 'icon' => 'fas fa-calendar-alt', 'color' => 'red'],
    ['title' => 'مشروع ناجح', 'value' => '100+', 'icon' => 'fas fa-project-diagram', 'color' => 'blue'],
    ['title' => 'عميل راضٍ', 'value' => '50+', 'icon' => 'fas fa-smile', 'color' => 'green'],
    ['title' => 'جائزة حصلنا عليها', 'value' => '25+', 'icon' => 'fas fa-award', 'color' => 'orange']
];

foreach ($stats_data as $stat) {
    $title = $database->escape_string($stat['title']);
    $value = $database->escape_string($stat['value']);
    $icon = $database->escape_string($stat['icon']);
    $color = $database->escape_string($stat['color']);
    
    $check = $database->query("SELECT id FROM statistics WHERE title = '$title'");
    if ($check->num_rows == 0) {
        $sql = "INSERT INTO statistics (title, value, icon, color, status) 
                VALUES ('$title', '$value', '$icon', '$color', 'active')";
        if ($database->query($sql)) {
            echo "<p class='success'>✓ تم إضافة إحصائية: $title</p>";
        }
    }
}

// بيانات الإعدادات
$settings_data = [
    ['site_name' => 'سراج سوفت'],
    ['site_description' => 'شركة رائدة في مجال البرمجيات والتقنية'],
    ['contact_email' => 'info@seragsoft.com'],
    ['contact_phone' => '+967777777777'],
    ['contact_address' => 'صنعاء، اليمن'],
    ['facebook_url' => 'https://facebook.com/seragsoft'],
    ['twitter_url' => 'https://twitter.com/seragsoft']
];

foreach ($settings_data as $setting) {
    foreach ($setting as $key => $value) {
        $key_safe = $database->escape_string($key);
        $value_safe = $database->escape_string($value);
        
        $check = $database->query("SELECT id FROM site_settings WHERE setting_key = '$key_safe'");
        if ($check->num_rows == 0) {
            $sql = "INSERT INTO site_settings (setting_key, setting_value) VALUES ('$key_safe', '$value_safe')";
            $database->query($sql);
        }
    }
}

// التحقق من وجود مستخدم admin
$check_admin = $database->query("SELECT id FROM users WHERE username = 'admin'");
if ($check_admin->num_rows == 0) {
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, role, status) 
            VALUES ('admin', 'admin@seragsoft.com', '$password_hash', 'admin', 'active')";
    if ($database->query($sql)) {
        echo "<p class='success'>✓ تم إنشاء المستخدم admin بكلمة المرور admin123</p>";
    }
}

// التحقق من وجود مستخدم altaf
$check_altaf = $database->query("SELECT id FROM users WHERE username = 'altaf'");
if ($check_altaf->num_rows == 0) {
    $password_hash = password_hash('123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, role, status) 
            VALUES ('altaf', 'altaf@seragsoft.com', '$password_hash', 'editor', 'active')";
    if ($database->query($sql)) {
        echo "<p class='success'>✓ تم إنشاء المستخدم altaf بكلمة المرور 123</p>";
    }
}

echo "<hr>";
echo "<h2>التحقق النهائي</h2>";

// التحقق من جميع الاستعلامات الأساسية
$test_queries = [
    "SELECT COUNT(*) as count FROM users WHERE status = 'active'" => "المستخدمون النشطون",
    "SELECT COUNT(*) as count FROM services WHERE status = 'active'" => "الخدمات النشطة",
    "SELECT COUNT(*) as count FROM clients WHERE status = 'active'" => "العملاء النشطين",
    "SELECT COUNT(*) as count FROM messages WHERE status = 'unread'" => "الرسائل غير المقروءة",
    "SELECT COUNT(*) as count FROM statistics WHERE status = 'active'" => "الإحصائيات النشطة"
];

foreach ($test_queries as $sql => $description) {
    $result = $database->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>$description: <strong>" . $row['count'] . "</strong></p>";
    } else {
        echo "<p class='error'>✗ فشل استعلام: $description</p>";
    }
}

echo "<br><a href='index.php' style='background: #d30909; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold; display: inline-block;'>🚀 ابدأ باستخدام لوحة التحكم</a>";
?>
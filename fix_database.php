<?php
// fix_database.php - لإصلاح مشاكل قاعدة البيانات
require_once 'config/database.php';

echo "<h2>إصلاح قاعدة البيانات</h2>";

// 1. التحقق من وجود المستخدم admin
$result = $database->query("SELECT id FROM users WHERE username = 'admin'");
if ($result->num_rows == 0) {
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $database->query("INSERT INTO users (username, password, email, role, is_active) VALUES ('admin', '$password_hash', 'admin@seragsoft.com', 'admin', 1)");
    echo "<p style='color: green;'>✓ تم إنشاء المستخدم admin</p>";
} else {
    echo "<p style='color: green;'>✓ المستخدم admin موجود</p>";
}

// 2. إضافة المستخدم altaf
$result = $database->query("SELECT id FROM users WHERE username = 'altaf'");
if ($result->num_rows == 0) {
    $password_hash = password_hash('123', PASSWORD_DEFAULT);
    $database->query("INSERT INTO users (username, password, email, role, is_active) VALUES ('altaf', '$password_hash', 'altaf@seragsoft.com', 'editor', 1)");
    echo "<p style='color: green;'>✓ تم إنشاء المستخدم altaf</p>";
} else {
    echo "<p style='color: green;'>✓ المستخدم altaf موجود</p>";
}

// 3. عرض جميع المستخدمين
echo "<h3>قائمة المستخدمين:</h3>";
$users = $database->query("SELECT id, username, email, role, is_active FROM users");
echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th></tr>";
while ($user = $users->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['username']}</td>";
    echo "<td>{$user['email']}</td>";
    echo "<td>{$user['role']}</td>";
    echo "<td>" . ($user['is_active'] ? 'نشط' : 'غير نشط') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><a href='login.php' style='background: #d30909; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>اذهب إلى صفحة الدخول</a>";
?>
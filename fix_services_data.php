<?php
// fix_services_data.php
require_once 'config/database.php';

echo "<h2>إصلاح بيانات الخدمات</h2>";
echo "<style>
    .success { color: green; }
    .error { color: red; }
</style>";

// 1. تحديث الأسماء الفارغة
$sql = "UPDATE services 
        SET name = CONCAT('خدمة ', id)
        WHERE name IS NULL OR TRIM(name) = ''";

if ($database->query($sql)) {
    echo "<p class='success'>✓ تم تحديث الأسماء الفارغة</p>";
}

// 2. جلب البيانات بعد الإصلاح
$sql = "SELECT id, name, description FROM services";
$result = $database->query($sql);

echo "<h3>البيانات بعد الإصلاح:</h3>";
echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>الاسم</th><th>الوصف</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . (strlen($row['description']) > 50 ? substr($row['description'], 0, 50) . '...' : $row['description']) . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<br><a href='index.php?page=services' style='background: #d30909; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>عرض صفحة الخدمات</a>";
?>
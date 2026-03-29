<?php
// التحقق من وجود صور العملاء
$clients_dir = 'assets/img/clients/';
echo "<h2>التحقق من صور العملاء في: $clients_dir</h2>";

if (is_dir($clients_dir)) {
    $found_images = 0;
    
    for ($i = 1; $i <= 24; $i++) {
        // التحقق من وجود الصورة بجميع الصيغ المحتملة
        $formats = ['png', 'jpg', 'jpeg', 'gif'];
        $image_found = false;
        $image_path = '';
        
        foreach ($formats as $format) {
            $possible_path = $clients_dir . "client{$i}.{$format}";
            if (file_exists($possible_path)) {
                $image_found = true;
                $image_path = $possible_path;
                break;
            }
        }
        
        if ($image_found) {
            echo "<p style='color: green;'>✅ وجدت: client{$i} - <a href='$image_path' target='_blank'>معاينة</a></p>";
            $found_images++;
        } else {
            echo "<p style='color: red;'>❌ مفقود: client{$i}</p>";
        }
    }
    
    echo "<h3>النتيجة: وجدت {$found_images} من أصل 24 صورة</h3>";
    
} else {
    echo "<p style='color: red;'>المجلد غير موجود: $clients_dir</p>";
}

echo "<p><a href='index.php'>العودة إلى الصفحة الرئيسية</a></p>";
?>
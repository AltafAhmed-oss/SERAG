<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استلام البيانات مع حماية من الحقن
    $name = $database->escape_string($_POST['name'] ?? '');
    $email = $database->escape_string($_POST['email'] ?? '');
    $phone = $database->escape_string($_POST['phone'] ?? '');
    $message = $database->escape_string($_POST['message'] ?? '');
    
    // التحقق من البيانات
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'جميع الحقول المطلوبة يجب ملؤها']);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني غير صحيح']);
        exit();
    }
    
    // حفظ الرسالة في قاعدة البيانات
    $sql = "INSERT INTO messages (name, email, phone, message, status) VALUES ('$name', '$email', '$phone', '$message', 'unread')";
    
    if ($database->query($sql)) {
        // إرسال إشعار بالبريد الإلكتروني (اختياري)
        sendNotificationEmail($name, $email, $phone, $message);
        
        echo json_encode(['success' => true, 'message' => 'تم إرسال رسالتك بنجاح! سنقوم بالرد عليك في أقرب وقت.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة غير مسموحة']);
}

function sendNotificationEmail($name, $email, $phone, $message) {
    // يمكنك إضافة كود إرسال البريد الإلكتروني هنا
    // هذا مثال باستخدام دالة mail البسيطة
    /*
    $to = "admin@sirajsoft.com";
    $subject = "رسالة جديدة من $name";
    $body = "
    اسم المرسل: $name
    البريد الإلكتروني: $email
    الهاتف: $phone
    
    الرسالة:
    $message
    ";
    
    mail($to, $subject, $body);
    */
}
?>
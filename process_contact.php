<?php
header('Content-Type: application/json; charset=utf-8');

// بيانات الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "seragsoft_db";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'فشل الاتصال بقاعدة البيانات']));
}

// التحقق من إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استلام البيانات مع حماية من الحقن
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $message = $conn->real_escape_string($_POST['message'] ?? '');

    // استعلام الإدخال
    $sql = "INSERT INTO contacts (name, email, phone, message) VALUES ('$name', '$email', '$phone', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'تم إرسال رسالتك بنجاح!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة غير مسموحة']);
}

$conn->close();
?>
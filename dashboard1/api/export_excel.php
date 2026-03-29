<?php
// api/export_excel_simple.php

// المسار الجذري
$rootPath = dirname(dirname(__DIR__));

// تحميل ملفات التكوين
require_once $rootPath . '/includes/config.php';
require_once $rootPath . '/includes/database1.php';

// الاتصال بقاعدة البيانات
$database = new DatabaseConnection();
$conn = $database->getConnection();

// جمع معايير البحث
$status = $_GET['status'] ?? '';
$service = $_GET['service'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';

// بناء الاستعلام
$where = ['1=1'];
$params = [];

if ($status) { $where[] = "status = ?"; $params[] = $status; }
if ($service) { $where[] = "service_type = ?"; $params[] = $service; }
if ($date_from) { $where[] = "DATE(created_at) >= ?"; $params[] = $date_from; }
if ($date_to) { $where[] = "DATE(created_at) <= ?"; $params[] = $date_to; }
if ($search) {
    $where[] = "(reference LIKE ? OR full_name LIKE ? OR email LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$whereClause = implode(' AND ', $where);
$query = "SELECT * FROM quote_requests WHERE $whereClause ORDER BY created_at DESC";

// تنفيذ الاستعلام
$stmt = $conn->prepare($query);
$stmt->execute($params);

// التحقق من المكتبة
$libraryPath = $rootPath . '/libraries/phpspreadsheet/vendor/autoload.php';

if (file_exists($libraryPath)) {
    // استخدام Excel مع الأسماء الكاملة
    require_once $libraryPath;
    
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // رؤوس الأعمدة
    $sheet->setCellValue('A1', 'رقم التسلسل');
    $sheet->setCellValue('B1', 'الرقم المرجعي');
    $sheet->setCellValue('C1', 'الاسم');
    $sheet->setCellValue('D1', 'البريد');
    $sheet->setCellValue('E1', 'الخدمة');
    $sheet->setCellValue('F1', 'الحالة');
    
    // بيانات
    $row = 2;
    $serial = 1;
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sheet->setCellValue('A' . $row, $serial++);
        $sheet->setCellValue('B' . $row, $data['reference'] ?? '');
        $sheet->setCellValue('C' . $row, $data['full_name'] ?? '');
        $sheet->setCellValue('D' . $row, $data['email'] ?? '');
        $sheet->setCellValue('E' . $row, $data['service_type'] ?? '');
        $sheet->setCellValue('F' . $row, $data['status'] ?? '');
        $row++;
    }
    
    // إرسال
    $filename = 'طلبات_عرض_السعر_' . date('Y-m-d') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    
} else {
    // استخدام CSV
    $filename = 'طلبات_عرض_السعر_' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['رقم التسلسل', 'الرقم المرجعي', 'الاسم', 'البريد', 'الخدمة', 'الحالة']);
    
    $serial = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $serial++,
            $row['reference'] ?? '',
            $row['full_name'] ?? '',
            $row['email'] ?? '',
            $row['service_type'] ?? '',
            $row['status'] ?? ''
        ]);
    }
    
    fclose($output);
}

exit;
?>
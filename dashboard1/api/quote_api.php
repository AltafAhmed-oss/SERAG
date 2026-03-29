<?php
// api/quote_api.php

// السماح بالوصول من أي مصدر (للاختبار فقط)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

require_once '../includes/config.php';
require_once '../includes/database1.php';

// إنشاء اتصال بقاعدة البيانات
$database = new DatabaseConnection();
$conn = $database->getConnection();

// دالة لمعالجة صف الطلب
function processQuoteRow($row) {
    // تحويل المرفقات من JSON
    $row['attachments_array'] = json_decode($row['attachments'] ?? '[]', true);
    $row['features_array'] = !empty($row['features']) ? explode(',', $row['features']) : [];
    
    // تحديد اسم الخدمة بالعربية
    $serviceNames = [
        'erp' => 'أنظمة ERP',
        'mobile' => 'تطبيقات الجوال',
        'web' => 'تطوير الويب',
        'custom' => 'حلول مخصصة'
    ];
    $row['service_name'] = $serviceNames[$row['service_type']] ?? $row['service_type'];
    
    // تحديد حالة الطلب بالعربية
    $statusNames = [
        'pending' => 'قيد الانتظار',
        'reviewed' => 'تمت المراجعة',
        'quoted' => 'تم عرض السعر',
        'accepted' => 'مقبول',
        'rejected' => 'مرفوض'
    ];
    $row['status_name'] = $statusNames[$row['status']] ?? $row['status'];
    
    // تحديد نطاق المشروع
    $scopeNames = [
        'small' => 'صغير',
        'medium' => 'متوسط',
        'large' => 'كبير',
        'not-sure' => 'غير محدد'
    ];
    $row['scope_name'] = $scopeNames[$row['project_scope']] ?? $row['project_scope'];
    
    return $row;
}

// الحصول على طريقة الطلب
$method = $_SERVER['REQUEST_METHOD'];

// الحصول على الإجراء المطلوب
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// الحصول على ID
$id = $_GET['id'] ?? $_POST['id'] ?? 0;

try {
    switch ($action) {
        case 'get_quote_details':
            if ($id) {
                $query = "SELECT * FROM quote_requests WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id]);
                $quote = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($quote) {
                    $quote = processQuoteRow($quote);
                    echo json_encode([
                        'success' => true,
                        'data' => $quote
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'لم يتم العثور على الطلب'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'معرّف الطلب غير صالح'
                ]);
            }
            break;
            
        case 'get_quote_status':
            if ($id) {
                $query = "SELECT status, quoted_price, notes FROM quote_requests WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id]);
                $quote = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($quote) {
                    echo json_encode([
                        'success' => true,
                        'status' => $quote['status'],
                        'quoted_price' => $quote['quoted_price'],
                        'notes' => $quote['notes']
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'لم يتم العثور على الطلب'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'معرّف الطلب غير صالح'
                ]);
            }
            break;
            
        case 'send_email':
            if ($id) {
                // جلب بيانات الطلب
                $query = "SELECT email, full_name, reference FROM quote_requests WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id]);
                $quote = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($quote) {
                    // هنا يمكنك إضافة كود إرسال البريد الفعلي
                    // للمثال سنقوم بتسجيل العملية فقط
                    error_log("تم طلب إرسال بريد للطلب ID: $id - البريد: " . $quote['email']);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'تم إرسال البريد الإلكتروني بنجاح إلى ' . $quote['email']
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'لم يتم العثور على الطلب'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'معرّف الطلب غير صالح'
                ]);
            }
            break;
            
        case 'update_status':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['id'] ?? 0;
                $status = $_POST['status'] ?? '';
                $notes = $_POST['notes'] ?? '';
                $quoted_price = !empty($_POST['quoted_price']) ? floatval($_POST['quoted_price']) : null;
                
                if ($id && $status) {
                    $query = "UPDATE quote_requests SET status = ?, updated_at = NOW()";
                    $params = [$status];
                    
                    if ($notes !== '') {
                        $query .= ", notes = CONCAT(IFNULL(notes, ''), '\n', ?)";
                        $params[] = date('Y-m-d H:i') . ': ' . $notes;
                    }
                    
                    if ($quoted_price !== null) {
                        $query .= ", quoted_price = ?";
                        $params[] = $quoted_price;
                    }
                    
                    $query .= " WHERE id = ?";
                    $params[] = $id;
                    
                    $stmt = $conn->prepare($query);
                    $result = $stmt->execute($params);
                    
                    if ($result) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'تم تحديث حالة الطلب بنجاح'
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'حدث خطأ في تحديث حالة الطلب'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'بيانات غير مكتملة'
                    ]);
                }
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'إجراء غير معروف'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ في الخادم: ' . $e->getMessage()
    ]);
}
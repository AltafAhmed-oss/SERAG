<?php
// pages/messages.php

// استعلام آمن لجلب الرسائل من جدول contacts مع الترقيم الصحيح
$sql = "SELECT * FROM contacts ORDER BY create_at DESC";
$messages_result = $database->query($sql);

// التحقق من النتيجة
if (!$messages_result) {
    echo "<div class='alert alert-error'>خطأ في جلب بيانات الرسائل. الرجاء التحقق من قاعدة البيانات.</div>";
    $messages = [];
} else {
    $messages = [];
    $counter = 1;
    while ($row = $messages_result->fetch_assoc()) {
        $row['counter'] = $counter;
        $messages[] = $row;
        $counter++;
    }
}
?>

<!-- تضمين jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- تضمين SweetAlert2 للنوافذ الجميلة -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
/* أنماط الجدول - محاذاة على سطر واحد */
.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    table-layout: fixed;
}

.data-table th {
    background-color: #f8f9fa;
    padding: 12px 15px;
    text-align: center;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.data-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.data-table tbody tr:hover {
    background-color: #f9f9f9;
}

/* تحسين تنسيق الأزرار لتكون متجاورة على سطر واحد */
.action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 40px;
}

.btn-group {
    display: flex;
    gap: 8px;
    flex-wrap: nowrap;
    justify-content: center;
    align-items: center;
}

/* أنماط الأزرار - متناسقة على سطر واحد */
.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    min-width: 70px;
    height: 32px;
    position: relative;
    z-index: 1;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
    min-width: 65px;
    height: 30px;
}

.btn-view {
    background: #dc3545;
    color: white;
    border: 1px solid #dc3545;
}

.btn-view:hover {
    background: #dc3545;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
}

.btn-reply {
    background: #dc3545;
    color: white;
    border: 1px solid #dc3545;
}

.btn-reply:hover {
    background: #dc3545;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
}

.btn-delete {
    background: #dc3545;
    color: white;
    border: 1px solid #dc3545;
}

.btn-delete:hover {
    background: #c82333;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
}

.btn-delete-all {
    background: #dc3545;
    color: white;
    border: 1px solid #dc3545;
    padding: 8px 16px;
    font-weight: 500;
    font-size: 14px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    z-index: 1;
}

.btn-delete-all:hover {
    background: #c82333;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
}

.btn-close {
    background: #6c757d;
    color: white;
    border: 1px solid #6c757d;
}

.btn-close:hover {
    background: #5a6268;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(108, 117, 125, 0.2);
}

/* منع تداخل النوافذ */
.swal2-container {
    z-index: 999999 !important;
}

/* تحسين العرض على الأجهزة الصغيرة */
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }
    
    .data-table {
        min-width: 700px;
    }
    
    .btn-group {
        gap: 5px;
    }
    
    .btn-sm {
        min-width: 55px;
        font-size: 11px;
        padding: 4px 8px;
        height: 28px;
    }
}

/* أنماط تفاصيل الرسالة في النافذة */
.message-details {
    text-align: right;
    direction: rtl;
}

.message-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 15px;
}

.meta-item {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 200px;
}

.meta-item strong {
    color: #666;
    margin-bottom: 5px;
    font-size: 14px;
}

.meta-item span {
    color: #333;
    font-weight: 500;
}

.message-content {
    margin-top: 20px;
}

.message-content strong {
    display: block;
    margin-bottom: 10px;
    color: #666;
}

.message-text {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    max-height: 200px;
    overflow-y: auto;
    line-height: 1.6;
    text-align: right;
}

/* أنماط SweetAlert2 المخصصة */
.swal2-popup {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    direction: rtl;
    z-index: 999999 !important;
}

.swal2-title {
    color: #333;
    font-weight: 600;
    font-size: 18px;
    text-align: right;
}

.swal2-html-container {
    text-align: right;
    font-size: 14px;
}

.swal2-confirm {
    background-color: #dc3545 !important;
    border: none !important;
    border-radius: 4px !important;
    padding: 8px 20px !important;
    font-size: 14px !important;
    position: relative;
    z-index: 1;
}

.swal2-confirm:hover {
    background-color: #c82333 !important;
}

.swal2-cancel {
    background-color: #6c757d !important;
    border: none !important;
    border-radius: 4px !important;
    padding: 8px 20px !important;
    font-size: 14px !important;
    position: relative;
    z-index: 1;
}

.swal2-cancel:hover {
    background-color: #5a6268 !important;
}

/* تنسيق زر لا توجد نتائج */
.no-results-message {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.no-results-message i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.3;
}

.no-results-message h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #495057;
}

.no-results-message p {
    font-size: 1rem;
    color: #6c757d;
}

/* تحسين محتوى الخلايا */
.data-table td[title] {
    cursor: help;
}

/* محاذاة عمود الرسالة */
.data-table td:nth-child(5) {
    text-align: right !important;
}

/* إزالة الأنماط الافتراضية للأزرار */
button {
    font-family: inherit;
    outline: none;
}

/* منع ظهور نوافذ متعددة */
.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) {
    overflow-y: hidden !important;
}

/* تنسيق لحل مشكلة التراكب */
.swal2-container-custom {
    z-index: 999999 !important;
}

/* إخفاء الأيقونات أثناء التحميل */
.swal2-show .swal2-icon {
    display: none;
}

/* تنسيقات إضافية لمنع التداخل */
#messages {
    position: relative;
    z-index: 1;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 0 10px;
}

.table-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    border: 1px solid transparent;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}
</style>

<div id="messages" class="content-page active">
    <div id="messageAlerts"></div>
    
    <div class="card">
        <div class="table-header">
            <div class="table-title">رسائل التواصل</div>
            <div>
                <button class="btn btn-delete-all" id="deleteAllMessagesBtn">
                    <i class="fas fa-trash"></i> حذف الكل
                </button>
            </div>
        </div>
        
        <?php if (empty($messages)): ?>
        <div class="no-results-message">
            <i class="fas fa-envelope"></i>
            <h3>لا توجد رسائل</h3>
            <p>لم يتم استلام أي رسائل بعد.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table id="messagesTable" class="data-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">الاسم</th>
                        <th width="15%">البريد الإلكتروني</th>
                        <th width="12%">الهاتف</th>
                        <th width="20%">الرسالة</th>
                        <th width="13%">التاريخ</th>
                        <th width="20%">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="messagesTableBody">
                    <?php foreach ($messages as $message): ?>
                    <?php 
                    $created_at = isset($message['create_at']) ? $message['create_at'] : date('Y-m-d H:i:s');
                    $formatted_date = date('d/m/Y H:i', strtotime($created_at));
                    $short_message = strlen($message['message']) > 60 ? substr($message['message'], 0, 60) . '...' : $message['message'];
                    ?>
                    <tr id="message-row-<?php echo $message['id']; ?>" data-id="<?php echo $message['id']; ?>">
                        <td class="counter" style="text-align: center;"><?php echo $message['counter']; ?></td>
                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                        <td><?php echo htmlspecialchars($message['phone'] ?? ''); ?></td>
                        <td title="<?php echo htmlspecialchars($message['message']); ?>">
                            <?php echo htmlspecialchars($short_message); ?>
                        </td>
                        <td><?php echo $formatted_date; ?></td>
                        <td class="action-buttons">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-view view-message-btn" 
                                        data-id="<?php echo $message['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($message['name']); ?>"
                                        data-email="<?php echo htmlspecialchars($message['email']); ?>"
                                        data-phone="<?php echo htmlspecialchars($message['phone']); ?>"
                                        data-message="<?php echo htmlspecialchars($message['message']); ?>"
                                        data-date="<?php echo $formatted_date; ?>">
                                    <i class="fas fa-eye"></i> عرض
                                </button>
                                <button class="btn btn-sm btn-reply reply-message-btn" 
                                        data-id="<?php echo $message['id']; ?>"
                                        data-email="<?php echo htmlspecialchars($message['email']); ?>"
                                        data-name="<?php echo htmlspecialchars($message['name']); ?>">
                                    <i class="fas fa-reply"></i> رد
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // تهيئة SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // 1. حدث حذف جميع الرسائل
    $('#deleteAllMessagesBtn').on('click', function(e) {
        e.preventDefault();
        deleteAllMessagesConfirm();
    });

    // 2. حدث عرض الرسالة
    $(document).on('click', '.view-message-btn', function(e) {
        e.preventDefault();
        
        const messageId = $(this).data('id');
        const messageData = {
            id: messageId,
            name: $(this).data('name'),
            email: $(this).data('email'),
            phone: $(this).data('phone'),
            message: $(this).data('message'),
            date: $(this).data('date')
        };
        
        showMessageModal(messageData);
    });

    // 3. حدث الرد على الرسالة
    $(document).on('click', '.reply-message-btn', function(e) {
        e.preventDefault();
        
        const email = $(this).data('email');
        const name = $(this).data('name');
        
        if (!email) {
            showAlert('لا يوجد بريد إلكتروني للرد', 'error');
            return;
        }
        
        const subject = encodeURIComponent('رد على استفسارك');
        const body = encodeURIComponent(`عزيزي/عزيزتي ${name},\n\nشكراً لتواصلك معنا.\n\nبخصوص استفسارك:\n\nمع خالص التقدير،`);
        
        window.open(`mailto:${email}?subject=${subject}&body=${body}`, '_blank');
    });
});

// وظيفة تأكيد حذف جميع الرسائل
function deleteAllMessagesConfirm() {
    Swal.fire({
        title: 'تأكيد الحذف',
        html: 'هل أنت متأكد من حذف <strong>جميع الرسائل</strong>؟<br><small>لا يمكن استعادة الرسائل بعد الحذف.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، احذف الكل',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        focusCancel: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: {
            container: 'swal2-container-custom'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            deleteAllMessages();
        }
    });
}

// وظيفة عرض نافذة الرسالة - بدون زر حذف
function showMessageModal(messageData) {
    const modalHtml = `
        <div class="message-details">
            <div class="message-meta">
                <div class="meta-item">
                    <strong>الاسم:</strong>
                    <span>${messageData.name || 'غير معروف'}</span>
                </div>
                <div class="meta-item">
                    <strong>التاريخ:</strong>
                    <span>${messageData.date || 'غير محدد'}</span>
                </div>
            </div>
            <div class="message-meta">
                <div class="meta-item">
                    <strong>البريد الإلكتروني:</strong>
                    <span>${messageData.email || 'غير معروف'}</span>
                </div>
                <div class="meta-item">
                    <strong>الهاتف:</strong>
                    <span>${messageData.phone || 'غير متوفر'}</span>
                </div>
            </div>
            <div class="message-content">
                <strong>الرسالة:</strong>
                <div class="message-text">
                    ${messageData.message.replace(/\n/g, '<br>') || 'لا توجد رسالة'}
                </div>
            </div>
        </div>
    `;
    
    Swal.fire({
        title: 'عرض الرسالة',
        html: modalHtml,
        width: '700px',
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonText: 'رد عبر البريد',
        showCancelButton: true,
        cancelButtonText: 'إغلاق',
        reverseButtons: true,
        focusCancel: true,
        allowOutsideClick: true,
        allowEscapeKey: true,
        customClass: {
            container: 'swal2-container-custom'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const subject = encodeURIComponent('رد على استفسارك');
            const body = encodeURIComponent(`عزيزي/عزيزتي ${messageData.name},\n\nشكراً لتواصلك معنا.\n\nبخصوص استفسارك:\n\nمع خالص التقدير،`);
            window.open(`mailto:${messageData.email}?subject=${subject}&body=${body}`, '_blank');
        }
    });
}

// وظيفة حذف جميع الرسائل
function deleteAllMessages() {
    $.ajax({
        url: 'ajax/contacts_ajax.php',
        type: 'POST',
        data: { action: 'delete_all_messages' },
        dataType: 'json',
        beforeSend: function() {
            Swal.fire({
                title: 'جاري الحذف...',
                text: 'يرجى الانتظار',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                customClass: {
                    container: 'swal2-container-custom'
                }
            });
        },
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                showAlert('تم حذف جميع الرسائل بنجاح', 'success');
                
                // إزالة جميع الصفوف
                $('#messagesTableBody tr').fadeOut(300, function() {
                    $(this).remove();
                });
                
                // إضافة رسالة عدم وجود بيانات
                setTimeout(() => {
                    $('#messagesTableBody').html(`
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d;">
                                <i class="fas fa-envelope" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i><br>
                                لا توجد رسائل
                            </td>
                        </tr>
                    `);
                }, 350);
            } else {
                showAlert(response.message || 'حدث خطأ أثناء الحذف', 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            showAlert('حدث خطأ في الاتصال بالخادم', 'error');
            console.error('AJAX Error:', error);
        }
    });
}

// وظيفة تحديث أرقام الصفوف
function updateRowNumbers() {
    $('#messagesTableBody tr').each(function(index) {
        $(this).find('.counter').text(index + 1);
    });
}

// وظيفة عرض التنبيهات
function showAlert(message, type) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    Toast.fire({
        icon: type,
        title: message
    });
}
</script>
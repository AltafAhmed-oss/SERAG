<?php
// pages/clients.php
// التحقق من أن المستخدم مسجل الدخول
if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-error'>غير مصرح بالدخول</div>";
    return;
}

// استعلام آمن لجلب العملاء
$sql = "SHOW COLUMNS FROM clients";
$columns_result = $database->query($sql);
$has_display_order = false;
$has_status = false;
$has_logo = false;

if ($columns_result) {
    while ($column = $columns_result->fetch_assoc()) {
        if ($column['Field'] == 'display_order') $has_display_order = true;
        if ($column['Field'] == 'status') $has_status = true;
        if ($column['Field'] == 'logo') $has_logo = true;
    }
}

// بناء الاستعلام المناسب
if ($has_display_order && $has_status) {
    $order_by = "display_order, created_at DESC";
} elseif ($has_display_order) {
    $order_by = "display_order DESC";
} else {
    $order_by = "id DESC";
}

$sql = "SELECT * FROM clients ORDER BY $order_by";

// تنفيذ الاستعلام
$clients_result = $database->query($sql);

if (!$clients_result) {
    // محاولة استعلام أبسط
    $sql = "SELECT * FROM clients ORDER BY id DESC";
    $clients_result = $database->query($sql);
}

// التحقق من النتيجة
if (!$clients_result) {
    echo "<div class='alert alert-error'>خطأ في جلب بيانات العملاء. الرجاء التحقق من قاعدة البيانات.</div>";
    $clients = [];
} else {
    $clients = [];
    while ($row = $clients_result->fetch_assoc()) {
        $clients[] = $row;
    }
}
?>

<div id="clients" class="content-page active">
    <div id="clientAlerts"></div>
    
    <div class="card">
        <div class="table-header">
            <div class="table-title">إدارة العملاء</div>
        </div>
        
        <?php if (empty($clients)): ?>
        <div class="no-results-message">
            <i class="fas fa-users"></i>
            <h3>لا يوجد عملاء</h3>
            <p>لم يتم إضافة أي عملاء بعد.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الشعار</th>
                        <th>اسم العميل</th>
                        <th>الوصف</th>
                        <th>الموقع</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $index => $client): ?>
                    <?php 
                    $logo = isset($client['logo']) ? $client['logo'] : '';
                    $display_order = isset($client['display_order']) ? $client['display_order'] : 1;
                    $status = isset($client['status']) ? $client['status'] : 'active';
                    $status_text = ($status == 'active') ? 'نشط' : 'غير نشط';
                    ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td>
                            <?php if (!empty($logo) && file_exists("../uploads/clients/" . $logo)): ?>
                            <img src="uploads/clients/<?php echo htmlspecialchars($logo); ?>" 
                                 alt="<?php echo htmlspecialchars($client['name']); ?>"
                                 style="width: 50px; height: 50px; object-fit: contain;">
                            <?php else: ?>
                            <div style="width: 50px; height: 50px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                <i class="fas fa-building" style="color: #999;"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($client['name']); ?></td>
                        <td style="max-width: 250px;">
                            <?php 
                            $desc = $client['description'];
                            echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($client['location'] ?? 'غير محدد'); ?></td>
                        <td><?php echo $display_order; ?></td>
                        <td>
                            <?php if ($status == 'active'): ?>
                            <span style="background: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 20px; font-size: 13px;">نشط</span>
                            <?php else: ?>
                            <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 20px; font-size: 13px;">غير نشط</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-edit edit-client" data-id="<?php echo $client['id']; ?>" data-type="client" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; margin-right: 5px;">تعديل</button>
                            <button class="btn btn-sm btn-delete delete-client" data-id="<?php echo $client['id']; ?>" data-type="client" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px;">حذف</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ========== نافذة تعديل العميل ========== -->
<div id="clientModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="clientModalTitle">تعديل العميل</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="clientForm">
                <input type="hidden" id="clientId" name="id" value="">
                
                <div class="form-group">
                    <label for="clientName">اسم العميل *</label>
                    <input type="text" id="clientName" name="name" required placeholder="أدخل اسم العميل">
                </div>
                
                <div class="form-group">
                    <label for="clientDescription">الوصف</label>
                    <textarea id="clientDescription" name="description" rows="3" placeholder="أدخل وصف العميل"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="clientLocation">الموقع</label>
                    <input type="text" id="clientLocation" name="location" placeholder="أدخل موقع العميل">
                </div>
                
                <div class="form-group">
                    <label for="clientDisplayOrder">ترتيب العرض *</label>
                    <input type="number" id="clientDisplayOrder" name="display_order" required min="1" value="1">
                </div>
                
                <div class="form-group">
                    <label for="clientStatus">الحالة *</label>
                    <select id="clientStatus" name="status" required>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="clientLogo">الشعار</label>
                    <input type="file" id="clientLogo" name="logo" accept="image/*">
                    <div id="currentLogo" style="margin-top: 10px;"></div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="saveClientBtn">حفظ</button>
                    <button type="button" class="btn btn-secondary close-modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========== نافذة تأكيد الحذف ========== -->
<div id="deleteClientModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>تأكيد الحذف</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <p>هل أنت متأكد من أنك تريد حذف هذا العميل؟</p>
            <input type="hidden" id="deleteClientId" value="">
            <div class="form-actions">
                <button type="button" class="btn btn-danger" id="confirmClientDelete">نعم، احذف</button>
                <button type="button" class="btn btn-secondary close-modal">إلغاء</button>
            </div>
        </div>
    </div>
</div>

<style>
.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background-color: #f8f9fa;
    padding: 12px;
    text-align: right;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}

.data-table td {
    padding: 12px;
    text-align: right;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}

.data-table tr:hover {
    background-color: #f8f9fa;
}

.data-table tr:nth-child(even) {
    background-color: #fdfdfd;
}

.table-responsive {
    overflow-x: auto;
}

@media (max-width: 768px) {
    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    
    .data-table {
        min-width: 800px;
    }
}
</style>

<script>
// ========== الأكواد الجديدة المطلوبة ==========

// إغلاق النوافذ
document.querySelectorAll('.close-modal').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });
    });
});

// إغلاق النافذة عند النقر خارجها
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});

// ========== تعديل العميل ==========
document.querySelectorAll('.edit-client').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        
        // جلب بيانات العميل عبر AJAX
        fetch(`ajax/clients_ajax.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success === false) {
                    showClientAlert('خطأ', data.message, 'error');
                    return;
                }
                
                // تعبئة النموذج بالبيانات
                document.getElementById('clientModalTitle').textContent = 'تعديل العميل';
                document.getElementById('clientId').value = data.id;
                document.getElementById('clientName').value = data.name || '';
                document.getElementById('clientDescription').value = data.description || '';
                document.getElementById('clientLocation').value = data.location || '';
                document.getElementById('clientDisplayOrder').value = data.display_order || 1;
                document.getElementById('clientStatus').value = data.status || 'active';
                
                // عرض الشعار الحالي إذا كان موجوداً
                const currentLogoDiv = document.getElementById('currentLogo');
                if (data.logo) {
                    currentLogoDiv.innerHTML = `
                        <p>الشعار الحالي:</p>
                        <img src="uploads/clients/${data.logo}" 
                             style="max-width: 100px; max-height: 100px; margin-top: 5px; border: 1px solid #ddd; padding: 5px;">
                    `;
                } else {
                    currentLogoDiv.innerHTML = '<p>لا يوجد شعار حالياً</p>';
                }
                
                // فتح النافذة
                document.getElementById('clientModal').style.display = 'flex';
            })
            .catch(error => {
                showClientAlert('خطأ', 'حدث خطأ أثناء جلب البيانات', 'error');
                console.error('Error:', error);
            });
    });
});

// ========== حذف العميل ==========
document.querySelectorAll('.delete-client').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        document.getElementById('deleteClientId').value = id;
        document.getElementById('deleteClientModal').style.display = 'flex';
    });
});

// ========== تأكيد حذف العميل ==========
document.getElementById('confirmClientDelete').addEventListener('click', function() {
    const id = document.getElementById('deleteClientId').value;
    
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('ajax/clients_ajax.php?action=delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showClientAlert('نجاح', data.message, 'success');
            // إعادة تحميل الصفحة بعد ثانية
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showClientAlert('خطأ', data.message, 'error');
        }
        document.getElementById('deleteClientModal').style.display = 'none';
    })
    .catch(error => {
        showClientAlert('خطأ', 'حدث خطأ أثناء الحذف', 'error');
        console.error('Error:', error);
    });
});

// ========== حفظ نموذج العميل ==========
document.getElementById('clientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const saveBtn = document.getElementById('saveClientBtn');
    
    // تغيير نص الزر أثناء الحفظ
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'جاري الحفظ...';
    saveBtn.disabled = true;
    
    // التحقق من الحقول المطلوبة
    if (!formData.get('name') || !formData.get('display_order')) {
        showClientAlert('تحذير', 'الرجاء ملء جميع الحقول المطلوبة', 'warning');
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
        return;
    }
    
    // إرسال البيانات عبر AJAX
    fetch('ajax/clients_ajax.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('خطأ في الشبكة');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // عرض رسالة النجاح
            showClientAlert('نجاح', 'تم حفظ العميل بنجاح', 'success');
            
            // إغلاق النافذة
            document.getElementById('clientModal').style.display = 'none';
            
            // إعادة تحميل الصفحة كاملة بعد تأخير قصير
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showClientAlert('خطأ', data.message || 'حدث خطأ أثناء الحفظ', 'error');
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
        }
    })
    .catch(error => {
        showClientAlert('خطأ', 'حدث خطأ أثناء الحفظ: ' + error.message, 'error');
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
        console.error('Error:', error);
    });
});

// ========== دالة لعرض الإشعارات ==========
function showClientAlert(title, message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <strong>${title}</strong>
        <p>${message}</p>
    `;
    
    const container = document.getElementById('clientAlerts');
    container.innerHTML = '';
    container.appendChild(alertDiv);
    
    // إزالة الإشعار بعد 5 ثوانٍ
    setTimeout(() => {
        if (alertDiv.parentNode === container) {
            alertDiv.remove();
        }
    }, 5000);
}

// ========== إضافة الأنماط المباشرة (نفس تصميم statistics.php) ==========
const style = document.createElement('style');
style.textContent = `
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: white;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        color: #333;
    }

    .close-modal {
        cursor: pointer;
        font-size: 24px;
        color: #999;
    }

    .close-modal:hover {
        color: #333;
    }

    .modal-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: 500;
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="file"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-group input[type="text"]:focus,
    .form-group input[type="number"]:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #4a90e2;
        box-shadow: 0 0 0 2px rgba(74,144,226,0.2);
    }

    .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .alert {
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .alert-error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .alert-warning {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
    }

    .alert-info {
        background-color: #d1ecf1;
        border: 1px solid #bee5eb;
        color: #0c5460;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #545b62;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }
    
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
`;
document.head.appendChild(style);
</script>
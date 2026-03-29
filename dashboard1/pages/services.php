<?php
// pages/services.php

// فحص وجود جدول services
$table_exists = $database->query("SHOW TABLES LIKE 'services'");

if (!$table_exists || $table_exists->num_rows == 0) {
    echo "<div class='alert alert-error'>
        <h4>⚠️ جدول الخدمات غير موجود!</h4>
        <p>جدول الخدمات غير موجود في قاعدة البيانات.</p>
        <p><a href='check_tables.php' style='color: #d30909; font-weight: bold;'>انقر هنا لإصلاح قاعدة البيانات</a></p>
    </div>";
    $services = [];
} else {
    // فحص وجود عمود title بدلاً من name
    $column_exists = $database->query("SHOW COLUMNS FROM services LIKE 'title'");
    
    if (!$column_exists || $column_exists->num_rows == 0) {
        echo "<div class='alert alert-error'>
            <h4>⚠️ عمود 'title' غير موجود!</h4>
            <p>عمود عنوان الخدمة غير موجود في جدول الخدمات.</p>
            <p><a href='check_tables.php' style='color: #d30909; font-weight: bold;'>انقر هنا لإصلاح قاعدة البيانات</a></p>
        </div>";
        
        // محاولة جلب البيانات بدون عمود title
        $sql = "SHOW COLUMNS FROM services";
        $result = $database->query($sql);
        $columns = [];
        if ($result) {
            while ($col = $result->fetch_assoc()) {
                $columns[] = $col['Field'];
            }
        }
        
        if (!empty($columns)) {
            $sql = "SELECT " . implode(", ", $columns) . " FROM services";
            $services_result = $database->query($sql);
        } else {
            $services_result = false;
        }
    } else {
        // الاستعلام العادي إذا كان كل شيء صحيحاً
        // ملاحظة: الجدول الحالي لا يحتوي على display_order أو category
        // لذا نستخدم الاستعلام الأساسي
        $sql = "SELECT * FROM services ORDER BY created_at DESC";
        $services_result = $database->query($sql);
    }
    
    // معالجة النتيجة
    if (!$services_result) {
        echo "<div class='alert alert-error'>خطأ في جلب بيانات الخدمات</div>";
        $services = [];
    } else {
        $services = [];
        while ($row = $services_result->fetch_assoc()) {
            // إضافة قيم افتراضية للمفاتيح المفقودة
            $row['title'] = $row['title'] ?? 'خدمة بدون عنوان';
            $row['description'] = $row['description'] ?? 'لا يوجد وصف';
            $row['category'] = $row['category'] ?? 'غير محدد'; // غير موجود في الجدول الحالي
            $row['icon'] = $row['icon'] ?? 'fas fa-question-circle';
            $row['display_order'] = $row['display_order'] ?? 1; // غير موجود في الجدول الحالي
            $row['status'] = isset($row['is_active']) ? ($row['is_active'] ? 'active' : 'inactive') : 'active';
            
            $services[] = $row;
        }
    }
}
?>

<div id="services" class="content-page active">
    <div id="serviceAlerts"></div>
    
    <div class="card">
        <div class="table-header">
            <div class="table-title">إدارة الخدمات</div>
            <!-- تم إزالة زر إضافة خدمة جديدة من هنا -->
        </div>
        
        <?php if (empty($services)): ?>
        <div class="no-results-message">
            <i class="fas fa-building"></i>
            <h3>لا توجد خدمات</h3>
            <p>لم يتم إضافة أي خدمات بعد.</p>
            <!-- تم إزالة زر إضافة أول خدمة من هنا -->
            <p style="margin-top: 20px;">
                <a href="check_tables.php" class="btn" style="background: #6c757d;">فحص وإصلاح قاعدة البيانات</a>
            </p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table id="servicesTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="service-icon-cell">الأيقونة</th>
                        <th>اسم الخدمة</th>
                        <th class="service-description-cell">الوصف</th>
                        <th>الفئة</th>
                        <th>الحالة</th>
                        <th>تاريخ الإضافة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $index => $service): ?>
                    <tr>
                        <td><?php echo $service['id'] ?? ($index + 1); ?></td>
                        <td class="service-icon-cell">
                            <i class="<?php echo htmlspecialchars($service['icon']); ?> fa-lg"></i>
                        </td>
                        <td><?php echo htmlspecialchars($service['title']); ?></td>
                        <td class="service-description-cell" title="<?php echo htmlspecialchars($service['description']); ?>">
                            <?php 
                            $desc = $service['description'];
                            echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($service['category']); ?></td>
                        <td>
                            <span class="status status-<?php echo ($service['status'] == 'active') ? 'active' : 'inactive'; ?>">
                                <?php echo ($service['status'] == 'active') ? 'نشط' : 'غير نشط'; ?>
                            </span>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($service['created_at'] ?? 'now')); ?></td>
                        <td class="action-buttons">
                            <button class="btn btn-sm btn-edit edit-service" data-id="<?php echo $service['id']; ?>" data-type="service">تعديل</button>
                            <button class="btn btn-sm btn-delete delete-service" data-id="<?php echo $service['id']; ?>" data-type="service">حذف</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- نموذج إضافة/تعديل الخدمة -->
<div id="serviceModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="serviceModalTitle">إضافة خدمة جديدة</h3>
            <span class="modal-close" onclick="document.getElementById('serviceModal').style.display='none'">&times;</span>
        </div>
        <div class="modal-body">
            <form id="serviceForm">
                <input type="hidden" id="serviceId" name="id" value="">
                
                <div class="form-group">
                    <label for="serviceTitle">اسم الخدمة *</label>
                    <input type="text" id="serviceTitle" name="title" required placeholder="أدخل اسم الخدمة">
                </div>
                
                <div class="form-group">
                    <label for="serviceDescription">وصف الخدمة *</label>
                    <textarea id="serviceDescription" name="description" rows="4" required placeholder="أدخل وصف الخدمة"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="serviceIcon">أيقونة الخدمة</label>
                    <div class="icon-picker">
                        <input type="text" id="serviceIcon" name="icon" placeholder="fas fa-cog" value="fas fa-cog">
                        <div class="icon-preview">
                            <i id="iconPreview" class="fas fa-cog"></i>
                        </div>
                    </div>
                    <small class="form-hint">استخدم أيقونات FontAwesome مثل: fas fa-mobile-alt, fas fa-laptop-code</small>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="serviceStatus">حالة الخدمة</label>
                        <select id="serviceStatus" name="status">
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="serviceOrder">ترتيب العرض</label>
                        <input type="number" id="serviceOrder" name="order" min="1" value="1">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="document.getElementById('serviceModal').style.display='none'">إلغاء</button>
                    <button type="submit" class="btn btn-save" id="saveServiceBtn">حفظ الخدمة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// تمت إزالة مستمع الحدث لزر إضافة الخدمة الأولى

// تمت إزالة مستمع الحدث لزر إضافة خدمة جديدة

// تحديث معاينة الأيقونة
document.getElementById('serviceIcon').addEventListener('input', function() {
    document.getElementById('iconPreview').className = this.value || 'fas fa-question-circle';
});

// معالجة حذف الخدمة
document.querySelectorAll('.delete-service').forEach(button => {
    button.addEventListener('click', function() {
        const serviceId = this.getAttribute('data-id');
        const serviceName = this.closest('tr').querySelector('td:nth-child(3)').textContent;
        
        if (confirm(`هل أنت متأكد من حذف الخدمة "${serviceName}"؟`)) {
            fetch('services_ajax.php?action=delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${serviceId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    // إعادة تحميل الصفحة بعد ثانيتين
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'حدث خطأ أثناء الحذف');
            });
        }
    });
});

// معالجة تعديل الخدمة
document.querySelectorAll('.edit-service').forEach(button => {
    button.addEventListener('click', function() {
        const serviceId = this.getAttribute('data-id');
        
        // جلب بيانات الخدمة
        fetch(`services_ajax.php?action=get&id=${serviceId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success === false) {
                    showAlert('error', data.message);
                    return;
                }
                
                // تعبئة النموذج بالبيانات
                document.getElementById('serviceModalTitle').textContent = 'تعديل الخدمة';
                document.getElementById('serviceId').value = data.id;
                document.getElementById('serviceTitle').value = data.title || '';
                document.getElementById('serviceDescription').value = data.description || '';
                document.getElementById('serviceIcon').value = data.icon || 'fas fa-cog';
                document.getElementById('iconPreview').className = data.icon || 'fas fa-cog';
                document.getElementById('serviceOrder').value = data.display_order || 1;
                document.getElementById('serviceStatus').value = data.is_active ? 'active' : 'inactive';
                
                document.getElementById('serviceModal').style.display = 'flex';
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'حدث خطأ أثناء جلب البيانات');
            });
    });
});

// معالجة حفظ الخدمة
document.getElementById('serviceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const saveBtn = document.getElementById('saveServiceBtn');
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'جاري الحفظ...';
    saveBtn.disabled = true;
    
    const formData = new FormData(this);
    // تحويل الحالة إلى is_active
    formData.append('is_active', formData.get('status') === 'active' ? '1' : '0');
    
    fetch('services_ajax.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            // إغلاق النموذج وإعادة تحميل الصفحة بعد 1.5 ثانية
            document.getElementById('serviceModal').style.display = 'none';
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message);
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'حدث خطأ أثناء الحفظ');
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
});

// دالة لعرض التنبيهات
function showAlert(type, message) {
    const alertsDiv = document.getElementById('serviceAlerts');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <span>${message}</span>
        <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    alertsDiv.appendChild(alert);
    
    // إزالة التنبيه بعد 5 ثوانٍ
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>

<style>
/* تنسيقات إضافية */
.icon-picker {
    display: flex;
    gap: 10px;
    align-items: center;
}

.icon-preview {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.icon-preview i {
    font-size: 1.2rem;
    color: #333;
}

.form-hint {
    color: #666;
    font-size: 0.85rem;
    margin-top: 5px;
    display: block;
}

.alert {
    padding: 12px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    animation: slideIn 0.3s ease;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.alert-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: inherit;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
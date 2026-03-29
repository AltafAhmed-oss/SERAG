<?php
// pages/statistics.php

// استعلام آمن لجلب الإحصائيات
$sql = "SHOW COLUMNS FROM statistics";
$columns_result = $database->query($sql);
$has_color = false;
$has_status = false;

if ($columns_result) {
    while ($column = $columns_result->fetch_assoc()) {
        if ($column['Field'] == 'color') $has_color = true;
        if ($column['Field'] == 'status') $has_status = true;
    }
}

// بناء الاستعلام المناسب
if ($has_color && $has_status) {
    $sql = "SELECT * FROM statistics ORDER BY id";
} elseif ($has_color) {
    $sql = "SELECT * FROM statistics ORDER BY id";
} else {
    // استعلام بديل بدون عمود color
    $sql = "SELECT id, title, value, icon, 
                   IF(status IS NOT NULL, status, 'active') as status,
                   IF(created_at IS NOT NULL, created_at, NOW()) as created_at 
            FROM statistics ORDER BY id";
}

// تنفيذ الاستعلام
$statistics_result = $database->query($sql);

if (!$statistics_result) {
    // محاولة استعلام أبسط
    $sql = "SELECT * FROM statistics";
    $statistics_result = $database->query($sql);
}

// التحقق من النتيجة
if (!$statistics_result) {
    echo "<div class='alert alert-error'>خطأ في جلب بيانات الإحصائيات. الرجاء التحقق من قاعدة البيانات.</div>";
    $statistics = [];
} else {
    $statistics = [];
    while ($row = $statistics_result->fetch_assoc()) {
        $statistics[] = $row;
    }
}
?>

<div id="statistics" class="content-page active">
    <div id="statAlerts"></div>
    
    <div class="card">
        <div class="table-header">
            <div class="table-title">إدارة الإحصائيات</div>
            <!-- تم إزالة زر إضافة إحصائية جديدة من هنا -->
        </div>
        
        <?php if (empty($statistics)): ?>
        <div class="no-results-message">
            <i class="fas fa-chart-bar"></i>
            <h3>لا توجد إحصائيات</h3>
            <p>لم يتم إضافة أي إحصائيات بعد.</p>
            <!-- تم إزالة زر إضافة أول إحصائية من هنا -->
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table id="statsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>القيمة</th>
                        <th>الأيقونة</th>
                        <th>اللون</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statistics as $stat): ?>
                    <?php 
                    // قيم افتراضية للعناصر المفقودة
                    $color = isset($stat['color']) ? $stat['color'] : 'red';
                    $status = isset($stat['status']) ? $stat['status'] : 'active';
                    $icon = isset($stat['icon']) ? $stat['icon'] : 'fas fa-chart-bar';
                    $status_text = ($status == 'active') ? 'نشط' : 'غير نشط';
                    $status_class = ($status == 'active') ? 'active' : 'inactive';
                    ?>
                    <tr>
                        <td><?php echo $stat['id']; ?></td>
                        <td><?php echo htmlspecialchars($stat['title'] ?? 'بدون عنوان'); ?></td>
                        <td><?php echo htmlspecialchars($stat['value'] ?? '0'); ?></td>
                        <td><i class="<?php echo htmlspecialchars($icon); ?>"></i></td>
                        <td>
                            <span class="status" style="background-color: <?php echo $color; ?>20; color: <?php echo $color; ?>;">
                                <?php echo htmlspecialchars($color); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status status-<?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <button class="btn btn-sm btn-edit edit-stat" data-id="<?php echo $stat['id']; ?>" data-type="statistic">تعديل</button>
                            <button class="btn btn-sm btn-delete delete-stat" data-id="<?php echo $stat['id']; ?>" data-type="statistic">حذف</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ========== إضافة النافذة المفقودة ========== -->
<div id="statModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="statModalTitle">إضافة إحصائية جديدة</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="statForm">
                <input type="hidden" id="statId" name="id" value="">
                
                <div class="form-group">
                    <label for="statTitle">العنوان *</label>
                    <input type="text" id="statTitle" name="title" required placeholder="أدخل عنوان الإحصائية">
                </div>
                
                <div class="form-group">
                    <label for="statValue">القيمة *</label>
                    <input type="text" id="statValue" name="value" required placeholder="أدخل قيمة الإحصائية">
                </div>
                
                <div class="form-group">
                    <label for="statIcon">الأيقونة *</label>
                    <select id="statIcon" name="icon" required>
                        <option value="fas fa-users">👥 مستخدمين</option>
                        <option value="fas fa-shopping-cart">🛒 طلبات</option>
                        <option value="fas fa-dollar-sign">💰 مبيعات</option>
                        <option value="fas fa-chart-line">📈 إحصائيات</option>
                        <option value="fas fa-star">⭐ تقييمات</option>
                         <option value="fas fa-calendar-alt">📅 تقويم</option>
                        <option value="fas fa-project-diagram">📊 مشاريع</option>
                        <option value="fas fa-check-circle">✅ مشروع ناجح</option>
                        <option value="fas fa-smile">😊 عميل راضي</option>
                        <option value="fas fa-handshake">🤝 شراكة</option>
                        <option value="fas fa-trophy">🏆 إنجازات</option>
                        <option value="fas fa-clock">⏰ وقت</option>
                        <option value="fas fa-globe">🌐 زوار</option>
                        <option value="fas fa-comments">💬 تعليقات</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="statColor">اللون *</label>
                    <select id="statColor" name="color" required>
                        <option value="red">🔴 أحمر</option>
                        <option value="blue">🔵 أزرق</option>
                        <option value="green">🟢 أخضر</option>
                        <option value="orange">🟠 برتقالي</option>
                        <option value="purple">🟣 بنفسجي</option>
                        <option value="teal">🔷 تركواز</option>
                        <option value="yellow">🟡 أصفر</option>
                        <option value="pink">🌸 وردي</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="statStatus">الحالة *</label>
                    <select id="statStatus" name="status" required>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">حفظ</button>
                    <button type="button" class="btn btn-secondary close-modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========== نافذة تأكيد الحذف ========== -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>تأكيد الحذف</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <p>هل أنت متأكد من أنك تريد حذف هذه الإحصائية؟</p>
            <input type="hidden" id="deleteId" value="">
            <div class="form-actions">
                <button type="button" class="btn btn-danger" id="confirmDelete">نعم، احذف</button>
                <button type="button" class="btn btn-secondary close-modal">إلغاء</button>
            </div>
        </div>
    </div>
</div>

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

// التعديل على الإحصائية
document.querySelectorAll('.edit-stat').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        
        // جلب بيانات الإحصائية عبر AJAX
        fetch(`statistics_ajax.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success === false) {
                    showAlert('خطأ', data.message, 'error');
                    return;
                }
                
                // تعبئة النموذج بالبيانات
                document.getElementById('statModalTitle').textContent = 'تعديل الإحصائية';
                document.getElementById('statId').value = data.id;
                document.getElementById('statTitle').value = data.title || '';
                document.getElementById('statValue').value = data.value || '';
                document.getElementById('statIcon').value = data.icon || 'fas fa-chart-bar';
                document.getElementById('statColor').value = data.color || 'red';
                document.getElementById('statStatus').value = data.status || 'active';
                
                // فتح النافذة
                document.getElementById('statModal').style.display = 'flex';
            })
            .catch(error => {
                showAlert('خطأ', 'حدث خطأ أثناء جلب البيانات', 'error');
                console.error('Error:', error);
            });
    });
});

// حذف الإحصائية
document.querySelectorAll('.delete-stat').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteModal').style.display = 'flex';
    });
});

// تأكيد الحذف
document.getElementById('confirmDelete').addEventListener('click', function() {
    const id = document.getElementById('deleteId').value;
    
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('statistics_ajax.php?action=delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('نجاح', data.message, 'success');
            // إعادة تحميل الصفحة بعد ثانية
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert('خطأ', data.message, 'error');
        }
        document.getElementById('deleteModal').style.display = 'none';
    })
    .catch(error => {
        showAlert('خطأ', 'حدث خطأ أثناء الحذف', 'error');
        console.error('Error:', error);
    });
});

// حفظ النموذج (إضافة/تعديل)
document.getElementById('statForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // التحقق من الحقول المطلوبة
    if (!formData.get('title') || !formData.get('value')) {
        showAlert('تحذير', 'الرجاء ملء جميع الحقول المطلوبة', 'warning');
        return;
    }
    
    // إرسال البيانات عبر AJAX
    fetch('statistics_ajax.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('نجاح', data.message, 'success');
            // إغلاق النافذة وإعادة تحميل الصفحة
            document.getElementById('statModal').style.display = 'none';
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert('خطأ', data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('خطأ', 'حدث خطأ أثناء الحفظ', 'error');
        console.error('Error:', error);
    });
});

// دالة لعرض الإشعارات
function showAlert(title, message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <strong>${title}</strong>
        <p>${message}</p>
    `;
    
    const container = document.getElementById('statAlerts');
    container.innerHTML = '';
    container.appendChild(alertDiv);
    
    // إزالة الإشعار بعد 5 ثوانٍ
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// إضافة الأنماط مباشرة
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
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-group input[type="text"]:focus,
    .form-group select:focus {
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
`;
document.head.appendChild(style);
</script>
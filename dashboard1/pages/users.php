<?php
// pages/users.php

// استعلام آمن لجلب المستخدمين
$sql = "SHOW COLUMNS FROM users";
$columns_result = $database->query($sql);
$has_status = false;
$has_last_login = false;
$has_created_at = false;

if ($columns_result) {
    while ($column = $columns_result->fetch_assoc()) {
        if ($column['Field'] == 'status') $has_status = true;
        if ($column['Field'] == 'last_login') $has_last_login = true;
        if ($column['Field'] == 'created_at') $has_created_at = true;
    }
}

// بناء الاستعلام المناسب
$select_fields = "id, username, email, role";
if ($has_status) $select_fields .= ", status";
if ($has_last_login) $select_fields .= ", last_login";
if ($has_created_at) $select_fields .= ", created_at";

$sql = "SELECT $select_fields FROM users ORDER BY id";

// تنفيذ الاستعلام
$users_result = $database->query($sql);

if (!$users_result) {
    // محاولة استعلام أبسط
    $sql = "SELECT id, username, email, role FROM users";
    $users_result = $database->query($sql);
}

// التحقق من النتيجة
if (!$users_result) {
    echo "<div class='alert alert-error'>خطأ في جلب بيانات المستخدمين. الرجاء التحقق من قاعدة البيانات.</div>";
    $users = [];
} else {
    $users = [];
    while ($row = $users_result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<div id="users" class="content-page active">
    <div id="userAlerts"></div>
    
    <div class="card">
        <div class="table-header">
            <div class="table-title">إدارة المستخدمين</div>
            <button class="btn btn-add" id="addUserBtn">إضافة مستخدم جديد</button>
        </div>
        
        <?php if (empty($users)): ?>
        <div class="no-results-message">
            <i class="fas fa-user-cog"></i>
            <h3>لا يوجد مستخدمون</h3>
            <p>لم يتم إضافة أي مستخدمين بعد.</p>
            <button class="btn btn-add" id="addFirstUserBtn" style="margin-top: 20px;">إضافة أول مستخدم</button>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>الحالة</th>
                        <th>آخر نشاط</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <?php 
                    // قيم افتراضية للعناصر المفقودة
                    $status = isset($user['status']) ? $user['status'] : 'active';
                    $last_login = isset($user['last_login']) ? $user['last_login'] : null;
                    $created_at = isset($user['created_at']) ? $user['created_at'] : null;
                    $status_text = ($status == 'active') ? 'نشط' : 'غير نشط';
                    $status_class = ($status == 'active') ? 'active' : 'inactive';
                    
                    // تنسيق الأدوار
                    $roles_display = [
                        'admin' => '<span class="status" style="background-color: #d30909; color: white;">مدير</span>',
                        'editor' => '<span class="status" style="background-color: #007bff; color: white;">محرر</span>', 
                        'viewer' => '<span class="status" style="background-color: #6c757d; color: white;">مشاهد</span>'
                    ];
                    $role_display = $roles_display[$user['role']] ?? $user['role'];
                    ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $role_display; ?></td>
                        <td>
                            <span class="status status-<?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($last_login): ?>
                                <?php echo date('d/m/Y H:i', strtotime($last_login)); ?>
                            <?php else: ?>
                                <span style="color: #6c757d; font-style: italic;">لم يسجل دخول</span>
                            <?php endif; ?>
                        </td>
                        <td class="action-buttons">
                            <button class="btn btn-sm btn-edit edit-user" data-id="<?php echo $user['id']; ?>" data-type="user">تعديل</button>
                            <?php if ($user['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                            <button class="btn btn-sm btn-delete delete-user" data-id="<?php echo $user['id']; ?>" data-type="user">حذف</button>
                            <?php endif; ?>
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
// زر إضافة المستخدم الأول
document.getElementById('addFirstUserBtn')?.addEventListener('click', function() {
    document.getElementById('addUserBtn').click();
});

// زر إضافة مستخدم جديد
document.getElementById('addUserBtn').addEventListener('click', function() {
    document.getElementById('userModalTitle').textContent = 'إضافة مستخدم جديد';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('userPassword').required = true;
    document.getElementById('userRole').value = 'editor';
    document.getElementById('userStatus').value = 'active';
    document.getElementById('userModal').style.display = 'flex';
});
</script>
<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// التحقق من تسجيل الدخول
if(!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$page_title = "إدارة الإحصاءات";
include '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// معالجة إضافة/تعديل الإحصاءات
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['add_stat'])) {
        $title = sanitizeInput($_POST['title']);
        $value = sanitizeInput($_POST['value']);
        $icon = sanitizeInput($_POST['icon']);
        
        $query = "INSERT INTO statistics (title, value, icon) VALUES (:title, :value, :icon)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':icon', $icon);
        
        if($stmt->execute()) {
            $success_message = "تم添加 الإحصاء بنجاح";
        } else {
            $error_message = "حدث خطأ أثناء添加 الإحصاء";
        }
    }
    
    if(isset($_POST['update_stat'])) {
        $id = sanitizeInput($_POST['id']);
        $title = sanitizeInput($_POST['title']);
        $value = sanitizeInput($_POST['value']);
        $icon = sanitizeInput($_POST['icon']);
        
        $query = "UPDATE statistics SET title = :title, value = :value, icon = :icon WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':icon', $icon);
        
        if($stmt->execute()) {
            $success_message = "تم تحديث الإحصاء بنجاح";
        } else {
            $error_message = "حدث خطأ أثناء التحديث";
        }
    }
    
    if(isset($_POST['delete_stat'])) {
        $id = sanitizeInput($_POST['id']);
        
        $query = "DELETE FROM statistics WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            $success_message = "تم حذف الإحصاء بنجاح";
        } else {
            $error_message = "حدث خطأ أثناء الحذف";
        }
    }
}

// جلب جميع الإحصاءات
$stats_query = "SELECT * FROM statistics ORDER BY created_at DESC";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
?>

<!-- محتوى الصفحة -->
<div class="container-fluid">
    <!-- ... القائمة الجانبية مثل باقي الصفحات ... -->
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">إدارة الإحصاءات</h1>
        </div>

        <?php if(isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- نموذج إضافة إحصاء جديد -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>إضافة إحصاء جديد</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">العنوان</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">القيمة</label>
                                <input type="number" class="form-control" name="value" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">الأيقونة (Font Awesome)</label>
                                <input type="text" class="form-control" name="icon" placeholder="fas fa-icon">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" name="add_stat" class="btn btn-primary w-100">إضافة</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- جدول الإحصاءات -->
        <div class="card">
            <div class="card-header">
                <h5>الإحصاءات الحالية</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>القيمة</th>
                                <th>الأيقونة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($stat = $stats_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $stat['title']; ?></td>
                                <td><?php echo $stat['value']; ?></td>
                                <td><i class="<?php echo $stat['icon']; ?>"></i> <?php echo $stat['icon']; ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $stat['id']; ?>">
                                        <button type="submit" name="delete_stat" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
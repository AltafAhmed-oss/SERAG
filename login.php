<?php
session_start();
require_once 'config/database.php';

// إذا كان المستخدم مسجل دخول مسبقاً، توجيهه للوحة التحكم
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// إنشاء مستخدم admin إذا لم يكن موجوداً
$check_admin = $database->query("SELECT id FROM users WHERE username = 'admin'");
if ($check_admin->num_rows == 0) {
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $database->query("INSERT INTO users (username, password, email, role, is_active) VALUES ('admin', '$password_hash', 'admin@seragsoft.com', 'admin', 1)");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $database->escape_string($_POST['username']);
    $password = $_POST['password'];
    
    // التصحيح: استخدام is_active بدلاً من status
    $result = $database->query("SELECT * FROM users WHERE username = '$username' AND is_active = 1");
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // تحديث وقت آخر دخول
            $database->query("UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");
            
            header("Location: index.php");
            exit();
        } else {
            $error = "كلمة المرور غير صحيحة";
        }
    } else {
        $error = "اسم المستخدم غير موجود";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - سراج سوفت</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #ffffff , #ffffff );
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        .login-form {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            text-align: center;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #2d2c2c;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .logo p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .input-with-icon input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .input-with-icon input:focus {
            outline: none;
            border-color: #d30909;
            box-shadow: 0 0 0 3px rgba(211, 9, 9, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #d30909;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #a80707;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(211, 9, 9, 0.3);
        }

        .error {
            background: #ff3838;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
        }

        .login-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            border: 2px dashed #dee2e6;
        }

        .login-info h3 {
            color: #d30909;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .credentials {
            background: white;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .credential-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .credential-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .credential-label {
            font-weight: 600;
            color: #333;
        }

        .credential-value {
            font-family: monospace;
            background: #ffebee;
            padding: 2px 6px;
            border-radius: 3px;
            color: #d30909;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <div class="logo">
                <h1>سراج سوفت</h1>
                <p>لوحة تحكم الإدارة</p>
            </div>

            <div class="login-info">
                <h3><i class="fas fa-key"></i> بيانات الدخول الافتراضية</h3>
                <div class="credentials">
                    <div class="credential-item">
                        <span class="credential-label">اسم المستخدم:</span>
                        <span class="credential-value">admin</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">كلمة المرور:</span>
                        <span class="credential-value">******</span>
                    </div>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">اسم المستخدم</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" required placeholder="أدخل اسم المستخدم" value="admin">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required placeholder="أدخل كلمة المرور" value="admin123">
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </button>
            </form>
        </div>
    </div>
</body>
</html>
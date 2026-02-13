<?php
// includes/config.php

// منع تحميل الملف أكثر من مرة
if (defined('SERAGSOFT_CONFIG_LOADED')) {
    return;
}

define('SERAGSOFT_CONFIG_LOADED', true);

// التحقق من حالة الجلسة قبل بدئها
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تعريف المسارات
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('INCLUDES_PATH', ROOT_PATH . 'includes' . DIRECTORY_SEPARATOR);

// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_NAME', 'seragsoft_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// إعدادات الموقع
define('SITE_NAME', 'سراج سوفت');
define('SITE_URL', 'http://localhost/WEBSERAG');
define('DEFAULT_TIMEZONE', 'Asia/Riyadh');

// تعيين المنطقة الزمنية
date_default_timezone_set(DEFAULT_TIMEZONE);

// التحقق من وضع التطوير
define('DEVELOPMENT_MODE', true);

// تقرير الأخطاء بناءً على وضع التطوير
if (DEVELOPMENT_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . 'error.log');
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// تشغيل بافر الإخراج
if (ob_get_level() == 0) {
    ob_start();
}

// وظيفة التحميل التلقائي للكلاسات
spl_autoload_register(function ($class_name) {
    $file = INCLUDES_PATH . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
?>
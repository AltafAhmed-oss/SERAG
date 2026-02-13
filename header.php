<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>سراج سوفت - للأنظمة والحلول البرمجية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/img/icon.png" rel="icon">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* إصلاح لون القائمة - تغيير من الأحمر إلى الغامق */
        .sleek-header {
            background: #2c3e50 !important;
        }
        
        #mainNav ul li a {
            color: #ffffff !important;
            font-weight: 600;
        }
        
        #mainNav ul li a:hover {
            color: #ecf0f1 !important;
            background: rgba(255, 255, 255, 0.1) !important;
        }
        
        #mainNav ul li a.active {
            color: #ecf0f1 !important;
            background: rgba(255, 255, 255, 0.15) !important;
        }
        
        /* زر القائمة للجوال */
        .menu-toggle span {
            background: #ffffff !important;
        }
        
        /* القائمة الجانبية للجوال */
        nav {
            background: #2c3e50 !important;
        }
        
        /* إزالة أي خلفيات حمراء */
        header {
            background: #2c3e50 !important;
        }
    </style>
</head>
<body>
    <!-- زر التواصل الدائري -->
    <div class="contact-circle" id="contactCircle">
        <i class="fas fa-comment-dots"></i>
    </div>
    <!-- نافذة التواصل المنبثقة -->
    <div class="contact-popup" id="contactPopup">
        <div class="close-popup" id="closePopup">&times;</div>
        <h3>تواصل معنا</h3>
        <div class="contact-methods">
            <a href="tel:+967774400559" class="contact-method">
                <i class="fas fa-phone"></i>
                <span>اتصل بنا</span>
            </a>
            <a href="https://wa.me/+967772288443" class="contact-method" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i>
                <span>واتساب</span>
            </a>
            <a href="mailto:Seragsoft1@gmail.com" class="contact-method">
                <i class="fas fa-envelope"></i>
                <span>البريد الإلكتروني</span>
            </a>
            <a href="#contact" class="contact-method">
                <i class="fas fa-form"></i>
                <span>نموذج التواصل</span>
            </a>
        </div>
    </div>
   <!-- الرأس -->
<header class="sleek-header">
    <div class="container header-container">
        <div class="logo">
            <img src="assets/img/icon.png" alt="سراج سوفت" height="50px">
        </div>
        
        <!-- زر القائمة للجوال -->
        <button class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <nav id="mainNav">
            <ul>
                <li><a href="index.php#home">الرئيسية</a></li>
                <li><a href="index.php#services">خدماتنا</a></li>
                <li><a href="index.php#about">من نحن</a></li>
                <li><a href="index.php#clients">عملاؤنا</a></li>
                <li><a href="index.php#contact">تواصل معنا</a></li>
            </ul>
        </nav>
    </div>
</header>
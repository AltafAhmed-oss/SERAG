<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
// الاتصال بقاعدة البيانات
$database = new Database();
$db = $database->getConnection();

// جلب الشرائح النشطة
$slides_query = "SELECT * FROM slides WHERE is_active = 1 ORDER BY created_at DESC";
$slides_stmt = $db->prepare($slides_query);
$slides_stmt->execute();
// جلب الخدمات
$services_query = "SELECT * FROM services ORDER BY created_at DESC";
$services_stmt = $db->prepare($services_query);
$services_stmt->execute();

// جلب العملاء
$clients_query = "SELECT * FROM clients ORDER BY created_at DESC";
$clients_stmt = $db->prepare($clients_query);
$clients_stmt->execute();

$page_title = "الرئيسية";
include 'includes/header.php';
?>
<style>
    body {
    background-color: #ffffff !important;
}

.section {
    background: #ffffff !important;
    padding: 80px 0;
}

.about-section {
    background: #ffffff !important;
    padding: 100px 0;
}

.clients {
    background-color: #ffffff !important;
}

.contact {
    background: #ffffff !important;
}

.contact-info-section {
    background: #ffffff !important;
}

/* خلفية رمادية خفيفة للشعارات */
.logos {
    display: flex;
    gap: 150px;
    justify-content: center;
    align-items: center;
    padding: 40px 0;
    background: #f8f9fa !important; /* رمادي خفيف */
}

.logos img {
    opacity: 0.1;
    width: 100px;
    height: auto;
    transition: opacity 0.3s ease;
}

.logos img:hover {
    opacity: 0.2;
}

/* التأكد من إزالة أي خلفيات ملونة أخرى */
.hero {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
}

.about-section {
    background: #ffffff !important;
}
  /* تحديث الهيدر */
header {
    background: #2c3e50; /* تغيير إلى اللون الغامق */
    color: white;
    padding:1px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(44, 62, 80, 0.2);
}

/* تحديث الفوتر */
footer {
    background: #2c3e50; /* تغيير إلى اللون الغامق */
    color: white;
    padding: 60px 0 30px;
}

/* تحديث قسم معلومات التواصل */
.contact-info-section {
    background: #ffffff;
    padding: 60px 0 40px;
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
}

.contact-info-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 40px 30px;
    width: 280px;
    text-align: center;
    transition: all 0.4s ease;
    box-shadow: 0 10px 30px rgba(44, 62, 80, 0.1);
    border: 2px solid #2c3e50; /* حدود باللون الغامق */
    position: relative;
    overflow: hidden;
}

.contact-info-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 5px;
    background: #2c3e50; /* شريط علوي باللون الغامق */
    transition: all 0.3s ease;
}

.contact-info-card i {
    font-size: 3rem;
    margin-bottom: 25px;
    color: #2c3e50; /* أيقونات باللون الغامق */
    transition: all 0.3s ease;
}

.contact-info-card h3 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: #2c3e50; /* عناوين باللون الغامق */
    font-weight: 700;
}

.contact-info-card p,
.contact-info-card a {
    color: #5a6c7d;
    text-decoration: none;
    font-size: 1.1rem;
    line-height: 1.6;
    transition: all 0.3s;
}

.contact-info-card a:hover {
    color: #2c3e50; /* تغيير اللون عند التمرير إلى الغامق */
}

/* تحديث أسهم الهيرو */
.slider-arrows .arrow {
    background: rgba(44, 62, 80, 0.8); /* خلفية شفافة باللون الغامق */
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 0 20px;
}

.slider-arrows .arrow:hover {
    background: #2c3e50; /* خلفية صلبة عند التمرير باللون الغامق */
    transform: scale(1.1);
}

/* تحديث نقاط التنقل في الهيرو */
.slider-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(44, 62, 80, 0.5); /* لون النقاط باللون الغامق */
    cursor: pointer;
    transition: all 0.3s ease;
}

.slider-dot.active {
    background: #2c3e50; /* لون النقطة النشطة باللون الغامق */
    transform: scale(1.3);
}

/* تحديث أزرار الهيرو - لون غامق */
.hero-btns .btn {
    background: #2c3e50; /* خلفية باللون الغامق */
    color: white !important;
    border: 2px solid #2c3e50;
    box-shadow: 0 4px 15px rgba(44, 62, 80, 0.3); /* توهج غامق */
}

.hero-btns .btn:hover {
    background: #34495e; /* لون أغمق عند التمرير */
    color: white !important;
    box-shadow: 0 8px 25px rgba(44, 62, 80, 0.5); /* توهج غامق أقوى */
    transform: translateY(-3px);
}

/* تحديث أيقونات الخدمات */
.service-card i {
    font-size: 3.5rem;
    margin-bottom: 25px;
    color: #2c3e50; /* أيقونات الخدمات باللون الغامق */
    transition: all 0.3s ease;
}

/* تحديث أيقونات المميزات */
.feature i {
    font-size: 3.5rem;
    margin-bottom: 25px;
    color: #2c3e50; /* أيقونات المميزات باللون الغامق */
    transition: all 0.3s ease;
}

/* تحديث أيقونات الإحصائيات */
.stat-number {
    font-size: 3rem;
    font-weight: 800;
    color: #2c3e50; /* أرقام الإحصائيات باللون الغامق */
    margin-bottom: 15px;
    display: block;
}

/* تحديث أيقونات الفوتر */
.footer-section h3:after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 50px;
    height: 3px;
    background-color: #ffffff;
}

/* تحديث تأثيرات التمرير للبطاقات */
.contact-info-card:hover,
.service-card:hover,
.feature:hover,
.stat-item:hover {
    border-color: #2c3e50; /* حدود باللون الغامق عند التمرير */
}

.contact-info-card:hover::before,
.service-card:hover::before,
.feature:hover::before {
    background: #2c3e50; /* شريط علوي باللون الغامق عند التمرير */
}

/* ========== التعديلات الجديدة ========== */

/* تغيير جميع الأيقونات إلى اللون الغامق */
.service-card i[class*="fa-"],
.feature i[class*="fa-"],
.contact-info-card i[class*="fa-"],
.stat-item i[class*="fa-"] {
    color: #2c3e50 !important;
}

/* إزالة أي تدرجات حمراء من الأيقونات */
.service-card i {
    background: none !important;
    -webkit-background-clip: unset !important;
    -webkit-text-fill-color: #2c3e50 !important;
}

.feature i {
    background: none !important;
    -webkit-background-clip: unset !important;
    -webkit-text-fill-color: #2c3e50 !important;
}

/* توهج غامق للأزرار */
.btn {
    box-shadow: 0 4px 15px rgba(44, 62, 80, 0.2) !important;
}

.btn:hover {
    box-shadow: 0 8px 25px rgba(44, 62, 80, 0.3) !important;
}

/* التأكد من أن جميع الظلال باللون الغامق */
.service-card {
    box-shadow: 0 10px 30px rgba(44, 62, 80, 0.1) !important;
}

.service-card:hover {
    box-shadow: 0 20px 40px rgba(44, 62, 80, 0.15) !important;
}

.feature {
    box-shadow: 0 10px 30px rgba(44, 62, 80, 0.1) !important;
}

.feature:hover {
    box-shadow: 0 20px 40px rgba(44, 62, 80, 0.15) !important;
}

.contact-info-card {
    box-shadow: 0 10px 30px rgba(44, 62, 80, 0.1) !important;
}

.contact-info-card:hover {
    box-shadow: 0 20px 40px rgba(44, 62, 80, 0.15) !important;
}

/* التأكد من أن جميع النصوص في الأقسام باللون الغامق */
.section-title {
    color: #2c3e50;
}

.service-card h3 {
    color: #2c3e50;
}

.feature h3 {
    color: #2c3e50;
}

.about-content h3 {
    color: #2c3e50;
}

/* تحديث تأثيرات التدرج للون الغامق */
.contact-info-card::before {
    background: linear-gradient(90deg, #34495e, #2c3e50) !important;
}

.service-card::before {
    background: linear-gradient(90deg, #34495e, #2c3e50) !important;
}

.feature::before {
    background: linear-gradient(90deg, #34495e, #2c3e50) !important;
}

/* تحديث ألوان التمرير للأيقونات */
.contact-info-card:hover i,
.service-card:hover i,
.feature:hover i {
    color: #34495e !important; /* لون أغمق عند التمرير */
}

/* تغيير جميع الخطوط الحمراء تحت النصوص إلى اللون الغامق */
.section-title:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: #2c3e50 !important;
}

.service-card h3::after {
    content: '';
    position: absolute;
    bottom: -10px;
    right: 50%;
    transform: translateX(50%);
    width: 40px;
    height: 2px;
    background: #2c3e50 !important;
    transition: width 0.4s ease;
}

.contact-info-card h3::after {
    content: '';
    position: absolute;
    bottom: -8px;
    right: 50%;
    transform: translateX(50%);
    width: 40px;
    height: 2px;
    background: #2c3e50 !important;
    transition: width 0.4s ease;
}
/* تحديث لون الأرقام في الإحصائيات إلى الغامق */
.stat-number {
    font-size: 3rem;
    font-weight: 800;
    color: #2c3e50 !important; /* لون غامق */
    margin-bottom: 15px;
    display: block;
}

.stat-text {
    font-size: 1.3rem;
    color: #2c3e50 !important; /* لون غامق */
    font-weight: 600;
}

/* تحديث الكتابة في الفوتر إلى الأبيض */
footer {
    background: #2c3e50;
    color: white !important; /* نص أبيض */
    padding: 60px 0 30px;
}

.footer-section h3 {
    color: white !important; /* عناوين بيضاء */
}

.footer-links a {
    color: #bdc3c7 !important; /* روابط رمادية فاتحة */
    text-decoration: none;
    transition: all 0.3s;
}

.footer-links a:hover {
    color: white !important; /* أبيض عند التمرير */
    padding-right: 5px;
}

.contact-details span {
    color: #bdc3c7 !important; /* تفاصيل الاتصال رمادية فاتحة */
}

.copyright {
    color: #bdc3c7 !important; /* حقوق النشر رمادية فاتحة */
}

/* التأكد من أن جميع النصوص في الفوتر بيضاء */
footer * {
    color: white !important;
}

footer .footer-links a,
footer .contact-details span,
footer .copyright {
    color: #bdc3c7 !important;
}

footer .footer-links a:hover {
    color: white !important;
}
/* تغيير ألوان تأثيرات التمرير للخطوط */
.contact-info-card:hover h3::after {
    background: #34495e !important;
}

.service-card:hover h3::after {
    background: #34495e !important;
}/* قسم من نحن - إزالة الأحمر وجعل الخلفية بيضاء */
.about-section {
    background: #ffffff !important; /* خلفية بيضاء */
    padding: 100px 0;
}

.about-content h3 {
    font-size: 1.8rem;
    margin-bottom: 25px;
    color: #2c3e50 !important; /* تغيير من الأحمر إلى الغامق */
    line-height: 1.4;
    font-weight: 700;
}

.about-content h2.section-title {
    color: #2c3e50 !important; /* تغيير من الأحمر إلى الغامق */
}

/* إزالة أي تدرجات أو خلفيات حمراء من قسم من نحن */
.about-section::before,
.about-section::after {
    display: none !important; /* إخفاء أي عناصر زخرفية حمراء */
}

/* التأكد من أن نص "شركة يمنية متخصصة" باللون الغامق */
.about-content h3[style*="color"] {
    color: #2c3e50 !important; /* إجبار اللون الغامق */
}


.about-image {
    border: 1px solid #e9ecef !important; /* حدود رمادية بدلاً من الحمراء */
    box-shadow: 0 20px 40px rgba(44, 62, 80, 0.1) !important; /* ظل غامق */
}

.about-image:hover {
    box-shadow: 0 30px 60px rgba(44, 62, 80, 0.15) !important; /* ظل غامق عند التمرير */
}

/* تحديث أيقونات معلومات التواصل */
.contact-info-card i {
    font-size: 3rem;
    margin-bottom: 25px;
    color: #2c3e50 !important; /* لون غامق صلب */
    transition: all 0.3s ease;
    background: none !important; /* إزالة الخلفية الشفافة */
    -webkit-background-clip: unset !important;
    -webkit-text-fill-color: #2c3e50 !important;
}

.contact-info-card:hover i {
    color: #34495e !important; /* لون أغمق عند التمرير */
    transform: scale(1.15) rotate(5deg);
}

/* إزالة التدرجات الشفافة من أيقونات التواصل */
.contact-info-card i[class*="fa-"] {
    background: none !important;
    -webkit-text-fill-color: #2c3e50 !important;
    opacity: 1 !important; /* إزالة الشفافية */
}

/* التأكد من أن جميع أيقونات التواصل غامقة */
.contact-info-card .fa-map-marker-alt,
.contact-info-card .fa-phone,
.contact-info-card .fa-whatsapp,
.contact-info-card .fa-envelope {
    color: #2c3e50 !important;
    background: none !important;
    -webkit-text-fill-color: #2c3e50 !important;
}
/* تحديث زر إرسال الرسالة في نموذج التواصل */
.contact-form .btn {
    background: #2c3e50 !important; /* لون غامق */
    color: white !important; /* نص أبيض */
    border: 2px solid #2c3e50 !important; /* حدود غامقة */
    box-shadow: 0 4px 12px rgba(44, 62, 80, 0.2) !important; /* ظل خفيف بدون توهج */
}

.contact-form .btn:hover {
    background: #34495e !important; /* لون أغمق عند التمرير */
    color: white !important;
    border: 2px solid #34495e !important;
    box-shadow: 0 6px 15px rgba(44, 62, 80, 0.3) !important; /* ظل خفيف بدون توهج */
    transform: translateY(-2px);
}
/* تحديث أيقونات نافذة التواصل المنبثقة */
.contact-popup .contact-method i {
    color: #2c3e50 !important; /* لون غامق */
    background: none !important;
    -webkit-text-fill-color: #2c3e50 !important;
    opacity: 1 !important;
}

.contact-popup .contact-method:hover i {
    color: #34495e !important; /* لون أغمق عند التمرير */
}

/* التأكد من إزالة أي لون أحمر من أيقونات التواصل */
.contact-popup .fa-comments,
.contact-popup .fa-whatsapp,
.contact-popup .fa-phone,
.contact-popup .fa-envelope {
    color: #2c3e50 !important;
    background: none !important;
    -webkit-text-fill-color: #2c3e50 !important;
    
}

/* تحديث نافذة التواصل المنبثقة */
.contact-popup {
    background: white !important;
    border: 1px solid #e9ecef !important;
}

.contact-popup h3 {
    color: #2c3e50 !important; /* عنوان غامق */
}

.contact-method {
    color: #2c3e50 !important; /* نص غامق */
    border: 1px solid #e9ecef !important;
}

.contact-method:hover {
    background: #f8f9fa !important;
    color: #2c3e50 !important;
}
/* إظهار الإطار الخفيف وإزالة الأحمر منه */
.service-card {
    border: 1px solid rgba(44, 62, 80, 0.15) !important; /* إطار غامق خفيف */
    box-shadow: 0 5px 15px rgba(44, 62, 80, 0.1) !important; /* ظل خفيف */
    outline: none !important; /* إزالة أي outline أحمر */
}

.service-card:hover {
    border: 1px solid rgba(44, 62, 80, 0.3) !important; /* إطار أغمق عند التمرير */
    box-shadow: 0 8px 25px rgba(44, 62, 80, 0.15) !important;
     color: #2c3e50 !important;
}

/* إزالة أي outline أحمر من جميع العناصر */
*:focus {
    outline: none !important;
    box-shadow: none !important;
}

/* التأكد من إزالة أي حدود حمراء */
.service-card,
.feature,
.contact-info-card,
.stat-item {
    border-color: rgba(44, 62, 80, 0.15) !important;
}

/* إظهار الشريط العلوي باللون الغامق الخفيف */
.service-card::before,
.feature::before,
.contact-info-card::before {
    background: rgba(44, 62, 80, 0.2) !important; /* شريط علوي غامق خفيف */
    height: 3px !important; /* سماكة خفيفة */
}

/* ========== إضافة زر العودة للأعلى ========== */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: #2c3e50;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 999;
    box-shadow: 0 4px 15px rgba(44, 62, 80, 0.3);
    opacity: 0;
    visibility: hidden;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    background: #34495e;
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(44, 62, 80, 0.5);
}

.back-to-top i {
    font-size: 1.2rem;
    color: white;
}

/* ========== تحسين الأيقونات الاجتماعية في الفوتر ========== */
.social-icons {
    display: flex;
    gap: 12px;
    margin-top: 20px;
    justify-content: flex-start;
}

.social-icons a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    background-color: rgba(255, 255, 255, 0.15) !important;
    color: white !important;
    border-radius: 50%;
    transition: all 0.3s ease;
    font-size: 1.2rem;
    border: 1px solid rgba(255, 255, 255, 0.25);
    position: relative;
    overflow: hidden;
}

/* التأكد من أن الأيقونة بيضاء دائمًا */
.social-icons a i {
    color: white !important;
    z-index: 2;
    position: relative;
}

/* الخلفية عند التمرير */
.social-icons a::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: white;
    transform: scale(0);
    transition: all 0.3s ease;
    z-index: 1;
}

/* تأثيرات عند التمرير */
.social-icons a:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
}

.social-icons a:hover::before {
    transform: scale(1);
}

/* تغيير لون الأيقونة عند التمرير */
.social-icons a:hover i {
    color: #2c3e50 !important;
}

/* ألوان خاصة لكل شبكة اجتماعية عند التمرير */
.social-icons a.facebook:hover i {
    color: #1877f2 !important; /* أزرق فيسبوك */
}

.social-icons a.twitter:hover i {
    color: #1da1f2 !important; /* أزرق تويتر */
}

.social-icons a.instagram:hover i {
    color: #e4405f !important; /* أحمر/وردي إنستغرام */
}

.social-icons a.linkedin:hover i {
    color: #0a66c2 !important; /* أزرق لينكد إن */
}

.social-icons a.whatsapp:hover i {
    color: #25d366 !important; /* أخضر واتساب */
}

.social-icons a.youtube:hover i {
    color: #ff0000 !important; /* أحمر يوتيوب */
}

/* تحسين رؤية الأيقونات في الفوتر */
.footer-section .social-icons {
    margin-top: 20px;
}

.footer-section .social-icons a {
    margin: 0;
}

/* تنسيقات رسائل النموذج */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-weight: 500;
    text-align: center;
    border: 1px solid transparent;
}
.alert-success {
    background-color: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}
.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}
/* تنسيقات زر الإرسال */
#loadingSpinner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
#submitBtn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
/* تحسينات للنموذج */
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
}
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    transition: all 0.3s;
    font-family: inherit;
}
.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #2c3e50;
    box-shadow: 0 0 0 2px rgba(44, 62, 80, 0.1);
}
.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

/* ========== زر التواصل الدائري مع تأثير التوهج ========== */
.contact-circle {
    position: fixed;
    bottom: 30px;
    left: 30px;
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #2c3e50, #34495e);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    box-shadow: 0 5px 20px rgba(44, 62, 80, 0.4);
    cursor: pointer;
    z-index: 999;
    transition: all 0.3s ease;
    animation: pulse-dark 2s infinite;
    border: 2px solid white;
}

.contact-circle:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 25px rgba(44, 62, 80, 0.6);
}

.contact-circle:active {
    transform: scale(0.95);
    box-shadow: 0 3px 15px rgba(44, 62, 80, 0.3);
}

@keyframes pulse-dark {
    0% {
        box-shadow: 0 0 0 0 rgba(44, 62, 80, 0.7);
    }
    70% {
        box-shadow: 0 0 0 15px rgba(44, 62, 80, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(44, 62, 80, 0);
    }
}

/* نافذة التواصل المنبثقة */
.contact-popup {
    position: fixed;
    bottom: 120px;
    left: 30px;
    background: white;
    border-radius: 15px;
    padding: 20px;
    width: 300px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    z-index: 998;
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px);
    transition: all 0.4s ease;
    border: 1px solid #e9ecef;
}

.contact-popup.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.contact-popup h3 {
    color: #2c3e50 !important;
    margin-bottom: 15px;
    text-align: center;
}

.contact-popup .contact-methods {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.contact-method {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    border-radius: 8px;
    background: #f8f9fa;
    transition: all 0.3s;
    text-decoration: none;
    color: #2c3e50;
    border: 1px solid #e9ecef;
}

.contact-method:hover {
    background: #edf0f2;
    transform: translateX(-5px);
    border-color: #2c3e50;
}

.contact-method i {
    margin-left: 10px;
    color: #2c3e50 !important;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.contact-method:hover i {
    color: #34495e !important;
}

.close-popup {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #2c3e50;
    color: white;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: 1px solid white;
}

.close-popup:hover {
    background: #34495e;
    transform: scale(1.1);
}

</style>
<!-- القسم الرئيسي مع السلايدر -->
<section class="hero" id="home">
    <div class="hero-slider">
        <!-- الشريحة الأولى -->
        <div class="slide active" style="background-image: url('https://images.unsplash.com/photo-1558655146-9f40138edfeb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2064&q=80');">
            <div class="slide-content">    
                <h1>حلول برمجية مبتكرة تقود أعمالك للنجاح</h1>
                <p>نقدم في سراج سوفت أنظمة محاسبية متكاملة، تطبيقات ويب وجوال، وحلولاً تقنية مخصصة تلبي طموحاتك.</p>
                <div class="hero-btns">
                    <a href="#services" class="btn">اكتشف خدماتنا</a>
                    <a href="request-quote.php" class="btn">اطلب عرض سعر</a>
                </div>
            </div>
        </div>  
        <!-- الشريحة الثانية -->
        <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');">
            <div class="slide-content">
                <h1>تطوير برمجي متكامل لمؤسستك</h1>
                <p>نطور حلولاً برمجية متكاملة تساعد مؤسستك على النمو والازدهار في عالم الأعمال الرقمي.</p>
                <div class="hero-btns">
                    <a href="#services" class="btn">اكتشف خدماتنا</a>
                    <a href="request-quote.php" class="btn">اطلب عرض سعر</a>
                </div>
            </div>
        </div>  
        <!-- الشريحة الثالثة -->
        <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1551650975-87deedd944c3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1974&q=80');">
            <div class="slide-content">
                <h1>تطبيقات جوال مبتكرة وفريدة</h1>
                <p>نصمم تطبيقات جوال مبتكرة تلبي احتياجات عملائك وتعزز تجربتهم مع علامتك التجارية.</p>
                <div class="hero-btns">
                    <a href="#services" class="btn">اكتشف خدماتنا</a>
                    <a href="request-quote.php" class="btn">اطلب عرض سعر</a>
                </div>
            </div>
        </div>    
        <!-- الشريحة الرابعة -->
        <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');">
            <div class="slide-content">
                <h1>أنظمة إدارة متكاملة لمؤسستك</h1>
                <p>نوفر أنظمة ERP متكاملة لإدارة الموارد المالية والبشرية والمخازن بكفاءة عالية.</p>
                <div class="hero-btns">
                    <a href="#services" class="btn">اكتشف خدماتنا</a>
                    <a href="request-quote.php" class="btn">اطلب عرض سعر</a>
                </div>
            </div>
        </div>
    </div>  
   
        <!-- أسهم التنقل -->
        <div class="slider-arrows">
            <div class="arrow prev-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
            <div class="arrow next-arrow">
                <i class="fas fa-chevron-left"></i>
            </div>
        </div>
        <!-- نقاط التنقل -->
        <div class="slider-nav">
            <div class="slider-dot active" data-slide="0"></div>
            <div class="slider-dot" data-slide="1"></div>
            <div class="slider-dot" data-slide="2"></div>
            <div class="slider-dot" data-slide="3"></div>
        </div>
    
</section>
<!-- قسم الخدمات -->
<section class="section" id="services">
    <div class="container">
        <h2 class="section-title">خدمات متكاملة لتلبية كل احتياجاتك</h2>
        
        <div class="services-grid">
            <?php
            // الاتصال بقاعدة البيانات
            require_once 'includes/database.php';
            $database = new Database();
            $db = $database->getConnection();
            
            try {
                // محاولة جلب الخدمات مع العمود is_active
                $services_query = "SELECT * FROM services WHERE is_active = 1 ORDER BY created_at ASC";
                $services_stmt = $db->prepare($services_query);
                $services_stmt->execute();
                $has_active_column = true;
                
            } catch (PDOException $e) {
                // في حالة خطأ (عند عدم وجود عمود is_active)
                $has_active_column = false;
                
                // جلب جميع الخدمات بدون شرط is_active
                $services_query = "SELECT * FROM services ORDER BY created_at ASC";
                $services_stmt = $db->prepare($services_query);
                $services_stmt->execute();
            }
            
            // عرض الخدمات من قاعدة البيانات
            if ($services_stmt->rowCount() > 0) {
                while ($service = $services_stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="service-card">';
                    echo '<i class="' . htmlspecialchars($service['icon']) . '"></i>';
                    echo '<h3>' . htmlspecialchars($service['title']) . '</h3>';
                    echo '<p>' . htmlspecialchars($service['description']) . '</p>';
                    echo '</div>';
                }
            } else {
                // إذا لم توجد خدمات في قاعدة البيانات، عرض الخدمات الافتراضية
                ?>
                <div class="service-card">
                    <i class="fas fa-building"></i>
                    <h3>أنظمة ERP</h3>
                    <p>أنظمة مالية، موارد بشرية، إدارة مخازن متكاملة وشاملة لجميع احتياجات مؤسستك. صممت خصيصًا لتلبي متطلبات الأعمال بمختلف أحجامها.</p>
                </div>
                
                <div class="service-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>تطبيقات الجوال</h3>
                    <p>تطبيقات iOS و Android عالية الأداء مع واجهات مستخدم جذابة وسهلة الاستخدام. نضمن تجربة مستخدم فريدة وسلسة.</p>
                </div>
                
                <div class="service-card">
                    <i class="fas fa-laptop-code"></i>
                    <h3>تطوير الويب</h3>
                    <p>مواقع إلكترونية ومتاجر تفاعلية بتقنيات حديثة وتصاميم متجاوبة. نبتكر حلولاً رقمية تعزز حضورك على الإنترنت.</p>
                </div>
                
                <div class="service-card">
                    <i class="fas fa-tools"></i>
                    <h3>حلول مخصصة</h3>
                    <p>برمجة خاصة وتطوير حلول فريدة لتلبية المتطلبات الخاصة بعملك. نقدم استشارات تقنية وحلولاً مبتكرة تناسب احتياجاتك.</p>
                </div>
                
                <?php
                // عرض رسالة للمسؤول
                if (isset($_SESSION['user_id'])) {
                    echo '<div class="alert alert-info text-center" style="grid-column: 1 / -1;">';
                    echo '<h4>ملاحظة للمسؤول</h4>';
                    echo '<p>يتم عرض خدمات افتراضية لأن قاعدة البيانات لا تحتوي على خدمات أو يوجد خطأ في الاتصال.</p>';
                    echo '</div>';
                }
            }
            
            // إذا كان العمود is_active غير موجود، عرض رسالة للمسؤول
            if (!$has_active_column && isset($_SESSION['user_id'])) {
                echo '<div class="alert alert-warning text-center" style="grid-column: 1 / -1;">';
                echo '<h4>ملاحظة فنية</h4>';
                echo '<p>عمود is_active غير موجود في جدول الخدمات. يرجى تحديث قاعدة البيانات.</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>
 <div class="logos">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
      </div>
<!-- قسم من نحن - التصميم الجديد -->
<section class="section about-section" id="about">
    <div class="container">
        <h2 class="section-title">من نحن؟</h2>  
        <div class="about-container">
            <div class="about-content">
                <h3 style="color: #2c3e50; margin-bottom: 20px;">شركة يمنية رائدة في مجال الحلول البرمجية</h3>
                <p style="margin-bottom: 20px; line-height: 1.8;">
                    شركة يمنية رائدة متخصصة في تصميم وتطوير الأنظمة المحاسبية، الحلول الإدارية، وتطبيقات الويب والجوال. من عروس البحر الاحمر "الحديدة" انطلقنا وعلى مدار عشرون 20 عاما من التطور ومواكبة التكنولوجيا في العالم كي نلبي احتياجات سوق العمل التجاري والإداري والمهني.
                </p>
                <p style="margin-bottom: 20px; line-height: 1.8;">
                    نحن نمزج بين الخبرة التقنية العميقة والفهم الدقيق لاحتياجات السوق المحلي لنقدم حلولاً تكنولوجية تدفع أعمال عملائنا نحو النمو والنجاح.
                </p>    
             
            </div>
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="فريق سراج سوفت">
            </div>
        </div>
        <div class="about-stats">  
    <?php
    // جلب الإحصاءات من قاعدة البيانات
    $stats_query = "SELECT * FROM statistics ORDER BY created_at DESC";
    $stats_stmt = $db->prepare($stats_query);
    $stats_stmt->execute();
    
    while ($stat = $stats_stmt->fetch(PDO::FETCH_ASSOC)): 
    ?>
    
    <div class="stat-item">
        <span class="stat-number"><?php echo $stat['value']; ?>+</span>
        <span class="stat-text"><?php echo $stat['title']; ?></span>
    </div>
  
 <?php 
    endwhile; 
    // إذا لم توجد إحصاءات، استخدم القيم الافتراضية
    if ($stats_stmt->rowCount() == 0): 
    ?>
    
    <div class="stat-item">
        <span class="stat-number">20+</span>
        <span class="stat-text">سنوات من الخبرة</span>
    </div>
    <div class="stat-item">
        <span class="stat-number">100+</span>
        <span class="stat-text">مشروع ناجح</span>
    </div>
    <div class="stat-item">
        <span class="stat-number">50+</span>
        <span class="stat-text">عميل راضٍ</span>
    </div>
    <?php endif; ?>
                    </div>     
                </div>
            </div>
        </div>
    </section>
<div class="logos">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
      </div>
<?php
$servername = "localhost";
$username   = "root";       // غيّر حسب إعدادك
$password   = "";           // غيّر حسب إعدادك
$dbname     = "seragsoft_db";  // غيّر حسب قاعدة بياناتك

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}
// جلب البيانات فقط إذا كانت مفعّلة is_active=1
$sql = "SELECT * FROM sirajsoft_features WHERE is_active = 1 ORDER BY display_order ASC";
$result = $conn->query($sql);
?>
<section>   <h3 class="section-title" style="margin-top: 60px;">لماذا تختار سراج سوفت؟</h3>
<div class="features-container">
    <div class="features">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="feature">
                    <i class="<?php echo htmlspecialchars($row['icon']); ?>"></i>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>لا توجد مميزات مضافة حالياً</p>
        <?php endif; ?>
    </div>
</div>
<?php $conn->close(); ?>
</section>
<div class="logos">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
      </div>
<!-- قسم العملاء -->
<section class="section clients" id="clients">
    <div class="container">
        <h2 class="section-title">قصص نجاح نفخر بها</h2>
        <div class="client-logos">
            <?php 
            // جلب العملاء من قاعدة البيانات
            $clients_query = "SELECT * FROM clients ORDER BY id ASC";
            $clients_stmt = $db->prepare($clients_query);
            $clients_stmt->execute();
            
            if ($clients_stmt->rowCount() > 0) {
                // عرض العملاء من قاعدة البيانات
                while ($client = $clients_stmt->fetch(PDO::FETCH_ASSOC)) {
                    $logo_path = 'assets/img/clients/' . $client['logo'];
                    $default_logo_path = 'assets/img/icon.png';
                    
                    // التحقق من وجود صورة العميل
                    if (file_exists($logo_path)) {
                        $image_src = $logo_path;
                    } else {
                        $image_src = $default_logo_path;
                    }
                    echo '<div class="client-logo">';
                    echo '<img src="' . $image_src . '" alt="' . $client['name'] . '" title="' . $client['name'] . '">';
                    echo '</div>';
                }
            } else {
                // إذا لم توجد بيانات في قاعدة البيانات
                echo '<div class="alert alert-warning text-center">';
                echo 'لم يتم إضافة العملاء إلى قاعدة البيانات بعد.';
                echo '</div>';
                
                // عرض الصور مباشرة من المجلد كحل بديل
                for ($i = 1; $i <= 24; $i++) {
                    $image_name = "client{$i}.png";
                    $image_path = "assets/img/clients/{$image_name}";
                    $default_image = "assets/img/icon.png";
                    
                    if (file_exists($image_path)) {
                        $image_src = $image_path;
                    } else {
                        $image_src = $default_image;
                    }
                    
                    echo '<div class="client-logo">';
                    echo '<img src="' . $image_src . '" alt="عميل ' . $i . '">';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</section>
<div class="logos">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
     <img class="altaf-slap" src="assets/img/ico1n.png">
      </div>
      <!-- قسم التواصل بنا مع الخريطة -->
       
    <section class="section contact" id="contact">
    <div class="container">
        <h2 class="section-title">هل أنت مستعد لبدء مشروعك؟</h2>
        <p style="text-align: center; margin-bottom: 40px;">تواصل مع فريقنا اليوم للحصول على استشارة مجانية</p>  
        <div class="contact-map-container">
            <div class="contact-form">
<form id="contactForm" action="process_contact.php" method="POST">
    <div class="form-group">
        <label for="name">الاسم الكامل</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="phone">رقم الهاتف</label>
        <input type="tel" id="phone" name="phone">
    </div>
    <div class="form-group">
        <label for="message">الرسالة</label>
        <textarea id="message" name="message" required></textarea>
    </div>
    <button class="btn" type="submit">إرسال الرسالة</button>
</form>
            </div>
            <div class="map-side">
                <div id="company-map">
                    <div class="map-placeholder">
                        <div>
                            <i class="fas fa-map-marked-alt"></i>
                            <p>جاري تحميل خريطة الموقع...</p>
                        </div>        
                    </div>    
                </div>
            </div>
        </div>
    </div>
</section>
<section class="contact-info-section">
    <div class="contact-info-container">
        <div class="contact-info-card">
            <i class="fas fa-map-marker-alt"></i>
            <h3>عنواننا</h3>
            <p>اليمن - صنعاء - جولة ريماس<br>جوار عالم العسل اليمني</p>
        </div>
        <div class="contact-info-card">
            <i class="fas fa-phone"></i>
            <h3>اتصل بنا</h3>
            <p><a href="tel:+967772288443" dir="ltr">+967 772 288 443</a></p>
        </div>
        <div class="contact-info-card">
            <i class="fab fa-whatsapp"></i>
            <h3>واتساب</h3>
            <p><a href="https://wa.me/967774400559" target="_blank" dir="ltr">+967 774 400 559</a></p>
        </div> 
        <div class="contact-info-card">
            <i class="fas fa-envelope"></i>
            <h3>البريد الإلكتروني</h3>
            <p><a href="mailto:Seragsoft1@gmail.com">Seragsoft1@gmail.com</a></p>
        </div> </div>
</section>
<!-- زر العودة للأعلى -->
<div class="back-to-top" id="backToTop">
    <i class="fas fa-chevron-up"></i>
</div>
<?php
include 'includes/footer.php';
?>

<script>
// كود JavaScript لزر العودة للأعلى
document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('backToTop');
    
    // إظهار أو إخفاء زر العودة للأعلى عند التمرير
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('show');
        } else {
            backToTopButton.classList.remove('show');
        }
    });
    // النقر على زر العودة للأعلى
    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    
    fetch('process_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        var messageDiv = document.getElementById('responseMessage');
        if (data.success) {
            messageDiv.innerHTML = '<p style="color:green;">' + data.message + '</p>';
            document.getElementById('contactForm').reset();
        } else {
            messageDiv.innerHTML = '<p style="color:red;">' + data.message + '</p>';
        }
    })
    .catch(error => {
        document.getElementById('responseMessage').innerHTML = '<p style="color:red;">حدث خطأ في الإرسال</p>';
    });
});
</script>
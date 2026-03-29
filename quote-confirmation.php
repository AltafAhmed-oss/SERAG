<?php
// quote-confirmation.php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// تفعيل الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// التحقق من أن المستخدم أرسل طلباً
if (!isset($_SESSION['last_quote'])) {
    header('Location: request-quote.php');
    exit;
}

$quoteData = $_SESSION['last_quote'];
$page_title = "تأكيد طلب عرض السعر";
include 'includes/header.php';
?>

<style>
.confirmation-section {
    padding: 100px 0;
    background: #f8f9fa;
    min-height: 70vh;
}

.confirmation-container {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: 20px;
    padding: 50px;
    box-shadow: 0 20px 50px rgba(44, 62, 80, 0.1);
    text-align: center;
}

.confirmation-icon {
    font-size: 5rem;
    color: #27ae60;
    margin-bottom: 30px;
    animation: bounce 1s ease infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

.confirmation-container h1 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 2.5rem;
}

.confirmation-container p {
    color: #5a6c7d;
    font-size: 1.2rem;
    line-height: 1.8;
    margin-bottom: 25px;
}

.quote-details {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 30px;
    margin: 30px 0;
    text-align: right;
    border: 2px solid #e9ecef;
}

.quote-details h3 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 1.5rem;
    padding-bottom: 10px;
    border-bottom: 2px solid #2c3e50;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #2c3e50;
}

.detail-value {
    color: #5a6c7d;
}

.reference-number {
    font-size: 2rem;
    color: #2c3e50;
    font-weight: 800;
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    border: 2px solid #2c3e50;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 40px;
    flex-wrap: wrap;
}

.btn {
    padding: 15px 30px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-primary {
    background: #2c3e50;
    color: white;
    border: 2px solid #2c3e50;
}

.btn-primary:hover {
    background: #34495e;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(44, 62, 80, 0.2);
}

.btn-secondary {
    background: white;
    color: #2c3e50;
    border: 2px solid #2c3e50;
}

.btn-secondary:hover {
    background: #f8f9fa;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(44, 62, 80, 0.1);
}

.next-steps {
    margin-top: 40px;
    padding: 25px;
    background: #f8f9fa;
    border-radius: 15px;
    border-right: 5px solid #2c3e50;
}

.next-steps h3 {
    color: #2c3e50;
    margin-bottom: 15px;
    font-size: 1.3rem;
}

.next-steps ol {
    text-align: right;
    padding-right: 20px;
    color: #5a6c7d;
    line-height: 1.8;
}

.next-steps li {
    margin-bottom: 10px;
}

@media (max-width: 768px) {
    .confirmation-container {
        padding: 30px 20px;
    }
    
    .confirmation-container h1 {
        font-size: 2rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}

/* تحديث الفوتر */
footer {
    background: #2c3e50 !important;
    color: white;
    padding: 60px 0 30px;
}

/* تحديث الكتابة في الفوتر إلى الأبيض */
footer {
    background: #2c3e50;
    color: white !important; /* نص أبيض */
    padding: 60px 0 30px;
}

.footer-section h3 {
    color: white !important; /* عناوين بيضاء */
    font-size: 1.3rem;
    margin-bottom: 20px;
    position: relative;
    padding-bottom: 10px;
}

.footer-section h3:after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 50px;
    height: 2px;
    background-color: white;
}

.footer-links {
    list-style: none;
}

.footer-links li {
    margin-bottom: 10px;
    transition: all 0.3s;
}

.footer-links li:hover {
    transform: translateX(-5px);
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

.contact-details {
    list-style: none;
}

.contact-details li {
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.contact-details i {
    margin-left: 10px;
    color: #bdc3c7;
    font-size: 1.2rem;
    width: 25px;
}

.contact-details span {
    color: #bdc3c7 !important; /* تفاصيل الاتصال رمادية فاتحة */
}

.copyright {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
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

/* ========== زر التواصل الدائري ========== */
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

/* ========== زر العودة للأعلى ========== */
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
    border: 2px solid white;
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

/* ========== إزالة أي آثار للون الأحمر ========== */
.service-option i[class*="fa-"],
.pricing-icon {
    color: #2c3e50 !important;
    background: none !important;
    -webkit-text-fill-color: #2c3e50 !important;
}

/* تحسين تنسيقات النص */
.section-title {
    color: #2c3e50 !important;
}

/* تحسينات للأيقونات */
.fa-check {
    color: #27ae60 !important;
}

/* إزالة أي outline أحمر من جميع العناصر */
*:focus {
    outline: none !important;
    box-shadow: none !important;
}

/* إزالة أي حدود حمراء */
.service-option,
.feature-option,
.pricing-card,
.contact-info-card {
    border-color: #e9ecef !important;
}
/* إضافة هذه الأنماط في قسم CSS الخاص بالصفحة */
.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    display: none;
}
.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<section class="confirmation-section">
    <div class="container">
        <div class="confirmation-container">
            <i class="fas fa-check-circle confirmation-icon"></i>
            <h1>تم إرسال طلبك بنجاح!</h1>
            <p>شكراً لتواصلك مع سراج سوفت. لقد استلمنا طلب عرض السعر الخاص بمشروعك وسنقوم بمراجعته من قبل فريقنا المختص.</p>
            
            <div class="reference-number">
                رقم المرجع: <?php echo htmlspecialchars($quoteData['reference']); ?>
            </div>
            
            <div class="quote-details">
                <h3>تفاصيل الطلب</h3>
                <div class="detail-item">
                    <span class="detail-label">الاسم:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($quoteData['name']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">البريد الإلكتروني:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($quoteData['email']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">تاريخ الطلب:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($quoteData['timestamp']); ?></span>
                </div>
            </div>
            
            <div class="next-steps">
                <h3>الخطوات التالية:</h3>
                <ol>
                    <li>سيقوم فريقنا بمراجعة طلبك وتحليل متطلبات المشروع</li>
                    <li>سنتواصل معك خلال 24 ساعة عمل لعرض السعر المخصص</li>
                    <li>سنقدم لك استشارة مجانية لفهم احتياجاتك بشكل أفضل</li>
                    <li>ستحصل على عرض سعر مفصل يتضمن جميع التكاليف والجداول الزمنية</li>
                </ol>
            </div>
            
            <div class="action-buttons">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i>
                    العودة للصفحة الرئيسية
                </a>
                <a href="request-quote.php" class="btn btn-secondary">
                    <i class="fas fa-plus"></i>
                    طلب عرض سعر جديد
                </a>
                <a href=" https://wa.me/+967772288443" class="btn btn-secondary">
                    <i class="fas fa-phone"></i>
                    اتصل بنا
                </a>
            </div>
            
            <p style="margin-top: 30px; color: #7f8c8d; font-size: 1rem;">
                <i class="fas fa-info-circle" style="margin-left: 8px;"></i>
                لأي استفسار، يمكنك الاتصال على: <a href="tel:+967772288443" style="color: #2c3e50;">+967 772 288 443</a>
            </p>
        </div>
    </div>
</section>

<?php
// مسح بيانات الجلسة بعد العرض
unset($_SESSION['last_quote']);
include 'includes/footer.php';
?>
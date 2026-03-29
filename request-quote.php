<?php
// request-quote.php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// توليد CSRF token للأمان
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// توليد رمز CSRF إذا لم يكن موجوداً
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_title = "طلب عرض سعر";
include 'includes/header.php';
?>

<style>
/* ========== أنماط طلب عرض السعر ========== */

/* تنسيقات إضافية للصفحة */
.quote-hero {
    background: linear-gradient(135deg, rgba(44, 62, 80, 0.9), rgba(52, 73, 94, 0.9)),
                url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
    background-size: cover;
    background-position: center;
    color: white;
    padding: 120px 0 80px;
    text-align: center;
    position: relative;
}

.quote-hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.quote-hero h1 {
    font-size: 3rem;
    margin-bottom: 20px;
    animation: fadeInUp 1s ease;
}

.quote-hero p {
    font-size: 1.3rem;
    margin-bottom: 30px;
    line-height: 1.6;
    animation: fadeInUp 1.2s ease;
}

.quote-hero-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-top: 40px;
    animation: fadeInUp 1.4s ease;
}

.hero-stat {
    text-align: center;
}

.hero-stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: white;
    margin-bottom: 10px;
    display: block;
}

.hero-stat-text {
    font-size: 1.1rem;
    opacity: 0.9;
}

/* قسم عرض الأسعار */
.pricing-section {
    padding: 100px 0;
    background: #ffffff;
}

.pricing-intro {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 60px;
}

.pricing-intro h2 {
    color: #2c3e50;
    font-size: 2.2rem;
    margin-bottom: 20px;
    position: relative;
    padding-bottom: 15px;
}

.pricing-intro h2:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: #2c3e50;
}

.pricing-intro p {
    color: #5a6c7d;
    font-size: 1.1rem;
    line-height: 1.7;
}

/* خطوات طلب السعر */
.quote-steps {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-bottom: 60px;
    flex-wrap: wrap;
}

.quote-step {
    text-align: center;
    max-width: 250px;
    position: relative;
}

.quote-step-number {
    width: 60px;
    height: 60px;
    background: #2c3e50;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0 auto 20px;
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

.quote-step:hover .quote-step-number {
    background: #34495e;
    transform: scale(1.1);
}

.quote-step-number::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 100%;
    width: 40px;
    height: 2px;
    background: #ecf0f1;
    z-index: 1;
}

.quote-step:last-child .quote-step-number::after {
    display: none;
}

.quote-step h3 {
    color: #2c3e50;
    margin-bottom: 15px;
    font-size: 1.3rem;
}

.quote-step p {
    color: #5a6c7d;
    line-height: 1.6;
}

/* أسعار الخدمات */
.services-pricing {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 80px;
}

.pricing-card {
    background: #ffffff;
    border-radius: 15px;
    padding: 40px 30px;
    text-align: center;
    box-shadow: 0 15px 40px rgba(44, 62, 80, 0.1);
    border: 2px solid #ecf0f1;
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.pricing-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(90deg, #34495e, #2c3e50);
    transition: all 0.3s ease;
}

.pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(44, 62, 80, 0.15);
    border-color: #2c3e50;
}

.pricing-card:hover::before {
    height: 7px;
}

.pricing-icon {
    font-size: 3.5rem;
    color: #2c3e50;
    margin-bottom: 25px;
    transition: all 0.3s ease;
}

.pricing-card:hover .pricing-icon {
    transform: scale(1.1);
}

.pricing-card h3 {
    color: #2c3e50;
    font-size: 1.6rem;
    margin-bottom: 15px;
    position: relative;
    padding-bottom: 10px;
}

.pricing-card h3:after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 50%;
    transform: translateX(50%);
    width: 40px;
    height: 2px;
    background: #2c3e50;
    transition: width 0.4s ease;
}

.pricing-card:hover h3:after {
    width: 70px;
}

.pricing-features {
    list-style: none;
    margin: 25px 0;
    text-align: right;
}

.pricing-features li {
    padding: 8px 0;
    color: #5a6c7d;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.pricing-features li i {
    color: #27ae60;
    margin-left: 10px;
}

.pricing-amount {
    font-size: 2.5rem;
    font-weight: 800;
    color: #2c3e50;
    margin: 25px 0;
}

.pricing-amount span {
    font-size: 1rem;
    color: #7f8c8d;
    font-weight: normal;
}

/* قسم النموذج */
.quote-form-section {
    padding: 80px 0;
    background: #f8f9fa;
    scroll-margin-top: 100px; /* إضافة هذا لتحسين الانتقال */
}

.quote-form-container {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: 20px;
    padding: 50px;
    box-shadow: 0 20px 50px rgba(44, 62, 80, 0.1);
    border: 1px solid #e9ecef;
}

.quote-form-container h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 40px;
    font-size: 2rem;
    position: relative;
    padding-bottom: 15px;
}

.quote-form-container h2:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: #2c3e50;
}

/* تحسينات للنموذج */
.quote-form .form-group {
    margin-bottom: 25px;
}

.quote-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.quote-form label {
    display: block;
    margin-bottom: 8px;
    color: #2c3e50;
    font-weight: 600;
    font-size: 1rem;
}

.quote-form input,
.quote-form select,
.quote-form textarea {
    width: 100%;
    padding: 14px 20px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    font-family: inherit;
}

.quote-form input:focus,
.quote-form select:focus,
.quote-form textarea:focus {
    outline: none;
    border-color: #2c3e50;
    box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
}

.quote-form textarea {
    min-height: 150px;
    resize: vertical;
}

/* اختيار الخدمات */
.service-selection {
    margin: 30px 0;
}

.service-selection label {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 15px;
    display: block;
}

.service-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.service-option {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    position: relative;
}

.service-option:hover {
    border-color: #2c3e50;
    transform: translateY(-3px);
}

.service-option.selected {
    border-color: #2c3e50;
    background: #f8f9fa;
    box-shadow: 0 5px 15px rgba(44, 62, 80, 0.1);
}

.service-option.selected::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 5px;
    background: #2c3e50;
}

.service-option i {
    font-size: 2rem;
    color: #2c3e50;
    margin-bottom: 10px;
    display: block;
    transition: all 0.3s ease;
}

.service-option.selected i {
    color: #34495e;
    transform: scale(1.1);
}

.service-option h4 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.service-option.selected h4 {
    color: #34495e;
    font-weight: bold;
}

/* قسم المميزات */
.features-selection {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 30px;
    margin: 30px 0;
    border: 1px solid #e9ecef;
}

.features-selection label {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 20px;
    display: block;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.feature-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    cursor: pointer;
}

.feature-option:hover {
    border-color: #2c3e50;
    background: #f8f9fa;
    transform: translateX(-5px);
}

.feature-option input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #2c3e50;
    cursor: pointer;
}

.feature-option label {
    margin: 0;
    cursor: pointer;
    color: #34495e;
    font-weight: 500;
    flex: 1;
}

/* زر الإرسال */
.btn-submit-quote {
    width: 100%;
    background: #2c3e50;
    color: white;
    border: none;
    padding: 16px;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 30px;
    position: relative;
    overflow: hidden;
}

.btn-submit-quote::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, #34495e, #2c3e50);
    transition: all 0.3s ease;
    z-index: 1;
}

.btn-submit-quote:hover::before {
    opacity: 0.9;
}

.btn-submit-quote span,
.btn-submit-quote i {
    position: relative;
    z-index: 2;
}

.btn-submit-quote:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(44, 62, 80, 0.2);
}

.btn-submit-quote:disabled {
    background: #bdc3c7;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.btn-submit-quote:disabled::before {
    display: none;
}

/* رسالة النجاح */
.success-message {
    display: none;
    text-align: center;
    padding: 40px;
    background: white;
    border-radius: 10px;
    border: 2px solid #2c3e50;
    margin-top: 30px;
    box-shadow: 0 10px 30px rgba(44, 62, 80, 0.1);
}

.success-message i {
    font-size: 4rem;
    color: #27ae60;
    margin-bottom: 20px;
    animation: bounce 1s ease infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.success-message h3 {
    color: #2c3e50;
    font-size: 1.8rem;
    margin-bottom: 15px;
}

.success-message p {
    color: #5a6c7d;
    font-size: 1.1rem;
    line-height: 1.6;
}

.success-message strong {
    color: #2c3e50;
}

/* قسم الأسئلة الشائعة */
.faq-section {
    padding: 80px 0;
    background: #ffffff;
}

.faq-section h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 50px;
    font-size: 2.2rem;
    position: relative;
    padding-bottom: 15px;
}

.faq-section h2:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: #2c3e50;
}

.faq-container {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    margin-bottom: 15px;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-item:hover {
    border-color: #2c3e50;
}

.faq-question {
    padding: 20px;
    background: #f8f9fa;
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.faq-question:hover {
    background: #edf0f2;
}

.faq-question i {
    transition: transform 0.3s ease;
    color: #2c3e50;
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 0 20px;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
    color: #5a6c7d;
    line-height: 1.6;
}

.faq-item.active .faq-answer {
    padding: 20px;
    max-height: 500px;
}

/* ========== أكواد مشتركة مع الصفحة الرئيسية ========== */

/* إضافة تنسيقات الرأس والفوتر لتتناسب مع الصفحة الرئيسية */
header {
    background: #2c3e50 !important;
    color: white;
    padding: 1px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(44, 62, 80, 0.2);
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

/* ========== أنماط مشتركة للأزرار ========== */
.btn {
    display: inline-block;
    background: #2c3e50;
    color: white;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, #34495e, #2c3e50);
    transition: all 0.3s ease;
    z-index: 1;
}

.btn span {
    position: relative;
    z-index: 2;
}

.btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(213, 216, 219, 0.3);
}

.btn:hover::before {
    opacity: 0.9;
}

/* ========== رسائل التنبيه ========== */
.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
    display: none;
    text-align: center;
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

/* ========== تنسيقات متجاوبة ========== */
@media (max-width: 768px) {
    .quote-hero {
        padding: 80px 0 60px;
    }
    
    .quote-hero h1 {
        font-size: 2.2rem;
    }
    
    .quote-hero p {
        font-size: 1.1rem;
    }
    
    .quote-hero-stats {
        flex-direction: column;
        gap: 20px;
    }
    
    .quote-steps {
        gap: 30px;
    }
    
    .quote-step-number::after {
        display: none;
    }
    
    .services-pricing {
        grid-template-columns: 1fr;
    }
    
    .quote-form-container {
        padding: 30px 20px;
    }
    
    .quote-form .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .service-options {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    /* تحسينات للجوال */
    .footer-content {
        flex-direction: column;
        gap: 30px;
    }
    
    .footer-section {
        min-width: 100%;
    }
    
    .social-icons {
        justify-content: center;
    }
    
    .contact-circle {
        bottom: 20px;
        left: 20px;
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .contact-popup {
        bottom: 100px;
        left: 20px;
        width: 280px;
    }
    
    .back-to-top {
        bottom: 20px;
        right: 20px;
        width: 45px;
        height: 45px;
    }
}

@media (max-width: 480px) {
    .quote-hero h1 {
        font-size: 1.8rem;
    }
    
    .pricing-card {
        padding: 30px 20px;
    }
    
    .service-options {
        grid-template-columns: 1fr;
    }
    
    .contact-circle {
        bottom: 15px;
        left: 15px;
        width: 55px;
        height: 55px;
        font-size: 1.4rem;
    }
    
    .contact-popup {
        bottom: 90px;
        left: 15px;
        width: 260px;
    }
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

/* ========== تنسيقات إضافية للأزرار ========== */
.select-service-btn {
    background: #2c3e50 !important;
    color: white !important;
    border: 2px solid #2c3e50 !important;
    padding: 12px 25px !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    margin-top: 20px !important;
    width: 100% !important;
    position: relative !important;
    overflow: hidden !important;
}

.select-service-btn:hover {
    background: #34495e !important;
    border-color: #34495e !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 10px 20px rgba(44, 62, 80, 0.2) !important;
}

.select-service-btn:active {
    transform: translateY(-1px) !important;
}

/* تأثير النقر */
.select-service-btn.clicked {
    animation: clickEffect 0.5s ease;
}

@keyframes clickEffect {
    0% { transform: scale(1); }
    50% { transform: scale(0.95); }
    100% { transform: scale(1); }
}

/* تأثيرات إضافية عند الاختيار */
.quote-form-container.highlight {
    animation: highlightSection 1.5s ease;
}

@keyframes highlightSection {
    0% {
        box-shadow: 0 0 0 0 rgba(44, 62, 80, 0.3);
    }
    70% {
        box-shadow: 0 0 0 15px rgba(44, 62, 80, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(44, 62, 80, 0);
    }
}
</style>

<!-- قسم الهيدر الخاص -->
<section class="quote-hero">
    <div class="container">
        <div class="quote-hero-content">
            <h1>احصل على عرض سعر مخصص لمشروعك</h1>
            <p>نقدم لك تقييماً دقيقاً وتخطيطاً استراتيجياً لمشروعك التقني. أخبرنا بمتطلباتك وسنقدم لك أفضل حل يتناسب مع ميزانيتك وأهدافك.</p>
            
            <div class="quote-hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number">24</span>
                    <span class="hero-stat-text">ساعة للرد</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">100%</span>
                    <span class="hero-stat-text">ضمان الجودة</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">مجاناً</span>
                    <span class="hero-stat-text">استشارة أولية</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم عرض الأسعار -->
<section class="pricing-section">
    <div class="container">
        <div class="pricing-intro">
            <h2>أسعار خدماتنا</h2>
            <p>نقدم مجموعة متنوعة من الحزم والخدمات التي تناسب احتياجاتك وميزانيتك. جميع الأسعار قابلة للتخصيص حسب متطلبات مشروعك.</p>
        </div>
        
        <!-- خطوات طلب السعر -->
        <div class="quote-steps">
            <div class="quote-step">
                <div class="quote-step-number">1</div>
                <h3>أخبرنا بمتطلباتك</h3>
                <p>املأ النموذج وصف مشروعك بالتفصيل</p>
            </div>
            <div class="quote-step">
                <div class="quote-step-number">2</div>
                <h3>تحليل المشروع</h3>
                <p>فريقنا يحلل متطلباتك ويخطط للحل الأمثل</p>
            </div>
            <div class="quote-step">
                <div class="quote-step-number">3</div>
                <h3>عرض السعر</h3>
                <p>نرسل لك عرض سعر مفصل خلال 24 ساعة</p>
            </div>
            <div class="quote-step">
                <div class="quote-step-number">4</div>
                <h3>بدء التنفيذ</h3>
                <p>نبدأ العمل فور موافقتك على العرض</p>
            </div>
        </div>
        
        <!-- أسعار الخدمات -->
        <div class="services-pricing">
            <div class="pricing-card">
                <i class="fas fa-building pricing-icon"></i>
                <h3>أنظمة ERP</h3>
                <ul class="pricing-features">
                    <li><span>إدارة مالية ومحاسبية</span> <i class="fas fa-check"></i></li>
                    <li><span>إدارة الموارد البشرية</span> <i class="fas fa-check"></i></li>
                    <li><span>إدارة المخازن والمستودعات</span> <i class="fas fa-check"></i></li>
                    <li><span>تقارير وتحليلات متقدمة</span> <i class="fas fa-check"></i></li>
                    <li><span>دعم فني لمدة سنة</span> <i class="fas fa-check"></i></li>
                </ul>
                <div class="pricing-amount">يبدأ من <span>50,000 ريال</span></div>
                <button class="btn select-service-btn" onclick="selectService('erp', true)">
                    <i class="fas fa-check-circle" style="margin-left: 8px;"></i>
                    <span>اختر هذه الخدمة</span>
                </button>
            </div>
            
            <div class="pricing-card">
                <i class="fas fa-mobile-alt pricing-icon"></i>
                <h3>تطبيقات الجوال</h3>
                <ul class="pricing-features">
                    <li><span>تطبيقات iOS و Android</span> <i class="fas fa-check"></i></li>
                    <li><span>تصميم واجهة مستخدم جذابة</span> <i class="fas fa-check"></i></li>
                    <li><span>تكامل مع واجهات برمجة API</span> <i class="fas fa-check"></i></li>
                    <li><span>نشر في متاجر التطبيقات</span> <i class="fas fa-check"></i></li>
                    <li><span>صيانة لمدة 6 أشهر</span> <i class="fas fa-check"></i></li>
                </ul>
                <div class="pricing-amount">يبدأ من <span>25,000 ريال</span></div>
                <button class="btn select-service-btn" onclick="selectService('mobile', true)">
                    <i class="fas fa-check-circle" style="margin-left: 8px;"></i>
                    <span>اختر هذه الخدمة</span>
                </button>
            </div>
            
            <div class="pricing-card">
                <i class="fas fa-laptop-code pricing-icon"></i>
                <h3>تطوير الويب</h3>
                <ul class="pricing-features">
                    <li><span>مواقع تفاعلية متجاوبة</span> <i class="fas fa-check"></i></li>
                    <li><span>متاجر إلكترونية متكاملة</span> <i class="fas fa-check"></i></li>
                    <li><span>لوحات تحكم متقدمة</span> <i class="fas fa-check"></i></li>
                    <li><span>تحسين محركات البحث SEO</span> <i class="fas fa-check"></i></li>
                    <li><span>استضافة وسنة صيانة</span> <i class="fas fa-check"></i></li>
                </ul>
                <div class="pricing-amount">يبدأ من <span>15,000 ريال</span></div>
                <button class="btn select-service-btn" onclick="selectService('web', true)">
                    <i class="fas fa-check-circle" style="margin-left: 8px;"></i>
                    <span>اختر هذه الخدمة</span>
                </button>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <p style="color: #5a6c7d; font-size: 1.1rem;">
                <i class="fas fa-info-circle" style="color: #2c3e50; margin-left: 8px;"></i>
                جميع الأسعار قابلة للتغيير حسب تعقيد المشروع والمتطلبات الإضافية
            </p>
        </div>
    </div>
</section>

<!-- قسم نموذج طلب السعر -->
<section class="quote-form-section">
    <div class="container">
        <div class="quote-form-container">
            <h2>اطلب عرض سعر مخصص</h2>
            
            <div class="alert" id="responseAlert"></div>
            
            <form class="quote-form" id="quoteForm" enctype="multipart/form-data">
                <!-- CSRF Token للأمان -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <!-- معلومات أساسية -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="fullName">الاسم الكامل *</label>
                        <input type="text" id="fullName" name="fullName" required>
                    </div>
                    <div class="form-group">
                        <label for="companyName">اسم الشركة / المؤسسة</label>
                        <input type="text" id="companyName" name="companyName">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">البريد الإلكتروني *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">رقم الهاتف *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                </div>
                
                <!-- اختيار الخدمة -->
                <div class="service-selection">
                    <label>الخدمة المطلوبة *</label>
                    <div class="service-options" id="serviceOptions">
                        <div class="service-option" data-service="erp">
                            <i class="fas fa-building"></i>
                            <h4>أنظمة ERP</h4>
                            <input type="radio" name="service" value="erp" hidden required>
                        </div>
                        <div class="service-option" data-service="mobile">
                            <i class="fas fa-mobile-alt"></i>
                            <h4>تطبيقات الجوال</h4>
                            <input type="radio" name="service" value="mobile" hidden required>
                        </div>
                        <div class="service-option" data-service="web">
                            <i class="fas fa-laptop-code"></i>
                            <h4>تطوير الويب</h4>
                            <input type="radio" name="service" value="web" hidden required>
                        </div>
                        <div class="service-option" data-service="custom">
                            <i class="fas fa-tools"></i>
                            <h4>حلول مخصصة</h4>
                            <input type="radio" name="service" value="custom" hidden required>
                        </div>
                    </div>
                </div>
                
                <!-- نطاق المشروع -->
                <div class="form-group">
                    <label for="projectScope">نطاق المشروع *</label>
                    <select id="projectScope" name="projectScope" required>
                        <option value="">اختر نطاق المشروع</option>
                        <option value="small">مشروع صغير (1-3 أشهر)</option>
                        <option value="medium">مشروع متوسط (3-6 أشهر)</option>
                        <option value="large">مشروع كبير (6+ أشهر)</option>
                        <option value="not-sure">لست متأكداً - أحتاج استشارة</option>
                    </select>
                </div>
                
                <!-- الميزانية المتوقعة -->
                <div class="form-group">
                    <label for="budget">الميزانية المتوقعة</label>
                    <select id="budget" name="budget">
                        <option value="">اختر نطاق الميزانية</option>
                        <option value="0-5000">أقل من 5,000 ريال</option>
                        <option value="5000-10000">5,000 - 10,000 ريال</option>
                        <option value="10000-20000">10,000 - 20,000 ريال</option>
                        <option value="20000-50000">20,000 - 50,000 ريال</option>
                        <option value="50000-100000">50,000 - 100,000 ريال</option>
                        <option value="100000+">أكثر من 100,000 ريال</option>
                        <option value="not-sure">لست متأكداً - أحتاج عرض سعر</option>
                    </select>
                </div>
                
                <!-- المميزات الإضافية -->
                <div class="features-selection">
                    <label style="display: block; margin-bottom: 20px; color: #2c3e50; font-weight: 600;">
                        <i class="fas fa-cogs" style="margin-left: 8px;"></i>
                        المميزات الإضافية المطلوبة
                    </label>
                    <div class="features-grid">
                        <div class="feature-option">
                            <input type="checkbox" id="feature1" name="features[]" value="responsive-design">
                            <label for="feature1">تصميم متجاوب</label>
                        </div>
                        <div class="feature-option">
                            <input type="checkbox" id="feature2" name="features[]" value="multi-language">
                            <label for="feature2">دعم متعدد اللغات</label>
                        </div>
                        <div class="feature-option">
                            <input type="checkbox" id="feature3" name="features[]" value="payment-gateway">
                            <label for="feature3">بوابة دفع إلكتروني</label>
                        </div>
                        <div class="feature-option">
                            <input type="checkbox" id="feature4" name="features[]" value="admin-panel">
                            <label for="feature4">لوحة تحكم متقدمة</label>
                        </div>
                        <div class="feature-option">
                            <input type="checkbox" id="feature5" name="features[]" value="api-integration">
                            <label for="feature5">تكامل مع واجهات برمجة API</label>
                        </div>
                        <div class="feature-option">
                            <input type="checkbox" id="feature6" name="features[]" value="technical-support">
                            <label for="feature6">دعم فني لمدة سنة</label>
                        </div>
                    </div>
                </div>
                
                <!-- وصف المشروع -->
                <div class="form-group">
                    <label for="projectDescription">وصف المشروع بالتفصيل *</label>
                    <textarea id="projectDescription" name="projectDescription" required 
                              placeholder="صف لنا مشروعك بالتفصيل: الأهداف، المميزات المطلوبة، الجمهور المستهدف، أي متطلبات خاصة..."></textarea>
                </div>
                
                <!-- الجدول الزمني -->
                <div class="form-group">
                    <label for="timeline">الجدول الزمني المطلوب</label>
                    <select id="timeline" name="timeline">
                        <option value="">اختر الجدول الزمني</option>
                        <option value="normal">وقت عادي (غير مستعجل)</option>
                        <option value="urgent">مستعجل (مضغوط بالوقت)</option>
                        <option value="flexible">مرن - حسب ما ترونه مناسباً</option>
                    </select>
                </div>
                
                <!-- ملفات مرفقة -->
                <div class="form-group">
                    <label for="attachments">
                        <i class="fas fa-paperclip" style="margin-left: 8px;"></i>
                        ملفات مرفقة (اختياري)
                    </label>
                    <input type="file" id="attachments" name="attachments[]" multiple 
                           accept=".pdf,.doc,.docx,.jpg,.png,.zip">
                    <small style="color: #7f8c8d; display: block; margin-top: 5px;">
                        يمكنك رفع ملفات مثل: وثائق المشروع، صور، wireframes، أي مراجع مفيدة (الحجم الأقصى: 10MB)
                    </small>
                </div>
                
                <!-- زر الإرسال -->
                <button type="submit" class="btn-submit-quote" id="submitBtn">
                    <i class="fas fa-paper-plane"></i>
                    <span id="submitText">إرسال طلب عرض السعر</span>
                    <span id="loadingSpinner" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> جاري الإرسال...
                    </span>
                </button>
            </form>
            
            <!-- رسالة النجاح -->
            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i>
                <h3>تم إرسال طلبك بنجاح!</h3>
                <p>شكراً لتواصلك مع سراج سوفت. سيتم تحليل متطلبات مشروعك وسنتواصل معك خلال 24 ساعة لعرض السعر المخصص.</p>
                <p>رقم المرجع: <strong id="referenceNumber">SRG-<?php echo date('Ymd-His'); ?></strong></p>
                <p>لأي استفسار، يمكنك الاتصال على: <a href="tel:+967772288443" style="color: #2c3e50;">+967 772 288 443</a></p>
            </div>
        </div>
    </div>
</section>

<!-- قسم الأسئلة الشائعة -->
<section class="faq-section">
    <div class="container">
        <h2>أسئلة شائعة حول عروض الأسعار</h2>
        
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    كم من الوقت يستغرق الحصول على عرض سعر؟
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    نهدف للرد على جميع طلبات عروض الأسعار خلال 24 ساعة عمل. قد يستغرق تحليل المشاريع المعقدة وقتاً أطول قليلاً، وسنخطرك بذلك.
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    هل عرض السعر مجاني؟
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    نعم، تقديم عرض السعر والاستشارة الأولية مجاناً تماماً. لا توجد أي تكاليف مسبقة.
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    ماذا يحدث بعد الموافقة على العرض؟
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    بعد موافقتك على عرض السعر، نوقع العقد ونبدأ فوراً في تخطيط المشروع وتخصيص فريق العمل. نقدم خطة عمل مفصلة وجدول زمني واضح.
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    هل يمكنني طلب تعديلات على العرض؟
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    بالتأكيد، يمكنك طبع أي تعديلات أو إضافات على العرض قبل الموافقة النهائية. نعمل معك لضمان أن العرض يلبي جميع متطلباتك.
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    ما هي طرق الدفع المتاحة؟
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    نقدم عدة خيارات للدفع تناسب عملائنا: الدفع الكامل مقدماً، أو تقسيم الدفع على مراحل المشروع، أو الدفع الشهري. يمكنك اختيار ما يناسبك.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- زر العودة للأعلى -->
<div class="back-to-top" id="backToTop">
    <i class="fas fa-chevron-up"></i>
</div>
<script>
// دالة لاختيار الخدمة مع الانتقال للنموذج
function selectService(serviceType, scrollToForm = true) {
    // إزالة التحديد من جميع الخدمات
    const serviceOptions = document.querySelectorAll('.service-option');
    serviceOptions.forEach(option => {
        option.classList.remove('selected');
        const radio = option.querySelector('input[type="radio"]');
        if (radio) radio.checked = false;
    });
    
    // تحديد الخدمة المطلوبة
    const targetOption = document.querySelector(`.service-option[data-service="${serviceType}"]`);
    if (targetOption) {
        targetOption.classList.add('selected');
        const radio = targetOption.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            // تشغيل حدث تغيير
            const changeEvent = new Event('change');
            radio.dispatchEvent(changeEvent);
        }
        
        // إضافة تأثير النقر للأزرار
        const clickedBtn = event ? event.target.closest('.select-service-btn') : null;
        if (clickedBtn) {
            clickedBtn.classList.add('clicked');
            setTimeout(() => {
                clickedBtn.classList.remove('clicked');
            }, 500);
        }
        
        // تحريك العرض لأسفل للنموذج إذا طُلب
        if (scrollToForm) {
            setTimeout(() => {
                const formSection = document.querySelector('.quote-form-section');
                if (formSection) {
                    // استخدام scrollIntoView للانتقال السلس
                    formSection.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start'
                    });
                    
                    // إضافة تأثير تسليط الضوء على النموذج
                    const formContainer = document.querySelector('.quote-form-container');
                    formContainer.classList.add('highlight');
                    setTimeout(() => {
                        formContainer.classList.remove('highlight');
                    }, 1500);
                    
                    // إظهار رسالة نجاح
                    const serviceNames = {
                        'erp': 'أنظمة ERP',
                        'mobile': 'تطبيقات الجوال',
                        'web': 'تطوير الويب',
                        'custom': 'حلول مخصصة'
                    };
                    
                    showAlert(`تم اختيار خدمة "${serviceNames[serviceType]}" بنجاح! يرجى إكمال باقي البيانات أدناه`, 'success');
                }
            }, 300);
        }
    }
}

// دالة للتحقق من صحة البيانات
function validateForm() {
    let isValid = true;
    let errorMessage = '';
    
    // الحقول المطلوبة
    const requiredFields = ['fullName', 'email', 'phone', 'projectDescription'];
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (!field || !field.value.trim()) {
            field.style.borderColor = '#e74c3c';
            isValid = false;
            errorMessage = 'يرجى ملء جميع الحقول المطلوبة';
        } else {
            field.style.borderColor = '#e9ecef';
        }
    });
    
    // التحقق من اختيار خدمة
    const selectedService = document.querySelector('.service-option.selected');
    if (!selectedService) {
        errorMessage = 'يرجى اختيار الخدمة المطلوبة';
        isValid = false;
    }
    
    // التحقق من صحة البريد الإلكتروني
    const email = document.getElementById('email').value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email)) {
        errorMessage = 'يرجى إدخال بريد إلكتروني صحيح';
        document.getElementById('email').style.borderColor = '#e74c3c';
        isValid = false;
    }
    
    // التحقق من رقم الهاتف
    const phone = document.getElementById('phone').value;
    const phoneRegex = /^[\d\s\-\+\(\)]{10,20}$/;
    const cleanPhone = phone.replace(/[^\d]/g, '');
    if (phone && (cleanPhone.length < 10 || cleanPhone.length > 20)) {
        errorMessage = 'يرجى إدخال رقم هاتف صحيح (10-20 رقم)';
        document.getElementById('phone').style.borderColor = '#e74c3c';
        isValid = false;
    }
    
    if (!isValid && errorMessage) {
        showAlert(errorMessage, 'error');
    }
    
    return isValid;
}

// دالة إرسال النموذج
async function submitQuoteForm(event) {
    event.preventDefault();
    
    // التحقق من صحة البيانات
    if (!validateForm()) {
        return;
    }
    
    // عرض حالة التحميل
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    submitText.style.display = 'none';
    loadingSpinner.style.display = 'inline';
    submitBtn.disabled = true;
    
    try {
        // جمع البيانات
        const formData = new FormData(document.getElementById('quoteForm'));
        
        // إرسال البيانات
        const response = await fetch('process-quote.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        // إخفاء حالة التحميل
        submitText.style.display = 'inline';
        loadingSpinner.style.display = 'none';
        submitBtn.disabled = false;
        
        if (data.success) {
            // إظهار رسالة النجاح
            document.getElementById('quoteForm').style.display = 'none';
            document.getElementById('successMessage').style.display = 'block';
            
            if (data.reference) {
                document.getElementById('referenceNumber').textContent = data.reference;
            }
            
            showAlert(data.message, 'success');
            
            // إعادة التوجيه إذا كان هناك رابط
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 3000);
            }
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        console.error('حدث خطأ:', error);
        
        // إخفاء حالة التحميل
        submitText.style.display = 'inline';
        loadingSpinner.style.display = 'none';
        submitBtn.disabled = false;
        
        showAlert('حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.', 'error');
    }
}

// دالة لعرض الرسائل
function showAlert(message, type = 'success') {
    const alertDiv = document.getElementById('responseAlert');
    
    // إخفاء الرسالة السابقة أولاً
    alertDiv.style.display = 'none';
    
    // تعيين النص والفئة
    alertDiv.textContent = message;
    alertDiv.className = 'alert';
    alertDiv.classList.add(type === 'success' ? 'alert-success' : 'alert-error');
    alertDiv.style.display = 'block';
    
    // إخفاء الرسالة بعد 5 ثوان
    setTimeout(() => {
        alertDiv.style.display = 'none';
    }, 5000);
}

// تهيئة الصفحة عند تحميل DOM
document.addEventListener('DOMContentLoaded', function() {
    // تهيئة اختيار الخدمة
    const serviceOptions = document.querySelectorAll('.service-option');
    serviceOptions.forEach(option => {
        option.addEventListener('click', function() {
            serviceOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });
    
    // تهيئة الأسئلة الشائعة
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', function() {
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                }
            });
            item.classList.toggle('active');
        });
    });
    
    // ربط حدث الإرسال
    const quoteForm = document.getElementById('quoteForm');
    if (quoteForm) {
        quoteForm.addEventListener('submit', submitQuoteForm);
    }
    
    // زر العودة للأعلى
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            backToTop.classList.toggle('show', window.pageYOffset > 300);
        });
        
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // إضافة التحقق في الوقت الحقيقي
    const emailField = document.getElementById('email');
    const phoneField = document.getElementById('phone');
    
    if (emailField) {
        emailField.addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                this.style.borderColor = '#e74c3c';
                showAlert('يرجى إدخال بريد إلكتروني صحيح', 'error');
            } else {
                this.style.borderColor = '#e9ecef';
            }
        });
    }
    
    if (phoneField) {
        phoneField.addEventListener('blur', function() {
            const phone = this.value;
            const cleanPhone = phone.replace(/[^\d]/g, '');
            if (phone && (cleanPhone.length < 10 || cleanPhone.length > 20)) {
                this.style.borderColor = '#e74c3c';
                showAlert('يرجى إدخال رقم هاتف صحيح (10-20 رقم)', 'error');
            } else {
                this.style.borderColor = '#e9ecef';
            }
        });
    }
    
    // تحسين الأزرار للانتقال السلس
    document.querySelectorAll('.select-service-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // منع السلوك الافتراضي
            e.preventDefault();
            
            // جلب نوع الخدمة من الزر
            const serviceType = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            
            // استدعاء دالة اختيار الخدمة
            selectService(serviceType, true);
        });
    });
});
</script>
<?php
include 'includes/footer.php';
?>
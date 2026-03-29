 document.addEventListener('DOMContentLoaded', function() {
            // إضافة تأثيرات التمرير عند التمرير العمودي
            function checkScroll() {
                const servicesSection = document.getElementById('services');
                const sectionPosition = servicesSection.getBoundingClientRect();
                
                if (sectionPosition.top < window.innerHeight / 1.3) {
                    servicesSection.classList.add('active');
                }
            }
            
            window.addEventListener('scroll', checkScroll);
            checkScroll(); // التحقق مرة واحدة عند التحميل
        });
        document.addEventListener('DOMContentLoaded', function() {
            // عناصر القائمة للجوال
            const menuToggle = document.getElementById('menuToggle');
            const mainNav = document.getElementById('mainNav');
            const body = document.body; 
            // تفعيل قائمة الجوال
            menuToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                mainNav.classList.toggle('active');
                body.classList.toggle('menu-open');
            });   
            // إغلاق القائمة عند النقر على رابط
            const navLinks = document.querySelectorAll('nav ul li a');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // إذا كانت القائمة مفتوحة (على الجوال)، نغلقها
                    if (window.innerWidth <= 768) {
                        menuToggle.classList.remove('active');
                        mainNav.classList.remove('active');
                        body.classList.remove('menu-open');
                    }          
                    // إنشاء تأثير ripple
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;           
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';            
                    this.appendChild(ripple);            
                    // إزالة تأثير ripple بعد انتهاء الرسوم المتحركة
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);            
                    // الانتقال إلى القسم المطلوب
                    const targetId = this.getAttribute('href');
                    if (targetId.startsWith('#')) {
                        e.preventDefault();
                        const targetSection = document.querySelector(targetId);
                        if (targetSection) {
                            targetSection.scrollIntoView({behavior: 'smooth'});
                        }             }         });     });     
            // إغلاق القائمة عند النقر خارجها (للجوال)
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && 
                    mainNav.classList.contains('active') &&
                    !event.target.closest('nav') && 
                    !event.target.closest('#menuToggle')) {
                    menuToggle.classList.remove('active');
                    mainNav.classList.remove('active');
                    body.classList.remove('menu-open');
                }
            });     
            // منع إغلاق القائمة عند النقر داخلها
            mainNav.addEventListener('click', function(event) {
                event.stopPropagation();
            });       
            // نافذة التواصل المنبثقة
            const contactCircle = document.getElementById('contactCircle');
            const contactPopup = document.getElementById('contactPopup');
            const closePopup = document.getElementById('closePopup');        
            contactCircle.addEventListener('click', function() {
                contactPopup.classList.toggle('active');
            });  
            closePopup.addEventListener('click', function() {
                contactPopup.classList.remove('active');
            });    
            // إغلاق نافذة التواصل عند النقر خارجها
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.contact-popup') && 
                    !event.target.closest('#contactCircle') && 
                    contactPopup.classList.contains('active')) {
                    contactPopup.classList.remove('active');
                }
            });      
            // منع إغلاق النافذة عند النقر داخلها
            contactPopup.addEventListener('click', function(event) {
                event.stopPropagation();
            });     
            // بدء تشغيل السلايدر
            initSlider();        
            // تفعيل نقاط التنقل في قسم الخدمات
            initFeatureNavigation();        
        // تفعيل نموذج الاتصال
            initContactForm();          
            // تهيئة الخريطة
            initMap();
        });    
        // دالة بدء تشغيل السلايدر
        function initSlider() {
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.slider-dot');
            const prevArrow = document.querySelector('.prev-arrow');
            const nextArrow = document.querySelector('.next-arrow');
            let currentSlide = 0;
            let slideInterval;        
            // عرض شريحة محددة
            function showSlide(index) {
                // إخفاء جميع الشرائح
                slides.forEach(slide => slide.classList.remove('active'));
                dots.forEach(dot => dot.classList.remove('active'));             
                // ضمان أن الفهرس ضمن النطاق الصحيح
                if (index >= slides.length) currentSlide = 0;
                else if (index < 0) currentSlide = slides.length - 1;
                else currentSlide = index;           
                // عرض الشريحة الحالية
                slides[currentSlide].classList.add('active');
                dots[currentSlide].classList.add('active');
            }      
            // الانتقال إلى الشريحة التالية
            function nextSlide() {
                showSlide(currentSlide + 1);
            }      
            // الانتقال إلى الشريحة السابقة
            function prevSlide() {
                showSlide(currentSlide - 1);
            }      
            // بدء التشغيل التلقائي للسلايدر
            function startSlideShow() {
                slideInterval = setInterval(nextSlide, 5000);
            }     
            // إيقاف التشغيل التلقائي للسلايدر
            function stopSlideShow() {
                clearInterval(slideInterval);
            }    
            // إضافة مستمعي الأحداث للأسهم
            if (nextArrow) {
                nextArrow.addEventListener('click', function() {
                    stopSlideShow();
                    nextSlide();
                    startSlideShow();
                });
            }   
            if (prevArrow) {
                prevArrow.addEventListener('click', function() {
                    stopSlideShow();
                    prevSlide();
                    startSlideShow();
                });  }  
            // إضافة مستمعي الأحداث لنقاط التنقل
            dots.forEach(dot => {
                dot.addEventListener('click', function() {
                    const slideIndex = parseInt(this.getAttribute('data-slide'));
                    stopSlideShow();
                    showSlide(slideIndex);
                    startSlideShow();
                });
            });  
            // إيقاف التشغيل التلقائي عند تحويم الماوس فوق السلايدر
            const slider = document.querySelector('.hero-slider');
            if (slider) {
                slider.addEventListener('mouseenter', stopSlideShow);
                slider.addEventListener('mouseleave', startSlideShow);
            } 
            // بدء التشغيل التلقائي
            startSlideShow();
        }
        // دالة تفعيل نقاط التنقل في قسم الخدمات
        function initFeatureNavigation() {
            const dots = document.querySelectorAll('.navigation-dots .dot');
            const features = document.querySelectorAll('.feature'); 
            dots.forEach(dot => {
                dot.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    // إزالة الفئة النشطة من جميع النقاط
                    dots.forEach(d => d.classList.remove('active'));
                    // إضافة الفئة النشطة للنقطة المحددة
                    this.classList.add('active');   
                    // تمرير البطاقات إلى المركز
                    const featuresContainer = document.querySelector('.features');
                    const featureWidth = features[0].offsetWidth + 20; // العرض + الهامش
                    featuresContainer.scrollTo({
                        left: index * featureWidth,
                        behavior: 'smooth'
                    });    });   });  }
        // دالة تفعيل نموذج الاتصال
        function initContactForm() {
            const contactForm = document.getElementById('contactForm');
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    e.preventDefault();   
                    // التحقق من صحة البيانات
                    const name = document.getElementById('name').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const message = document.getElementById('message').value.trim();     
                    if (!name || !email || !message) {
                        alert('يرجى ملء جميع الحقول المطلوبة');
                        return;
                    }             
                    // التحقق من صحة البريد الإlectronي
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        alert('يرجى إدخال بريد إلكتروني صحيح');
                        return;
                    }
                    // عرض رسالة نجاح
                    alert('شكراً لك! تم استلام رسالتك وسنتواصل معك قريباً.');   
                    // إعادة تعيين النموذج
                    contactForm.reset();
                }); }  }
        // دالة تهيئة الخريطة
        function initMap() {
            // إحداثيات موقع الشركة (صنعاء، اليمن)
            const companyLocation = [15.3694, 44.1910];  
            // إنشاء الخريطة
            const map = L.map('company-map').setView(companyLocation, 15);   
            // إضافة طبقة الخريطة الأساسية
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);   
            // إضافة علامة للموقع
            const marker = L.marker(companyLocation).addTo(map);   
            // إضافة نافذة منبثقة للعلامة
            marker.bindPopup(`
                <div style="text-align: center;">
                    <h3 style="color: #545454; margin-bottom: 10px;">سراج سوفت</h3>
                    <p>اليمن - صنعاء - جولة ريماس</p>
                    <p>جوار عالم العسل اليمني</p>
                </div>
            `).openPopup();   
            // إضافة دائرة حول الموقع للإشارة إلى المنطقة
            const circle = L.circle(companyLocation, {
                color: '#545454',
                fillColor: '#545454',
                fillOpacity: 0.2,
                radius: 500
            }).addTo(map);
        }
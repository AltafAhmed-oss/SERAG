// assets/js/main.js
document.addEventListener('DOMContentLoaded', function () {
    // تهيئة جميع الصفحات
    initPages();
    initSearch();
    initModals();
    initForms();
    initTables();
    initCharts();
});

// تهيئة الصفحات
function initPages() {
    // التحكم في تغيير الصفحات من القائمة الجانبية
    document.querySelectorAll('.sidebar-menu a[data-page]').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            loadPage(page);
        });
    });
}

// تحميل صفحة
function loadPage(page) {
    fetch(`index.php?page=${page}`)
        .then(response => response.text())
        .then(html => {
            // استخراج محتوى الصفحة من الـ HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const content = tempDiv.querySelector('#content-area');

            if (content) {
                document.getElementById('content-area').innerHTML = content.innerHTML;
                // إعادة تهيئة المكونات بعد تحميل الصفحة
                initComponents();
            }
        })
        .catch(error => console.error('Error loading page:', error));
}

// تهيئة جميع المكونات
function initComponents() {
    // إعادة ربط أحداث الزرود الجديدة
    bindPricingEvents();
    initSearch();
    initModals();
    initForms();
    initTables();
    initCharts();
}

// ربط أحداث الأسعار
function bindPricingEvents() {
    // ربط أحداث أزرار التعديل
    document.querySelectorAll('.edit-pricing-btn').forEach(btn => {
        // إزالة المستمعات السابقة لمنع التكرار
        btn.removeEventListener('click', handleEditPricing);
        btn.addEventListener('click', handleEditPricing);
    });

    // ربط أحداث أزرار الحذف
    document.querySelectorAll('.delete-pricing-btn').forEach(btn => {
        btn.removeEventListener('click', handleDeletePricing);
        btn.addEventListener('click', handleDeletePricing);
    });

    // ربط حدث الأيقونة
    const iconInput = document.getElementById('icon');
    if (iconInput) {
        iconInput.addEventListener('input', updateIconPreview);
    }
}

// معالج تعديل الأسعار
function handleEditPricing(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const id = this.getAttribute('data-id');
    if (id) {
        editPricing(id);
    }
}

// معالج حذف الأسعار
function handleDeletePricing(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const id = this.getAttribute('data-id');
    const title = this.getAttribute('data-title');
    
    if (id && title) {
        showDeleteModal(id, title);
    }
}

// تهيئة البحث
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            // تنفيذ البحث
            performSearch(searchTerm);
        });
    }
}

// تنفيذ البحث
function performSearch(term) {
    // البحث في الصفحة الحالية
    const activePage = document.querySelector('#content-area');
    if (!activePage) return;

    // البحث في الجداول
    const tables = activePage.querySelectorAll('table');
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        let hasResults = false;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(term)) {
                row.style.display = '';
                hasResults = true;
            } else {
                row.style.display = 'none';
            }
        });

        // إظهار/إخفاء رأس الجدول
        const thead = table.querySelector('thead');
        if (thead) {
            thead.style.display = hasResults ? '' : 'none';
        }
    });
}

// تهيئة النوافذ المنبثقة
function initModals() {
    // إغلاق النوافذ عند النقر خارجها
    window.addEventListener('click', function (e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });

    // إغلاق النوافذ عند النقر على زر الإغلاق
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            this.closest('.modal').style.display = 'none';
        });
    });
}

// تهيئة النماذج
function initForms() {
    // معاينة الصور قبل الرفع
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewId = this.getAttribute('id') + 'Preview';
                    const preview = document.getElementById(previewId);
                    if (preview) {
                        preview.innerHTML = `<img src="${e.target.result}" alt="معاينة">`;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });
}

// تهيئة الجداول
function initTables() {
    // إضافة أحداث لأزرار الحذف والتعديل
    document.querySelectorAll('.btn-delete:not(.delete-pricing-btn)').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type') || 'item';
            showDeleteConfirmation(id, type);
        });
    });

    document.querySelectorAll('.btn-edit:not(.edit-pricing-btn)').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type') || 'item';
            editItem(id, type);
        });
    });
}

// تهيئة الرسوم البيانية
function initCharts() {
    if (document.getElementById('visitsChart')) {
        // رسم بياني للزيارات
        const visitsCtx = document.getElementById('visitsChart').getContext('2d');
        new Chart(visitsCtx, {
            type: 'line',
            data: {
                labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                datasets: [{
                    label: 'عدد الزيارات',
                    data: [1200, 1900, 1500, 2200, 1800, 2500],
                    borderColor: '#d30909',
                    backgroundColor: 'rgba(211, 9, 9, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    if (document.getElementById('servicesChart')) {
        // رسم بياني للخدمات
        const servicesCtx = document.getElementById('servicesChart').getContext('2d');
        new Chart(servicesCtx, {
            type: 'doughnut',
            data: {
                labels: ['أنظمة ERP', 'تطبيقات الجوال', 'تطوير الويب', 'حلول مخصصة'],
                datasets: [{
                    data: [35, 25, 20, 20],
                    backgroundColor: [
                        '#d30909',
                        '#ff3838',
                        '#ff6b6b',
                        '#ff9f9f'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

// وظائف مساعدة
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'error'}`;
    alertDiv.textContent = message;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.padding = '15px';
    alertDiv.style.borderRadius = '5px';
    alertDiv.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function showDeleteConfirmation(id, type) {
    currentDeleteItem = id;
    currentDeleteType = type;

    let message = "هل أنت متأكد من أنك تريد حذف هذا العنصر؟";

    const messages = {
        'service': "هل أنت متأكد من أنك تريد حذف هذه الخدمة؟",
        'client': "هل أنت متأكد من أنك تريد حذف هذا العميل؟",
        'message': "هل أنت متأكد من أنك تريد حذف هذه الرسالة؟",
        'statistic': "هل أنت متأكد من أنك تريد حذف هذه الإحصائية؟",
        'user': "هل أنت متأكد من أنك تريد حذف هذا المستخدم؟"
    };

    document.getElementById('deleteConfirmMessage').textContent = messages[type] || message;
    document.getElementById('deleteConfirmModal').style.display = 'flex';
}

function editItem(id, type) {
    // فتح نافذة التعديل المناسبة
    const modalId = `${type}Modal`;
    const modal = document.getElementById(modalId);

    if (modal) {
        // جلب البيانات عبر AJAX
        fetch(`ajax/${type}s_ajax.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success || data.id) {
                    // ملء النموذج بالبيانات
                    Object.keys(data).forEach(key => {
                        const input = document.getElementById(`${type}${capitalizeFirst(key)}`);
                        if (input) {
                            input.value = data[key];
                        }
                    });

                    // إظهار النافذة
                    modal.style.display = 'flex';
                } else {
                    showAlert('حدث خطأ في جلب البيانات', 'error');
                }
            })
            .catch(error => {
                showAlert('حدث خطأ في الاتصال', 'error');
            });
    }
}

function capitalizeFirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// ========== دوال الأسعار المضافة ==========

function editPricing(id) {
    if (!id) return;
    
    console.log('تحرير سعر ID:', id);
    
    // تعيين النص والعنوان
    document.getElementById('pricingModalTitle').textContent = 'تعديل عرض السعر';
    document.getElementById('saveBtnText').textContent = 'حفظ التعديلات';
    
    // إظهار التحميل
    document.getElementById('saveBtnText').style.display = 'none';
    document.getElementById('saveBtnLoading').style.display = 'inline';
    
    // إرسال طلب AJAX
    const formData = new FormData();
    formData.append('action', 'get_pricing');
    formData.append('id', id);
    
    fetch('api/pricing_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('رد الخادم:', data);
        
        if (data.success) {
            // تعبئة النموذج بالبيانات
            document.getElementById('pricingId').value = data.data.id;
            document.getElementById('service_type').value = data.data.service_type;
            document.getElementById('title').value = data.data.title;
            document.getElementById('description').value = data.data.description;
            document.getElementById('icon').value = data.data.icon;
            document.getElementById('starting_price').value = data.data.starting_price;
            document.getElementById('currency').value = data.data.currency;
            document.getElementById('duration').value = data.data.duration;
            document.getElementById('support_duration').value = data.data.support_duration;
            document.getElementById('display_order').value = data.data.display_order;
            document.getElementById('is_active').checked = data.data.is_active == 1;
            
            // تحديث المميزات
            updateFeatures(data.data.features_array || []);
            
            // تحديث معاينة الأيقونة
            updateIconPreview();
            
            // إظهار النافذة
            openModal('pricingModal');
            
            showAlert('تم تحميل البيانات بنجاح', 'success');
        } else {
            showAlert(data.message || 'حدث خطأ في جلب البيانات', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('حدث خطأ في الاتصال بالخادم', 'error');
    })
    .finally(() => {
        document.getElementById('saveBtnText').style.display = 'inline';
        document.getElementById('saveBtnLoading').style.display = 'none';
    });
}

function showDeleteModal(id, title) {
    currentDeleteId = id;
    currentDeleteTitle = title;
    document.getElementById('deleteTitle').textContent = `هل أنت متأكد من حذف العرض "${title}"؟`;
    openModal('deletePricingModal');
}

function updateFeatures(features) {
    const container = document.getElementById('featuresContainer');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (features && features.length > 0) {
        features.forEach(feature => {
            addFeature(feature);
        });
    } else {
        addFeature();
    }
}

function addFeature(value = '') {
    const container = document.getElementById('featuresContainer');
    if (!container) return;
    
    const div = document.createElement('div');
    div.className = 'feature-item';
    div.innerHTML = `
        <input type="text" name="features[]" class="feature-input" placeholder="أدخل ميزة" value="${value}">
        <button type="button" class="btn-feature-remove" onclick="removeFeature(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
}

function updateIconPreview() {
    const iconInput = document.getElementById('icon');
    const preview = document.getElementById('iconPreview');
    
    if (!iconInput || !preview) return;
    
    const icon = iconInput.value;
    
    if (icon && icon.trim()) {
        preview.className = icon;
        preview.style.display = 'inline';
    } else {
        preview.style.display = 'none';
    }
}

// دالة فتح النافذة العامة
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}
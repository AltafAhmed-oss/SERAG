// وظيفة البحث المتقدمة
class AdvancedSearch {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.init();
    }
    
    init() {
        if (this.searchInput) {
            this.searchInput.addEventListener('input', this.handleSearch.bind(this));
        }
    }
    
    handleSearch(e) {
        const searchTerm = e.target.value.trim().toLowerCase();
        const activePage = document.querySelector('.content-page.active');
        
        if (!activePage) return;
        
        if (searchTerm === '') {
            this.resetSearch(activePage);
            return;
        }
        
        this.searchInPage(activePage, searchTerm);
    }
    
    searchInPage(container, searchTerm) {
        let hasResults = false;
        
        // البحث في الجداول
        hasResults = this.searchInTables(container, searchTerm) || hasResults;
        
        // البحث في البطاقات
        hasResults = this.searchInCards(container, searchTerm) || hasResults;
        
        // البحث في النماذج
        hasResults = this.searchInForms(container, searchTerm) || hasResults;
        
        // إظهار رسالة إذا لم توجد نتائج
        this.showNoResultsMessage(container, hasResults, searchTerm);
    }
    
    searchInTables(container, searchTerm) {
        const tables = container.querySelectorAll('table');
        let hasResults = false;
        
        tables.forEach(table => {
            let tableHasResults = false;
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                const isMatch = rowText.includes(searchTerm);
                
                if (isMatch) {
                    row.style.display = '';
                    tableHasResults = true;
                    hasResults = true;
                    this.highlightText(row, searchTerm);
                } else {
                    row.style.display = 'none';
                }
            });
            
            // إظهار/إخفاء رأس الجدول بناءً على وجود نتائج
            const thead = table.querySelector('thead');
            if (thead) {
                thead.style.display = tableHasResults ? '' : 'none';
            }
        });
        
        return hasResults;
    }
    
    searchInCards(container, searchTerm) {
        const cards = container.querySelectorAll('.card, .stat-card');
        let hasResults = false;
        
        cards.forEach(card => {
            const cardText = card.textContent.toLowerCase();
            const isMatch = cardText.includes(searchTerm);
            
            if (isMatch) {
                card.style.display = '';
                hasResults = true;
                this.highlightText(card, searchTerm);
            } else {
                card.style.display = 'none';
            }
        });
        
        return hasResults;
    }
    
    searchInForms(container, searchTerm) {
        const forms = container.querySelectorAll('form');
        let hasResults = false;
        
        forms.forEach(form => {
            const formText = form.textContent.toLowerCase();
            const isMatch = formText.includes(searchTerm);
            
            if (isMatch) {
                form.style.display = '';
                hasResults = true;
                this.highlightText(form, searchTerm);
            } else {
                form.style.display = 'none';
            }
        });
        
        return hasResults;
    }
    
    highlightText(element, searchTerm) {
        if (!searchTerm) return;
        
        // إزالة التمييز السابق
        this.removeHighlights(element);
        
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );
        
        let node;
        const nodes = [];
        
        while (node = walker.nextNode()) {
            if (node.parentNode.nodeName !== 'SCRIPT' && 
                node.parentNode.nodeName !== 'STYLE' &&
                node.parentNode.nodeName !== 'INPUT' &&
                node.parentNode.nodeName !== 'TEXTAREA' &&
                node.parentNode.nodeName !== 'SELECT') {
                nodes.push(node);
            }
        }
        
        nodes.forEach(node => {
            const text = node.nodeValue;
            const regex = new RegExp(`(${this.escapeRegex(searchTerm)})`, 'gi');
            const newText = text.replace(regex, '<mark class="search-highlight">$1</mark>');
            
            if (newText !== text) {
                const span = document.createElement('span');
                span.innerHTML = newText;
                node.parentNode.replaceChild(span, node);
            }
        });
    }
    
    removeHighlights(element) {
        const highlights = element.querySelectorAll('.search-highlight');
        highlights.forEach(highlight => {
            const parent = highlight.parentNode;
            parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
            parent.normalize();
        });
    }
    
    escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    
    showNoResultsMessage(container, hasResults, searchTerm) {
        let message = container.querySelector('.no-results-message');
        
        if (!hasResults && searchTerm) {
            if (!message) {
                message = document.createElement('div');
                message.className = 'no-results-message';
                message.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #6c757d;">
                        <i class="fas fa-search" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;"></i>
                        <h3 style="margin-bottom: 10px;">لا توجد نتائج</h3>
                        <p>لم نتمكن من العثور على أي نتائج تطابق "<strong>${searchTerm}</strong>"</p>
                        <p style="font-size: 0.9rem; margin-top: 10px;">جرب استخدام كلمات بحث أخرى أو تحقق من الأخطاء الإملائية</p>
                    </div>
                `;
                container.appendChild(message);
            }
        } else if (message) {
            message.remove();
        }
    }
    
    resetSearch(container) {
        // إعادة عرض جميع العناصر
        container.querySelectorAll('*').forEach(el => {
            el.style.display = '';
        });
        
        // إزالة التمييز
        this.removeHighlights(container);
        
        // إزالة رسالة عدم وجود نتائج
        const message = container.querySelector('.no-results-message');
        if (message) {
            message.remove();
        }
    }
}

// تهيئة البحث عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    new AdvancedSearch();
});
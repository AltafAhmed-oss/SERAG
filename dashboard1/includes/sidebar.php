<aside class="sidebar">
    <div class="sidebar-header">
        <img src="assets/img/icon.png" alt="سراج سوفت">
        <h2>سراج سوفت</h2>
    </div>
    <nav class="sidebar-menu">
        <ul>
            <li><a href="index.php?page=dashboard" class="<?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>" data-page="dashboard"><i class="fas fa-home"></i> <span>لوحة التحكم</span></a></li>
            <li><a href="index.php?page=statistics" class="<?php echo ($current_page == 'statistics') ? 'active' : ''; ?>" data-page="statistics"><i class="fas fa-chart-bar"></i> <span>إدارة الإحصائيات</span></a></li>
            <li><a href="index.php?page=services" class="<?php echo ($current_page == 'services') ? 'active' : ''; ?>" data-page="services"><i class="fas fa-building"></i> <span>إدارة الخدمات</span></a></li>
            <li><a href="index.php?page=clients" class="<?php echo ($current_page == 'clients') ? 'active' : ''; ?>" data-page="clients"><i class="fas fa-users"></i> <span>العملاء</span></a></li>
            <li><a href="index.php?page=quote_requests" class="<?php echo ($current_page == 'quote_requests') ? 'active' : ''; ?>" data-page="quote_requests"><i class="fas fa-file-invoice-dollar"></i> <span>طلبات عرض السعر</span></a></li>
            <li><a href="index.php?page=users" class="<?php echo ($current_page == 'users') ? 'active' : ''; ?>" data-page="users"><i class="fas fa-user-cog"></i> <span>المستخدمون</span></a></li>
           <li><a href="index.php?page=pricing" class="<?php echo ($current_page == 'pricing') ? 'active' : ''; ?>" data-page="pricing"><i class="fas fa-money-bill-wave"></i> <span>أسعار الخدمات</span></a></li>
            <li><a href="logout.php" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> <span>تسجيل الخروج</span></a></li>
        </ul>
    </nav>
</aside>
<?php
// includes/footer.php
?>
    </div> <!-- إغلاق div.dashboard-container الذي بدأ في header.php -->

    <!-- إضافة أي سكربتات إضافية هنا -->
    <script src="../assets/js/script.js"></script>
    
    <?php if (isset($database) && is_object($database)): ?>
        <?php $database->close(); ?>
    <?php endif; ?>
    
</body>
</html>
<?php
// config/database.php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "seragsoft_db";
    public $conn;
    private $error_log = true;
    
    public function __construct() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
            
            if ($this->conn->connect_error) {
                $this->log_error("فشل الاتصال بقاعدة البيانات: " . $this->conn->connect_error);
                $this->create_database_if_not_exists();
            }
            
            $this->conn->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            $this->log_error("خطأ في إنشاء الاتصال: " . $e->getMessage());
            $this->show_friendly_error();
        }
    }
    
    private function create_database_if_not_exists() {
        // محاولة إنشاء قاعدة البيانات إذا لم تكن موجودة
        $temp_conn = new mysqli($this->host, $this->username, $this->password);
        
        if ($temp_conn->connect_error) {
            $this->show_friendly_error();
            return;
        }
        
        $sql = "CREATE DATABASE IF NOT EXISTS `{$this->dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if ($temp_conn->query($sql)) {
            // إعادة الاتصال بقاعدة البيانات الجديدة
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
            if ($this->conn->connect_error) {
                $this->show_friendly_error();
                return;
            }
            $this->conn->set_charset("utf8mb4");
            $this->log_error("تم إنشاء قاعدة البيانات بنجاح");
        }
        
        $temp_conn->close();
    }
    
    public function escape_string($string) {
        return $this->conn->real_escape_string($string ?? '');
    }

    public function query($sql, $retry_on_error = true) {
        $start_time = microtime(true);
        
        try {
            $result = $this->conn->query($sql);
            
            if (!$result && $retry_on_error) {
                $this->log_error("فشل الاستعلام SQL: " . $this->conn->error . " | SQL: " . $sql);
                
                // محاولة إصلاح بعض الأخطاء الشائعة
                if (strpos($this->conn->error, 'Unknown column') !== false) {
                    // العمود غير موجود، نعيد استعلام بديل
                    $simple_sql = preg_replace('/ORDER BY\s+[^,]+(\s*,\s*[^,]+)*/i', '', $sql);
                    $simple_sql = preg_replace('/WHERE\s+[^=]+=\s*[^\s]+/i', '', $simple_sql);
                    $result = $this->conn->query($simple_sql);
                    
                    if ($result) {
                        $this->log_error("تم استخدام استعلام مبسط بديل");
                    }
                }
            }
            
            $execution_time = microtime(true) - $start_time;
            if ($execution_time > 1) { // إذا استغرق أكثر من ثانية
                $this->log_error("استعلام بطيء: {$execution_time} ثانية | SQL: " . $sql);
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->log_error("خطأ في تنفيذ الاستعلام: " . $e->getMessage() . " | SQL: " . $sql);
            return false;
        }
    }
    
    public function safe_query($table, $columns = '*', $where = '', $order = '', $limit = '') {
        // دالة آمنة للاستعلامات
        $sql = "SELECT $columns FROM $table";
        
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        
        if (!empty($order)) {
            // التحقق من وجود الأعمدة في ORDER BY
            $order_columns = explode(',', $order);
            $valid_columns = $this->get_table_columns($table);
            
            $safe_order = [];
            foreach ($order_columns as $col) {
                $col = trim(preg_replace('/\s+(ASC|DESC)$/i', '', $col));
                if (in_array($col, $valid_columns)) {
                    $safe_order[] = $col;
                }
            }
            
            if (!empty($safe_order)) {
                $sql .= " ORDER BY " . implode(', ', $safe_order);
            }
        }
        
        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->query($sql);
    }
    
    public function get_table_columns($table) {
        $columns = [];
        $result = $this->query("SHOW COLUMNS FROM $table");
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
        }
        
        return $columns;
    }
    
    public function table_exists($table) {
        $result = $this->query("SHOW TABLES LIKE '$table'");
        return $result && $result->num_rows > 0;
    }
    
    public function column_exists($table, $column) {
        $result = $this->query("SHOW COLUMNS FROM $table LIKE '$column'");
        return $result && $result->num_rows > 0;
    }
    
    private function log_error($message) {
        if ($this->error_log) {
            error_log("[SERAGSOFT DB] " . date('Y-m-d H:i:s') . " - " . $message);
            
            // في بيئة التطوير، عرض التنبيهات
            if (php_sapi_name() !== 'cli' && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false)) {
                echo "<!-- DB DEBUG: " . htmlspecialchars($message) . " -->\n";
            }
        }
    }
    
    private function show_friendly_error() {
        if (php_sapi_name() !== 'cli') {
            echo '<div style="background: #f8d7da; color: #721c24; padding: 20px; margin: 20px; border-radius: 5px; border: 1px solid #f5c6cb;">
                <h3 style="margin-top: 0;">⚠️ مشكلة في قاعدة البيانات</h3>
                <p>حدث خطأ في الاتصال بقاعدة البيانات. الرجاء:</p>
                <ol>
                    <li>التحقق من تشغيل خادم MySQL</li>
                    <li>فتح <a href="fix_all_tables.php" style="color: #721c24; font-weight: bold;">fix_all_tables.php</a> لإصلاح قاعدة البيانات</li>
                    <li>التحقق من إعدادات قاعدة البيانات في config/database.php</li>
                </ol>
                <p><a href="fix_all_tables.php" style="background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">إصلاح قاعدة البيانات الآن</a></p>
            </div>';
        }
    }

    public function get_last_id() {
        return $this->conn->insert_id;
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    
    public function __destruct() {
        $this->close();
    }
}

// بدء الاتصال
$database = new Database();
?>
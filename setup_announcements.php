<?php
/**
 * Announcement System Setup
 * Run once to create/verify announcements table
 */

$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'lms_julom';

echo "<h1>Announcement System Setup</h1>";
echo "<pre>";

try {
    $conn = new mysqli($hostname, $username, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "✓ Connected to database\n\n";
    
    // Create announcements table
    echo "Creating/verifying 'announcements' table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS `announcements` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `title` VARCHAR(255) NOT NULL,
      `content` TEXT NOT NULL,
      `target_audience` ENUM('all','admin','teacher','student') DEFAULT 'all',
      `priority` ENUM('low','normal','high','urgent') DEFAULT 'normal',
      `created_by` INT(11) UNSIGNED DEFAULT NULL,
      `created_at` DATETIME DEFAULT NULL,
      `expires_at` DATETIME DEFAULT NULL,
      `is_active` TINYINT(1) DEFAULT 1,
      PRIMARY KEY (`id`),
      KEY `idx_target_audience` (`target_audience`),
      KEY `idx_is_active` (`is_active`),
      KEY `idx_created_at` (`created_at`),
      KEY `fk_announcements_user` (`created_by`),
      CONSTRAINT `fk_announcements_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ 'announcements' table ready!\n\n";
    } else {
        echo "Note: " . $conn->error . "\n\n";
    }
    
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'announcements'");
    if ($result->num_rows > 0) {
        echo "========================================\n";
        echo "<span style='color: green; font-weight: bold;'>SUCCESS! Announcement system ready!</span>\n";
        echo "========================================\n\n";
        echo "Features added:\n";
        echo "✓ Admin can create/manage announcements\n";
        echo "✓ Target specific audiences (all/admin/teacher/student)\n";
        echo "✓ Priority levels (low/normal/high/urgent)\n";
        echo "✓ Optional expiration dates\n";
        echo "✓ Active/inactive toggle\n\n";
        echo "Access:\n";
        echo "- Admin: Dashboard → Announcements section\n";
        echo "- Teachers: Dashboard → Latest Announcements\n";
        echo "- Students: Dashboard → Latest Announcements\n\n";
    }
    
    $conn->close();
    echo "<span style='color: orange;'>⚠ Delete this file: setup_announcements.php</span>\n";
    
} catch (Exception $e) {
    echo "<span style='color: red;'>ERROR: " . $e->getMessage() . "</span>\n";
}

echo "</pre>";
echo "<hr>";
echo "<p><a href='dashboard'>Go to Dashboard</a></p>";
?>


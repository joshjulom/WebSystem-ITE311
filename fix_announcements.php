<?php
$conn = new mysqli('localhost', 'root', '', 'lms_julom');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Fixing Announcements Table</h2>";
echo "<pre>";

// Add missing columns
$columns = [
    "ADD COLUMN `target_audience` ENUM('all','admin','teacher','student') DEFAULT 'all' AFTER `content`",
    "ADD COLUMN `priority` ENUM('low','normal','high','urgent') DEFAULT 'normal' AFTER `target_audience`",
    "ADD COLUMN `created_by` INT(11) UNSIGNED DEFAULT NULL AFTER `priority`",
    "ADD COLUMN `expires_at` DATETIME DEFAULT NULL AFTER `created_at`",
    "ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 AFTER `expires_at`"
];

foreach ($columns as $column) {
    $sql = "ALTER TABLE `announcements` $column";
    echo "Adding column...\n";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Success\n";
    } else {
        // Check if column already exists
        if (strpos($conn->error, 'Duplicate column') !== false) {
            echo "✓ Already exists\n";
        } else {
            echo "✗ Error: " . $conn->error . "\n";
        }
    }
}

// Add indexes
echo "\nAdding indexes...\n";
$indexes = [
    "ADD INDEX `idx_target_audience` (`target_audience`)",
    "ADD INDEX `idx_is_active` (`is_active`)",
    "ADD INDEX `idx_created_at` (`created_at`)"
];

foreach ($indexes as $index) {
    $sql = "ALTER TABLE `announcements` $index";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Index added\n";
    } else {
        if (strpos($conn->error, 'Duplicate key') !== false) {
            echo "✓ Index already exists\n";
        } else {
            echo "Note: " . $conn->error . "\n";
        }
    }
}

// Add foreign key
echo "\nAdding foreign key...\n";
$fk_sql = "ALTER TABLE `announcements` ADD CONSTRAINT `fk_announcements_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL";
if ($conn->query($fk_sql) === TRUE) {
    echo "✓ Foreign key added\n";
} else {
    if (strpos($conn->error, 'Duplicate') !== false) {
        echo "✓ Foreign key already exists\n";
    } else {
        echo "Note: " . $conn->error . "\n";
    }
}

// Verify final structure
echo "\n========================================\n";
echo "Final table structure:\n";
echo "========================================\n";
$result = $conn->query("DESCRIBE announcements");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

echo "\n✅ Announcements table fixed!\n";
echo "You can now access your dashboard.\n";

$conn->close();
echo "</pre>";
?>


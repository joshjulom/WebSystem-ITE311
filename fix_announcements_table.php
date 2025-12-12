<?php
/**
 * Fix announcements table structure to match the model
 */

$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'lms_julom';

echo "<h1>Fixing Announcements Table Structure</h1>";
echo "<pre>";

try {
    $conn = new mysqli($hostname, $username, $password, $database);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    echo "✓ Connected to database\n\n";

    // Check current table structure
    echo "Current table structure:\n";
    $result = $conn->query("DESCRIBE announcements");
    $existingColumns = [];
    while ($row = $result->fetch_assoc()) {
        $existingColumns[] = $row['Field'];
        echo "  - " . $row['Field'] . "\n";
    }

    echo "\n";

    // Add missing columns
    $columnsToAdd = [
        'target_audience' => "ALTER TABLE announcements ADD COLUMN target_audience ENUM('all','admin','teacher','student') DEFAULT 'all' AFTER content",
        'priority' => "ALTER TABLE announcements ADD COLUMN priority ENUM('low','normal','high','urgent') DEFAULT 'normal' AFTER target_audience",
        'created_by' => "ALTER TABLE announcements ADD COLUMN created_by INT(11) UNSIGNED DEFAULT NULL AFTER priority",
        'expires_at' => "ALTER TABLE announcements ADD COLUMN expires_at DATETIME DEFAULT NULL AFTER created_by",
        'is_active' => "ALTER TABLE announcements ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER expires_at"
    ];

    foreach ($columnsToAdd as $column => $sql) {
        if (!in_array($column, $existingColumns)) {
            echo "Adding column: $column\n";
            if ($conn->query($sql) === TRUE) {
                echo "✓ Added $column column\n";
            } else {
                echo "✗ Failed to add $column: " . $conn->error . "\n";
            }
        } else {
            echo "Column $column already exists\n";
        }
    }

    // Add indexes
    echo "\nAdding indexes...\n";
    $indexes = [
        "ALTER TABLE announcements ADD KEY idx_target_audience (target_audience)",
        "ALTER TABLE announcements ADD KEY idx_is_active (is_active)",
        "ALTER TABLE announcements ADD KEY idx_created_at (created_at)",
        "ALTER TABLE announcements ADD KEY fk_announcements_user (created_by)"
    ];

    foreach ($indexes as $indexSql) {
        if ($conn->query($indexSql) === TRUE) {
            echo "✓ Added index\n";
        } else {
            echo "Note: " . $conn->error . "\n";
        }
    }

    // Add foreign key constraint
    echo "\nAdding foreign key constraint...\n";
    $fkSql = "ALTER TABLE announcements ADD CONSTRAINT fk_announcements_user FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL";
    if ($conn->query($fkSql) === TRUE) {
        echo "✓ Added foreign key constraint\n";
    } else {
        echo "Note: " . $conn->error . "\n";
    }

    // Update existing records to have default values
    echo "\nUpdating existing records...\n";
    $updateSql = "UPDATE announcements SET target_audience = 'all', priority = 'normal', is_active = 1 WHERE target_audience IS NULL";
    if ($conn->query($updateSql) === TRUE) {
        echo "✓ Updated existing records\n";
    } else {
        echo "Note: " . $conn->error . "\n";
    }

    echo "\n========================================\n";
    echo "<span style='color: green; font-weight: bold;'>SUCCESS! Table structure fixed!</span>\n";
    echo "========================================\n\n";

    // Show final structure
    echo "Final table structure:\n";
    $result = $conn->query("DESCRIBE announcements");
    while ($row = $result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }

    $conn->close();

} catch (Exception $e) {
    echo "<span style='color: red;'>ERROR: " . $e->getMessage() . "</span>\n";
}

echo "</pre>";
echo "<hr>";
echo "<p><a href='announcements'>Test Announcements Page</a></p>";
?>

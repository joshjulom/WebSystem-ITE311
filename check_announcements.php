<?php
$conn = new mysqli('localhost', 'root', '', 'lms_julom');

if ($conn->connect_error) {
    echo "Connection failed\n";
    exit;
}

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'announcements'");
if ($result && $result->num_rows > 0) {
    echo "✓ Table exists\n\n";
    
    // Get table structure
    echo "Table structure:\n";
    $structure = $conn->query("DESCRIBE announcements");
    while ($row = $structure->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    echo "\nRow count: ";
    $count = $conn->query("SELECT COUNT(*) as cnt FROM announcements");
    $row = $count->fetch_assoc();
    echo $row['cnt'] . "\n";
} else {
    echo "✗ Table does NOT exist\n";
    echo "Need to create it!\n";
}

$conn->close();
?>


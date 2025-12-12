<?php
$conn = new mysqli('localhost', 'root', '', 'lms_julom');

if ($conn->connect_error) {
    echo "Connection failed\n";
    exit;
}

echo "=== ANNOUNCEMENTS DEBUG ===\n\n";

// Check table structure
echo "Table structure:\n";
$result = $conn->query("DESCRIBE announcements");
while ($row = $result->fetch_assoc()) {
    echo "  " . $row['Field'] . " - " . $row['Type'] . " - " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . " - " . ($row['Default'] ?? 'NO DEFAULT') . "\n";
}

echo "\nData in table:\n";
$result = $conn->query("SELECT * FROM announcements");
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " - Title: " . $row['title'] . " - Created: " . $row['created_at'] . "\n";
}

$conn->close();
?>

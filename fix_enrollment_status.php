<?php
/**
 * Quick fix to add enrollment status column
 * Run this file once in your browser: http://localhost/ITE311-JULOM/fix_enrollment_status.php
 */

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'lms_julom';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Fixing Enrollment Table...</h2>";

// Check if status column exists
$checkColumn = $conn->query("SHOW COLUMNS FROM enrollments LIKE 'status'");

if ($checkColumn->num_rows == 0) {
    echo "<p>Adding 'status' column...</p>";
    
    // Add status column
    $sql = "ALTER TABLE enrollments 
            ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' 
            AFTER enrollment_date";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Status column added successfully!</p>";
        
        // Update existing enrollments to 'approved'
        $updateSql = "UPDATE enrollments SET status = 'approved' WHERE status = 'pending'";
        if ($conn->query($updateSql) === TRUE) {
            echo "<p style='color: green;'>✓ Existing enrollments set to 'approved'!</p>";
        }
        
        // Add indexes
        $indexSql1 = "ALTER TABLE enrollments ADD INDEX idx_status (status)";
        $conn->query($indexSql1);
        
        $indexSql2 = "ALTER TABLE enrollments ADD INDEX idx_user_status (user_id, status)";
        $conn->query($indexSql2);
        
        echo "<p style='color: green;'>✓ Indexes added!</p>";
        
    } else {
        echo "<p style='color: red;'>✗ Error adding status column: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ Status column already exists!</p>";
}

$conn->close();

echo "<hr>";
echo "<h3 style='color: green;'>✓ Done! You can now close this page and refresh your dashboard.</h3>";
echo "<p><a href='dashboard'>Go to Dashboard</a></p>";
echo "<hr>";
echo "<p><strong>Note:</strong> You can delete this file (fix_enrollment_status.php) after running it.</p>";
?>


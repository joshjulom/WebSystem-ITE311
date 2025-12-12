<?php
/**
 * Assignment System Setup Script
 * Run this file once to create the assignment tables
 * 
 * Access via browser: http://localhost/ITE311-JULOM/setup_assignments.php
 */

// Database configuration (from app/Config/Database.php)
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'lms_julom';

echo "<h1>Assignment System Setup</h1>";
echo "<pre>";

try {
    // Connect to database
    echo "Connecting to database...\n";
    $conn = new mysqli($hostname, $username, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "✓ Connected successfully!\n\n";
    
    // Create assignments table
    echo "Creating 'assignments' table...\n";
    $sql1 = "CREATE TABLE IF NOT EXISTS `assignments` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `course_id` INT(11) UNSIGNED NOT NULL,
      `title` VARCHAR(255) NOT NULL,
      `description` TEXT,
      `due_date` DATETIME DEFAULT NULL,
      `file_attachment` VARCHAR(255) DEFAULT NULL,
      `created_by` INT(11) UNSIGNED NOT NULL,
      `created_at` DATETIME DEFAULT NULL,
      `updated_at` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `fk_assignments_course` (`course_id`),
      KEY `fk_assignments_user` (`created_by`),
      CONSTRAINT `fk_assignments_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk_assignments_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql1) === TRUE) {
        echo "✓ 'assignments' table created successfully!\n\n";
    } else {
        echo "Note: " . $conn->error . "\n\n";
    }
    
    // Create assignment_submissions table
    echo "Creating 'assignment_submissions' table...\n";
    $sql2 = "CREATE TABLE IF NOT EXISTS `assignment_submissions` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `assignment_id` INT(11) UNSIGNED NOT NULL,
      `student_id` INT(11) UNSIGNED NOT NULL,
      `file_path` VARCHAR(255) DEFAULT NULL,
      `submission_text` TEXT,
      `submission_date` DATETIME DEFAULT NULL,
      `status` ENUM('Submitted','Graded') NOT NULL DEFAULT 'Submitted',
      `grade` DECIMAL(5,2) DEFAULT NULL,
      `feedback` TEXT,
      `graded_at` DATETIME DEFAULT NULL,
      `graded_by` INT(11) UNSIGNED DEFAULT NULL,
      `created_at` DATETIME DEFAULT NULL,
      `updated_at` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `fk_submissions_assignment` (`assignment_id`),
      KEY `fk_submissions_student` (`student_id`),
      KEY `fk_submissions_grader` (`graded_by`),
      CONSTRAINT `fk_submissions_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk_submissions_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk_submissions_grader` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql2) === TRUE) {
        echo "✓ 'assignment_submissions' table created successfully!\n\n";
    } else {
        echo "Note: " . $conn->error . "\n\n";
    }
    
    // Verify tables exist
    $result1 = $conn->query("SHOW TABLES LIKE 'assignments'");
    $result2 = $conn->query("SHOW TABLES LIKE 'assignment_submissions'");
    
    if ($result1->num_rows > 0 && $result2->num_rows > 0) {
        echo "========================================\n";
        echo "<span style='color: green; font-weight: bold;'>SUCCESS! Assignment system is ready!</span>\n";
        echo "========================================\n\n";
        echo "You can now:\n";
        echo "1. Login as a teacher\n";
        echo "2. Go to Dashboard\n";
        echo "3. Click 'Assignments' on any course you teach\n";
        echo "4. Start creating assignments!\n\n";
        echo "Upload directories:\n";
        echo "✓ writable/uploads/assignments/ (created)\n";
        echo "✓ writable/uploads/submissions/ (created)\n\n";
        echo "<span style='color: orange;'>⚠ Important: Delete this setup file now for security!</span>\n";
        echo "File to delete: setup_assignments.php\n";
    } else {
        echo "<span style='color: red;'>ERROR: Tables were not created properly.</span>\n";
        echo "Please check:\n";
        echo "1. Database connection settings\n";
        echo "2. 'courses' and 'users' tables exist\n";
        echo "3. Database user has CREATE TABLE permissions\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<span style='color: red;'>ERROR: " . $e->getMessage() . "</span>\n\n";
    echo "Common issues:\n";
    echo "1. XAMPP/Apache not running\n";
    echo "2. MySQL not running\n";
    echo "3. Wrong database name\n";
    echo "4. Database 'lms_julom' doesn't exist\n";
}

echo "</pre>";
echo "<hr>";
echo "<p><a href='dashboard'>Go to Dashboard</a> | <a href='login'>Login</a></p>";
?>

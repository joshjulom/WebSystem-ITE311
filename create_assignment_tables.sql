-- Assignment Workflow System Database Tables
-- Run this SQL script to create the necessary tables for the assignment system

-- Create assignments table
CREATE TABLE IF NOT EXISTS `assignments` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create assignment_submissions table
CREATE TABLE IF NOT EXISTS `assignment_submissions` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create upload directories (Note: This must be done manually or via PHP)
-- Directory structure needed:
-- writable/uploads/assignments/ (for teacher assignment files)
-- writable/uploads/submissions/ (for student submission files)


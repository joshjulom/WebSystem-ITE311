-- Add type and enrollment_id columns to notifications table
-- Run this SQL script in your database (phpMyAdmin or MySQL console)

USE your_database_name;

-- Add type column (default: 'general')
ALTER TABLE `notifications` 
ADD COLUMN `type` VARCHAR(50) DEFAULT 'general' AFTER `message`;

-- Add enrollment_id column (nullable, for enrollment-related notifications)
ALTER TABLE `notifications` 
ADD COLUMN `enrollment_id` INT(11) UNSIGNED NULL AFTER `type`;

-- Add foreign key for enrollment_id (optional, if you want referential integrity)
-- Uncomment if you have an enrollments table and want FK constraint
-- ALTER TABLE `notifications` 
-- ADD CONSTRAINT `fk_notifications_enrollment` 
-- FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE;

-- Update existing enrollment-related notifications to have type 'enrollment'
UPDATE `notifications` 
SET `type` = 'enrollment' 
WHERE `message` LIKE '%has requested to enroll%' 
   OR `message` LIKE '%enrollment request%'
   OR `message` LIKE '%enrollment%approved%'
   OR `message` LIKE '%enrollment%declined%';


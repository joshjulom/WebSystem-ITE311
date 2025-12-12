-- Add status column to enrollments table for approval system
-- Run this SQL in your database

ALTER TABLE enrollments 
ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' AFTER enrollment_date;

-- Update existing enrollments to 'approved' status
UPDATE enrollments SET status = 'approved' WHERE status = 'pending';

-- Add index for better performance
ALTER TABLE enrollments ADD INDEX idx_status (status);
ALTER TABLE enrollments ADD INDEX idx_user_status (user_id, status);


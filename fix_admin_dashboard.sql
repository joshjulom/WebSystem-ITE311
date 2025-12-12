-- SQL to fix Admin Dashboard display issues
-- Run this in phpMyAdmin to ensure everything works

-- 1. Add status column to courses if it doesn't exist
ALTER TABLE courses 
ADD COLUMN IF NOT EXISTS status ENUM('Active', 'Inactive') DEFAULT 'Active';

-- 2. Set all existing courses to Active status
UPDATE courses 
SET status = 'Active' 
WHERE status IS NULL OR status = '';

-- 3. Add course_code if missing
ALTER TABLE courses 
ADD COLUMN IF NOT EXISTS course_code VARCHAR(20) NULL;

-- 4. Generate course codes for existing courses (if needed)
UPDATE courses 
SET course_code = CONCAT('CS', LPAD(id, 3, '0'))
WHERE course_code IS NULL OR course_code = '';

-- 5. Verify the data
SELECT 'Courses Check:' as info;
SELECT 
    id,
    course_code,
    title,
    status,
    instructor_id
FROM courses
LIMIT 10;

SELECT 'Course Count:' as info, COUNT(*) as total_courses FROM courses;
SELECT 'Active Courses:' as info, COUNT(*) as active_courses FROM courses WHERE status = 'Active';

-- 6. Check if you have any users
SELECT 'Users Check:' as info;
SELECT id, name, email, role, status FROM users LIMIT 10;

SELECT '✓ Admin Dashboard Fix Complete!' as status;


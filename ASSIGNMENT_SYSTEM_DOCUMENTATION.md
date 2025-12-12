# Assignment Workflow System Documentation

## Overview
This document describes the complete Assignment Workflow System implemented in the LMS. The system allows teachers to create assignments, students to submit them, and teachers to grade submissions with full feedback capabilities.

## Table of Contents
1. [Database Setup](#database-setup)
2. [System Architecture](#system-architecture)
3. [Teacher Workflow](#teacher-workflow)
4. [Student Workflow](#student-workflow)
5. [File Structure](#file-structure)
6. [Security Features](#security-features)
7. [Testing Guide](#testing-guide)

---

## Database Setup

### Step 1: Run the SQL Script
Execute the `create_assignment_tables.sql` file in your MySQL database:

```sql
-- From phpMyAdmin or MySQL console
source create_assignment_tables.sql;
```

Or run migrations using CodeIgniter:
```bash
php spark migrate
```

### Step 2: Create Upload Directories
Ensure these directories exist with write permissions:
```
writable/uploads/assignments/  (for teacher files)
writable/uploads/submissions/   (for student files)
```

### Database Tables Created

#### 1. `assignments` Table
- `id` - Primary key
- `course_id` - Foreign key to courses table
- `title` - Assignment title (required)
- `description` - Instructions/description (required)
- `due_date` - Optional deadline
- `file_attachment` - Optional file name
- `created_by` - Teacher user ID (foreign key)
- `created_at`, `updated_at` - Timestamps

#### 2. `assignment_submissions` Table
- `id` - Primary key
- `assignment_id` - Foreign key to assignments
- `student_id` - Foreign key to users table
- `file_path` - Student's uploaded file
- `submission_text` - Optional text/notes
- `submission_date` - When submitted
- `status` - 'Submitted' or 'Graded'
- `grade` - Numeric grade (0-100)
- `feedback` - Teacher's comments
- `graded_at` - When graded
- `graded_by` - Teacher who graded (foreign key)
- `created_at`, `updated_at` - Timestamps

---

## System Architecture

### Models

#### AssignmentModel (`app/Models/AssignmentModel.php`)
**Key Methods:**
- `getAssignmentsByCourse($courseId)` - Get all assignments for a course
- `getAssignmentsByTeacher($teacherId)` - Get assignments created by a teacher
- `getAssignmentsWithCourse($courseId)` - Get assignments with course details
- `getAssignmentDetails($assignmentId)` - Get full assignment info
- `canUserAccess($assignmentId, $userId, $role)` - Check access permissions
- `deleteAssignment($id)` - Delete assignment and its file

#### AssignmentSubmissionModel (`app/Models/AssignmentSubmissionModel.php`)
**Key Methods:**
- `getSubmission($assignmentId, $studentId)` - Get a specific submission
- `hasSubmitted($assignmentId, $studentId)` - Check if already submitted
- `getSubmissionsByAssignment($assignmentId)` - Get all submissions for an assignment
- `getAssignmentSubmissionStatus($assignmentId, $courseId)` - Get all enrolled students and their status
- `submitAssignment($data)` - Create new submission
- `gradeSubmission($submissionId, $grade, $feedback, $gradedBy)` - Grade a submission
- `getStudentSubmissions($studentId, $courseId)` - Get student's submissions for a course
- `deleteSubmission($id)` - Delete submission and its file

### Controller

#### Assignment Controller (`app/Controllers/Assignment.php`)

**Teacher Methods:**
- `teacherView($courseId)` - Display all assignments for a course
- `create($courseId)` - Show assignment creation form
- `store()` - Save new assignment (handles file upload)
- `viewSubmissions($assignmentId)` - View all student submissions
- `grade()` - Process grading form
- `delete($assignmentId)` - Delete an assignment

**Student Methods:**
- `studentView($courseId)` - Display assignments for enrolled course
- `submitForm($assignmentId)` - Show submission form
- `submit()` - Process student submission (handles file upload)

**Shared Methods:**
- `downloadAssignment($assignmentId)` - Download teacher's assignment file
- `downloadSubmission($submissionId)` - Download student's submission file

### Views

#### Teacher Views
1. **teacher_view.php** - List all assignments with actions
2. **create.php** - Form to create new assignment
3. **view_submissions.php** - View and grade student submissions

#### Student Views
1. **student_view.php** - List assignments with submission status and grades
2. **submit.php** - Form to submit assignment

---

## Teacher Workflow

### 1. Creating an Assignment

**Access:** Dashboard → Course → "Assignments" button

**Steps:**
1. Click "Assignments" on any course you teach
2. Click "Create New Assignment"
3. Fill in the form:
   - **Title** (required)
   - **Description/Instructions** (required)
   - **Due Date** (optional) - datetime-local picker
   - **Attachment** (optional) - PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP (max 10MB)
4. Click "Create Assignment"

**Result:**
- Assignment saved to database
- All enrolled students receive notification
- Redirected to assignment list

**URL:** `/assignment/create/{course_id}`

### 2. Viewing Submissions

**Access:** Dashboard → Course → Assignments → "View Submissions"

**What You See:**
- Complete list of all enrolled students
- Submission status for each student:
  - Not Submitted (gray badge)
  - Submitted (yellow badge)
  - Graded (green badge)
- Download link for submitted files
- Current grade (if graded)
- "Grade" or "Edit Grade" button

**URL:** `/assignment/view-submissions/{assignment_id}`

### 3. Grading Submissions

**Steps:**
1. Click "Grade" button next to any submission
2. Modal opens showing:
   - Student name
   - Submission text/notes
   - Grade input (0-100)
   - Feedback textarea
3. Enter grade and optional feedback
4. Click "Submit Grade"

**Result:**
- Grade and feedback saved
- Status changed to "Graded"
- Student receives notification
- Grade visible to student immediately

### 4. Managing Assignments

**Actions Available:**
- View all submissions
- Download assignment attachment
- Delete assignment (confirmation required)

---

## Student Workflow

### 1. Viewing Assignments

**Access:** Dashboard → Enrolled Course → "View Assignments"

**What You See:**
- Card layout for each assignment
- Assignment details:
  - Title and description
  - Due date (highlighted if past due)
  - Teacher's attachment (download link)
  - Teacher name
  - Submission status badge
  - Your submission details (if submitted)
  - Your grade and feedback (if graded)

**URL:** `/assignment/student-view/{course_id}`

### 2. Submitting an Assignment

**Steps:**
1. Click "Submit Assignment" on any unsubmitted assignment
2. Review assignment details on submission page
3. Upload file (optional but recommended)
   - Allowed: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP, JPG, PNG
   - Max size: 10MB
4. Add notes/comments (optional)
5. Click "Submit Assignment"
6. Confirm submission (cannot resubmit)

**Restrictions:**
- Cannot submit if already submitted
- Cannot submit after due date (unless teacher allows)
- Must have at least file OR text submission

**Result:**
- Submission saved with timestamp
- Teacher receives notification
- Status changes to "Submitted"
- Can view submission details but cannot edit

**URL:** `/assignment/submit-form/{assignment_id}`

### 3. Viewing Grades

**Access:** Same assignment list view

**What You See:**
- Status badge changes to "Graded" (green)
- Grade displayed: "X/100"
- Teacher feedback in alert box
- Can still download your submission file

---

## File Structure

```
app/
├── Controllers/
│   └── Assignment.php          (Main controller)
├── Models/
│   ├── AssignmentModel.php
│   └── AssignmentSubmissionModel.php
├── Views/
│   └── assignments/
│       ├── teacher_view.php    (Teacher assignment list)
│       ├── create.php          (Create assignment form)
│       ├── view_submissions.php (Grading interface)
│       ├── student_view.php    (Student assignment list)
│       └── submit.php          (Submission form)
├── Database/
│   └── Migrations/
│       ├── 2024-01-10-000001_CreateAssignmentsTable.php
│       └── 2024-01-10-000002_CreateAssignmentSubmissionsTable.php
└── Config/
    └── Routes.php              (Assignment routes)

writable/
└── uploads/
    ├── assignments/            (Teacher files)
    └── submissions/            (Student files)
```

---

## Security Features

### 1. Authentication & Authorization
- All routes check `isLoggedIn` session
- Role-based access control:
  - Teachers can only manage their own course assignments
  - Students can only view/submit for enrolled courses
  - Admin has full access

### 2. File Upload Validation
- File type whitelist (no executables)
- Maximum file size: 10MB
- Random file names prevent overwrites
- Files stored outside web root

### 3. SQL Injection Prevention
- All queries use CodeIgniter Query Builder
- Prepared statements with parameter binding
- Foreign key constraints enforced

### 4. XSS Protection
- All output uses `esc()` helper
- HTML special chars escaped
- User input sanitized

### 5. CSRF Protection
- CodeIgniter CSRF tokens on all forms
- POST requests validated

### 6. Access Control
- `canUserAccess()` method validates permissions
- Course enrollment verified for students
- Instructor ownership verified for teachers

---

## Testing Guide

### Phase 1: Database Setup
1. ✅ Run SQL script or migrations
2. ✅ Verify tables created with correct structure
3. ✅ Check foreign key relationships
4. ✅ Ensure upload directories exist with write permissions

### Phase 2: Teacher Functionality
1. **Create Assignment**
   - Log in as teacher
   - Navigate to a course you teach
   - Click "Assignments" button
   - Create assignment with all fields
   - Create assignment with minimal fields
   - Upload various file types
   - Verify notification sent to students

2. **View Assignments**
   - Verify assignment list displays correctly
   - Test download assignment file
   - Check created date displays
   - Test delete assignment

3. **View Submissions**
   - Verify all enrolled students appear
   - Check "Not Submitted" status for students who haven't submitted
   - Verify submitted files are downloadable
   - Check submission dates display correctly

4. **Grade Submissions**
   - Open grading modal
   - Enter grade (test edge cases: 0, 100, decimals)
   - Add feedback
   - Submit and verify status changes
   - Verify student receives notification
   - Test editing existing grade

### Phase 3: Student Functionality
1. **View Assignments**
   - Log in as student enrolled in course
   - Navigate to course assignments
   - Verify all assignments display
   - Check due dates highlighted correctly
   - Test past due date warning
   - Download teacher's attachment

2. **Submit Assignment**
   - Click "Submit Assignment"
   - Upload file
   - Add submission text
   - Confirm submission
   - Verify cannot resubmit
   - Check notification sent to teacher

3. **View Grades**
   - Wait for teacher to grade
   - Refresh assignment list
   - Verify grade displays correctly
   - Check feedback displays
   - Verify status badge changes

### Phase 4: Edge Cases
1. **File Upload**
   - Test max file size (10MB)
   - Try invalid file types
   - Test with no file (only text)
   - Test with special characters in filename

2. **Due Dates**
   - Create assignment with no due date
   - Create assignment with past due date
   - Try submitting after due date
   - Verify time remaining calculation

3. **Permissions**
   - Try accessing another teacher's assignments
   - Try accessing unenrolled course as student
   - Test admin access to all features

4. **Data Integrity**
   - Delete assignment with submissions
   - Delete course with assignments
   - Deactivate student with submissions
   - Test cascade deletes

### Phase 5: UI/UX
1. Verify responsive design on mobile
2. Check all buttons and links work
3. Test form validation messages
4. Verify success/error alerts display
5. Check loading states on AJAX requests

---

## API Endpoints Summary

### Teacher Endpoints
```
GET  /assignment/teacher-view/{course_id}
GET  /assignment/create/{course_id}
POST /assignment/store
GET  /assignment/view-submissions/{assignment_id}
POST /assignment/grade
POST /assignment/delete/{assignment_id}
```

### Student Endpoints
```
GET  /assignment/student-view/{course_id}
GET  /assignment/submit-form/{assignment_id}
POST /assignment/submit
```

### Shared Endpoints
```
GET /assignment/download-assignment/{assignment_id}
GET /assignment/download-submission/{submission_id}
```

---

## Troubleshooting

### Common Issues

**1. "File not found" error**
- Check upload directory exists: `writable/uploads/assignments/` and `writable/uploads/submissions/`
- Verify directory permissions (777 or 755 with correct owner)
- Check file was actually uploaded (check database `file_attachment` or `file_path` field)

**2. "Access denied" error**
- Verify user is logged in
- Check user role matches required role
- For teachers: verify they own the course
- For students: verify they're enrolled in the course

**3. "Validation failed" error**
- Check all required fields filled
- Verify file size under 10MB
- Ensure file type is in whitelist
- Check grade is between 0-100

**4. Assignment not showing for students**
- Verify student is enrolled in the course
- Check assignment course_id matches enrolled course
- Refresh page or clear cache

**5. Upload fails**
- Check PHP `upload_max_filesize` in php.ini (set to at least 10M)
- Check PHP `post_max_size` in php.ini (set to at least 12M)
- Verify directory write permissions
- Check available disk space

---

## Future Enhancements

Potential features for future development:
- Assignment categories/types
- Multiple file uploads per submission
- Peer review system
- Rubric-based grading
- Late submission penalties
- Assignment drafts
- Bulk grading
- Export grades to CSV
- Assignment analytics/statistics
- Email notifications
- Assignment templates
- Plagiarism checking integration
- Group assignments
- Assignment cloning

---

## Support

For issues or questions:
1. Check this documentation
2. Review error logs in `writable/logs/`
3. Verify database constraints and foreign keys
4. Check file permissions
5. Review CodeIgniter documentation

---

**System Version:** 1.0  
**Last Updated:** December 2024  
**Framework:** CodeIgniter 4  
**PHP Version Required:** 7.4+  
**MySQL Version Required:** 5.7+


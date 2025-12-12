# Assignment System - Quick Start Guide

## 🚀 Installation (5 minutes)

### Step 1: Create Database Tables
Run this SQL in your phpMyAdmin or MySQL console:

```bash
# Navigate to your project root
cd C:\xampp\htdocs\ITE311-JULOM

# Import the SQL file
mysql -u root -p your_database_name < create_assignment_tables.sql
```

OR use CodeIgniter migrations:
```bash
php spark migrate
```

### Step 2: Create Upload Directories
```bash
# Windows Command Prompt
mkdir writable\uploads\assignments
mkdir writable\uploads\submissions

# Give write permissions (Windows will handle this automatically in most cases)
```

### Step 3: Done! 🎉
The system is now ready to use. No additional configuration needed.

---

## 📝 How to Use (Teachers)

### Creating Your First Assignment

1. **Login** as a teacher account
2. Go to **Dashboard**
3. Find your course in "My Courses" section
4. Click **"Assignments"** button
5. Click **"Create New Assignment"**
6. Fill in the form:
   ```
   Title: Homework 1 - Introduction to PHP
   Description: Complete exercises 1-5 from chapter 1
   Due Date: 2024-12-20 11:59 PM (optional)
   Attachment: homework1.pdf (optional)
   ```
7. Click **"Create Assignment"**

✅ Done! All enrolled students will be notified.

### Grading Submissions

1. Go to your course assignments
2. Click **"View Submissions"** on any assignment
3. You'll see a table with ALL enrolled students:
   - Students who submitted (yellow badge)
   - Students who haven't submitted (gray badge)
   - Students whose work you've graded (green badge)
4. Click **"Grade"** next to any submitted work
5. Enter grade (0-100) and optional feedback
6. Click **"Submit Grade"**

✅ Student receives notification and can see their grade immediately!

---

## 📚 How to Use (Students)

### Viewing Your Assignments

1. **Login** as a student
2. Go to **Dashboard**
3. Find your enrolled courses
4. Click **"View Assignments"** on any course
5. You'll see all assignments with:
   - ⏰ Due dates
   - 📎 Teacher's attachments
   - ✅ Your submission status
   - ⭐ Your grade (if graded)

### Submitting an Assignment

1. Click **"Submit Assignment"** on any assignment
2. Upload your file (PDF, DOC, DOCX, etc.)
   - OR add text notes
   - OR both!
3. Review and confirm
4. Click **"Submit Assignment"**

⚠️ **Important:** You can only submit ONCE. Make sure your work is complete!

### Checking Your Grade

1. Go back to assignment list
2. Graded assignments show:
   - 🎯 Your grade out of 100
   - 💬 Teacher's feedback
   - ✅ Green "Graded" badge

---

## 🎯 Quick Access URLs

Once logged in, you can bookmark these:

### Teachers
```
View course assignments:
http://localhost/ITE311-JULOM/assignment/teacher-view/{course_id}

Create assignment:
http://localhost/ITE311-JULOM/assignment/create/{course_id}
```

### Students
```
View course assignments:
http://localhost/ITE311-JULOM/assignment/student-view/{course_id}
```

---

## ✅ Testing Checklist

### As Teacher
- [ ] Create assignment with file attachment
- [ ] Create assignment without file
- [ ] View submissions before anyone submits
- [ ] Grade a submission
- [ ] Download student's submission file
- [ ] Delete an assignment

### As Student
- [ ] View assignments for enrolled course
- [ ] Download teacher's assignment file
- [ ] Submit assignment with file
- [ ] Try to resubmit (should be prevented)
- [ ] View your grade after teacher grades it
- [ ] Check feedback from teacher

---

## 🔧 Troubleshooting

### "File not found" error
```bash
# Make sure these directories exist:
C:\xampp\htdocs\ITE311-JULOM\writable\uploads\assignments\
C:\xampp\htdocs\ITE311-JULOM\writable\uploads\submissions\
```

### "Upload failed" error
Check `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 12M
```
Restart Apache after changing.

### "Access denied" error
- Teachers: Make sure you own the course (instructor_id matches your user_id)
- Students: Make sure you're enrolled in the course

### Can't see assignments
- Students must be enrolled in the course first
- Check the enrollments table in database

---

## 📊 Database Quick Reference

### Check assignments
```sql
SELECT * FROM assignments WHERE course_id = 1;
```

### Check submissions
```sql
SELECT * FROM assignment_submissions WHERE assignment_id = 1;
```

### Check who's enrolled
```sql
SELECT * FROM enrollments WHERE course_id = 1;
```

---

## 🎨 Features at a Glance

### For Teachers
✅ Create unlimited assignments per course  
✅ Attach files (PDF, DOC, PPT, etc.)  
✅ Set due dates (optional)  
✅ See all students (submitted or not)  
✅ Grade with feedback  
✅ Download student submissions  
✅ Delete assignments  
✅ Automatic notifications  

### For Students
✅ View all course assignments  
✅ Download teacher's files  
✅ Submit files and/or text  
✅ See due dates with countdown  
✅ View grades immediately  
✅ Read teacher feedback  
✅ Download own submissions  
✅ Past-due prevention  

---

## 📱 Dashboard Integration

The assignment system is fully integrated into your existing dashboard:

**Teacher Dashboard:**
- Each course row now has an "Assignments" button
- Clicking it takes you to assignment management

**Student Dashboard:**
- Each enrolled course card has "View Assignments" button
- Shows assignments, materials, and enrollment info together

---

## 🔐 Security Features

✅ Role-based access control  
✅ File type validation  
✅ File size limits (10MB)  
✅ SQL injection prevention  
✅ XSS protection  
✅ CSRF tokens  
✅ Enrollment verification  

---

## 📞 Need More Help?

See the full documentation:
- `ASSIGNMENT_SYSTEM_DOCUMENTATION.md` - Complete technical reference
- `create_assignment_tables.sql` - Database structure

---

**Ready to go!** Just run the SQL script and start using the system. 🚀

Questions? Check the full documentation or review the code comments.


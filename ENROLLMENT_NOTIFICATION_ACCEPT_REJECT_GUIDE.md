# Enrollment Notification Accept/Reject Feature Guide

## Overview
This guide explains the new enrollment notification system that allows teachers to accept or reject student enrollment requests directly from the notification dropdown in the navigation bar.

## Features Implemented

### 1. **Enhanced Notification System**
   - Notifications now support different types (general, enrollment, etc.)
   - Enrollment notifications include enrollment_id for tracking
   - Real-time accept/reject buttons for pending enrollment requests

### 2. **Teacher Notifications**
   - When a student enrolls in a course, the teacher receives a notification
   - The notification displays **Accept** and **Reject** buttons
   - Actions can be performed directly from the notification dropdown
   - No need to navigate to the dashboard

### 3. **Student Notifications**
   - Students receive notifications when their enrollment is approved or rejected
   - Clear status updates in real-time

## Database Changes Required

### Run the SQL Update Script

Execute the `update_notifications_table.sql` file in your database:

```sql
-- Add type column (default: 'general')
ALTER TABLE `notifications` 
ADD COLUMN `type` VARCHAR(50) DEFAULT 'general' AFTER `message`;

-- Add enrollment_id column (nullable, for enrollment-related notifications)
ALTER TABLE `notifications` 
ADD COLUMN `enrollment_id` INT(11) UNSIGNED NULL AFTER `type`;

-- Update existing enrollment-related notifications to have type 'enrollment'
UPDATE `notifications` 
SET `type` = 'enrollment' 
WHERE `message` LIKE '%has requested to enroll%' 
   OR `message` LIKE '%enrollment request%'
   OR `message` LIKE '%enrollment%approved%'
   OR `message` LIKE '%enrollment%declined%';
```

**Steps:**
1. Open phpMyAdmin or your MySQL client
2. Select your database
3. Run the SQL commands from `update_notifications_table.sql`
4. Verify the new columns exist in the `notifications` table

## How It Works

### For Teachers:

1. **Receiving Enrollment Requests:**
   - When a student requests to enroll, you'll see a notification badge in the navbar
   - Click on the "Notifications" dropdown

2. **Viewing the Request:**
   - The notification will show: "Student Name has requested to enroll in 'Course Title'"
   - Two buttons will appear: **Accept** (green) and **Reject** (red)

3. **Accepting an Enrollment:**
   - Click the **Accept** button
   - The system will:
     - Approve the enrollment in the database
     - Send a notification to the student
     - Mark the teacher's notification as read
     - Remove it from the dropdown
     - Show a success message

4. **Rejecting an Enrollment:**
   - Click the **Reject** button
   - Confirm the action
   - The system will:
     - Mark the enrollment as rejected
     - Send a notification to the student
     - Mark the teacher's notification as read
     - Remove it from the dropdown

### For Students:

1. **After Requesting Enrollment:**
   - You'll receive a notification: "Your enrollment request for 'Course Title' is pending approval"

2. **After Teacher's Decision:**
   - **If Approved:** "Your enrollment in 'Course Title' has been approved!"
   - **If Rejected:** "Your enrollment request for 'Course Title' was declined"

## Files Modified

### 1. Database Migration
   - `update_notifications_table.sql` - Adds new columns to notifications table

### 2. Models
   - `app/Models/NotificationModel.php` - Added support for `type` and `enrollment_id` fields
   - `app/Models/EnrollmentModel.php` - Already had necessary methods

### 3. Controllers
   - `app/Controllers/Course.php` - Updated enrollment notification creation to include type and enrollment_id
   - `app/Controllers/Notifications.php` - Enhanced to return enrollment status with notifications

### 4. Views
   - `app/Views/templates/header.php` - Updated notification dropdown to show accept/reject buttons for enrollment requests

### 5. Routes
   - Already configured in `app/Config/Routes.php`

## Usage Examples

### Example 1: Student Enrolls in a Course
1. Student clicks "Enroll" on a course
2. System creates enrollment with status "pending"
3. System sends notification to teacher with type "enrollment" and enrollment_id
4. Teacher sees notification with Accept/Reject buttons
5. Teacher clicks Accept
6. Student's enrollment status changes to "approved"
7. Student receives success notification

### Example 2: Teacher Rejects Enrollment
1. Teacher receives enrollment request notification
2. Teacher clicks Reject button
3. Confirms rejection in popup
4. Student's enrollment status changes to "rejected"
5. Student receives rejection notification
6. Student can request to enroll again later

## Testing the Feature

### Test Case 1: Accept Enrollment
1. Log in as a student
2. Go to Dashboard
3. Find an available course
4. Click "Enroll"
5. Log out and log in as the teacher for that course
6. Click on Notifications dropdown
7. You should see the enrollment request with Accept/Reject buttons
8. Click **Accept**
9. Verify the notification disappears
10. Log back in as student and verify enrollment is approved

### Test Case 2: Reject Enrollment
1. Follow steps 1-7 from Test Case 1
2. Click **Reject** and confirm
3. Verify the notification disappears
4. Log back in as student
5. Verify you see a rejection notification
6. Verify the course appears in "Available Courses" again

## Troubleshooting

### Issue: Buttons Not Showing
**Solution:** 
- Make sure you ran the SQL update script
- Clear browser cache
- Check browser console for JavaScript errors

### Issue: Accept/Reject Not Working
**Solution:**
- Verify routes are configured correctly in `app/Config/Routes.php`
- Check that the Course controller methods exist
- Look at browser console for AJAX errors

### Issue: Notifications Not Updating
**Solution:**
- Check that the notification dropdown refreshes every 5 seconds
- Verify jQuery is loaded properly
- Check browser console for errors

## Additional Notes

- The notification system auto-refreshes every 5 seconds
- Toast notifications appear when actions are completed
- Only teachers can see accept/reject buttons on enrollment notifications
- Students see regular "Mark as Read" buttons for their notifications
- The dashboard still shows the pending enrollment requests table for reference

## Benefits

1. **Faster Workflow:** Teachers can approve/reject without navigating away
2. **Real-time Updates:** Students get immediate feedback
3. **Better UX:** Visual feedback with toast notifications
4. **Reduced Clicks:** Actions in one place (notification dropdown)
5. **Clear Status:** Color-coded buttons and messages

## Future Enhancements

Possible improvements:
- Bulk accept/reject multiple enrollments
- Add reason for rejection (text field)
- Email notifications in addition to in-app notifications
- Notification sound/desktop notifications
- Filter notifications by type


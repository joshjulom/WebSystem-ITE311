# Enrollment Notification System - User Guide

## Overview
The enrollment notification system provides real-time updates to students when teachers approve or reject their course enrollment requests.

## Features

### For Students

#### 1. Enrollment Status Notifications
- **Location**: Top of dashboard (gradient purple card)
- **Shows**: Approved/declined enrollment messages with timestamps
- **Actions**: Click "Dismiss" to mark as read

#### 2. Rejected Enrollments Card
- **Location**: Below status notifications
- **Shows**: All courses where enrollment was declined
- **Actions**: Click "Remove" to clear from list
- **Note**: You can request enrollment again from Available Courses

#### 3. Pending Requests Alert
- **Location**: Below rejected enrollments
- **Shows**: Courses waiting for teacher approval
- **Color**: Yellow warning box

#### 4. Navbar Notifications
- **Location**: Bell icon in top navigation
- **Shows**: Red badge with unread count
- **Updates**: Automatically every 5 seconds
- **Dropdown**: Click to see recent notifications

### For Teachers

#### 1. Pending Enrollment Requests
- **Location**: Top of dashboard
- **Shows**: Student name, email, course, and request date
- **Actions**: 
  - ✅ **Approve** button - Grants student access
  - ❌ **Reject** button - Declines the request

#### 2. Automatic Notifications
- When student requests enrollment → Teacher receives notification
- When teacher approves/rejects → Student receives notification

## Notification Types

### Student Receives:
1. **Pending Approval** 📋
   - Message: "Your enrollment request for [Course] is pending approval"
   - Color: Blue (info)

2. **Approved** ✅
   - Message: "Your enrollment in [Course] has been approved!"
   - Color: Green (success)

3. **Declined** ❌
   - Message: "Your enrollment request for [Course] was declined"
   - Color: Red (danger)

### Teacher Receives:
1. **New Request** 📥
   - Message: "[Student Name] has requested to enroll in [Course]"
   - Color: Blue (info)

## Student Dashboard Sections

### 1. Enrollment Status Updates (Top Card)
```
┌─────────────────────────────────────────────────┐
│ 🔔 Enrollment Status Updates                   │
├─────────────────────────────────────────────────┤
│ ✅ Your enrollment in "Web Development" has     │
│    been approved!                               │
│    Dec 12, 2025 3:45 PM              [Dismiss]  │
├─────────────────────────────────────────────────┤
│ ❌ Your enrollment request for "Data Science"   │
│    was declined                                 │
│    Dec 12, 2025 2:30 PM              [Dismiss]  │
└─────────────────────────────────────────────────┘
```

### 2. Rejected Enrollments Card
```
┌─────────────────────────────────────────────────┐
│ ❌ Enrollment Requests Declined                 │
├─────────────────────────────────────────────────┤
│ Data Science                                    │
│ Requested on Dec 10, 2025            [Remove]   │
├─────────────────────────────────────────────────┤
│ ℹ️ You can request to enroll again from the    │
│   Available Courses section below.             │
└─────────────────────────────────────────────────┘
```

### 3. Pending Enrollment Requests
```
┌─────────────────────────────────────────────────┐
│ ⏰ Pending Enrollment Requests                  │
├─────────────────────────────────────────────────┤
│ You have 2 enrollment request(s) waiting for    │
│ teacher approval:                               │
│ • Mobile App Development - Requested Dec 12     │
│ • Cloud Computing - Requested Dec 11            │
└─────────────────────────────────────────────────┘
```

### 4. Student Overview Stats
```
┌─────────────────┬─────────────────┬─────────────────┐
│ Total Enrolled  │ Pending Approval│ Available       │
│       3         │        2        │       15        │
└─────────────────┴─────────────────┴─────────────────┘
```

## How to Use

### As a Student:

1. **Browse Available Courses**
   - Scroll to "Available Courses" section
   - Click "Enroll" button on desired course

2. **Wait for Approval**
   - Check "Pending Enrollment Requests" section
   - Watch for bell icon notification

3. **Check Notifications**
   - Click bell icon in navbar
   - Or check dashboard top section

4. **Handle Approved Enrollment**
   - Course appears in "My Enrolled Courses"
   - Access course materials and assignments
   - Dismiss notification

5. **Handle Rejected Enrollment**
   - Review in "Rejected Enrollments" card
   - Remove from list when ready
   - Can request enrollment again if desired

### As a Teacher:

1. **Check Pending Requests**
   - Login to dashboard
   - See "Pending Enrollment Requests" card at top

2. **Review Student Information**
   - Student name and email
   - Course requested
   - Request date

3. **Make Decision**
   - Click **Approve** to grant access
   - Click **Reject** to decline request
   - Confirm your choice

4. **Automatic Notification**
   - Student receives instant notification
   - Request removed from your pending list

## Technical Details

### Database Status Values
- `pending` - Initial status when student requests enrollment
- `approved` - Teacher has approved the request
- `rejected` - Teacher has declined the request

### Notification Storage
- Stored in `notifications` table
- `is_read` field tracks read status
- Auto-loads every 5 seconds in navbar
- Maximum 5 recent notifications shown in dropdown

### Enrollment Table Schema
```sql
enrollments:
- id (primary key)
- user_id (foreign key → users)
- course_id (foreign key → courses)
- enrollment_date (datetime)
- status (enum: pending, approved, rejected)
```

## Troubleshooting

### Student Not Seeing Notifications?
1. Check bell icon - badge should show count
2. Refresh the page
3. Check if notifications are marked as read

### Teacher Not Seeing Pending Requests?
1. Ensure you're the course instructor
2. Check if enrollment status is 'pending'
3. Verify student submitted the request

### "Already Enrolled" Error?
- You may have a pending or approved enrollment already
- Check your enrolled courses or pending requests
- Contact teacher if you believe it's an error

## Benefits

✅ **Real-time Updates** - Instant notifications when status changes
✅ **Clear Communication** - Students know exactly what happened
✅ **Better UX** - Visual indicators and organized dashboard
✅ **Transparency** - Track all enrollment stages
✅ **Clean Interface** - Easy to dismiss and manage notifications

## Support

If you encounter any issues:
1. Try refreshing the page
2. Check your internet connection
3. Clear browser cache if notifications don't load
4. Contact administrator if problem persists

---

**Last Updated**: December 12, 2025
**Version**: 1.0


# IMPORTANT: Run This SQL First!

## Quick Fix for "Loading..." Issue

The notifications should now load correctly, but to enable the **Accept/Reject** buttons for enrollment notifications, you need to update your database.

## Step-by-Step Instructions

### Option 1: Using phpMyAdmin (Easiest)

1. Open your browser and go to: `http://localhost/phpmyadmin`

2. Click on your database name in the left sidebar (probably `ite311_db` or similar)

3. Click on the **SQL** tab at the top

4. Copy and paste this SQL code:

```sql
-- Add type column (default: 'general')
ALTER TABLE `notifications` 
ADD COLUMN `type` VARCHAR(50) DEFAULT 'general' AFTER `message`;

-- Add enrollment_id column (nullable, for enrollment-related notifications)
ALTER TABLE `notifications` 
ADD COLUMN `enrollment_id` INT(11) UNSIGNED NULL AFTER `type`;

-- Update existing enrollment-related notifications
UPDATE `notifications` 
SET `type` = 'enrollment' 
WHERE `message` LIKE '%has requested to enroll%' 
   OR `message` LIKE '%enrollment request%'
   OR `message` LIKE '%enrollment%approved%'
   OR `message` LIKE '%enrollment%declined%';
```

5. Click the **Go** button at the bottom right

6. You should see a success message: "Your SQL query has been executed successfully"

7. **Refresh your dashboard page** (press F5)

### Option 2: Using MySQL Command Line

1. Open Command Prompt (Windows) or Terminal (Mac/Linux)

2. Navigate to MySQL:
   ```
   cd C:\xampp\mysql\bin
   ```

3. Login to MySQL:
   ```
   mysql -u root -p
   ```

4. Enter your password (usually empty for XAMPP, just press Enter)

5. Select your database:
   ```
   USE your_database_name;
   ```

6. Run the SQL commands from Option 1 above

### What This Does:

- **`type` column**: Identifies notification types (general, enrollment, etc.)
- **`enrollment_id` column**: Links enrollment notifications to specific enrollment requests
- This enables the Accept/Reject buttons in the notification dropdown

## After Running the SQL:

1. **Refresh your browser** (F5 or Ctrl+R)
2. The notifications should now load properly
3. Teachers will see **Accept** and **Reject** buttons for enrollment requests
4. Students will see regular notifications with "Mark as Read" buttons

## Testing:

1. Log in as a **student**
2. Go to Dashboard
3. Click **Enroll** on a course
4. Log out and log in as the **teacher** for that course
5. Click on **Notifications** (with the badge)
6. You should now see **Accept** and **Reject** buttons!

## Troubleshooting:

### Still seeing "Loading..."?
- Clear your browser cache (Ctrl+Shift+Delete)
- Make sure XAMPP MySQL is running
- Check the browser console (F12) for errors

### "Column already exists" error?
- The columns are already added, you're good!
- Just refresh your page

### Buttons not showing?
- Make sure you're logged in as a teacher
- Make sure there are pending enrollment requests
- The database update needs to be completed

## Need Help?

Check the file `ENROLLMENT_NOTIFICATION_ACCEPT_REJECT_GUIDE.md` for complete documentation.


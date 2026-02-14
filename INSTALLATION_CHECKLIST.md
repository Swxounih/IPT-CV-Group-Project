# 📋 INSTALLATION CHECKLIST

## ✅ Pre-Installation
- [ ] Backup your current database
- [ ] Backup your current PHP files
- [ ] Make sure you have database access (phpMyAdmin or command line)

## ✅ Step 1: Database Migration (5 minutes)
- [ ] Open phpMyAdmin
- [ ] Select database: `ipt_group_project`
- [ ] Click "SQL" tab
- [ ] Open `database_migration_updated.sql` file
- [ ] Copy entire contents
- [ ] Paste into SQL box
- [ ] Click "Go" button
- [ ] Verify: Should see success message
- [ ] Run test query: `SELECT user_id, cv_title FROM personal_information LIMIT 5;`
- [ ] Confirm: Should see user_id and cv_title columns populated

## ✅ Step 2: Create Photo Directory (1 minute)
```bash
mkdir -p uploads/photos
chmod 755 uploads/photos
```

Or via FTP/cPanel:
- [ ] Create folder: `uploads/photos/`
- [ ] Set permissions: 755

## ✅ Step 3: Replace PHP Files (10 minutes)

**Core files to replace (9 files):**
- [ ] personal-information.php
- [ ] save_resume.php  
- [ ] preview.php
- [ ] edit-cv-inline.php
- [ ] delete-cv.php
- [ ] delete-account.php
- [ ] dashboard.php
- [ ] create-additional-cv.php
- [ ] logout.php (NEW - add this file)

**Keep these files unchanged:**
- ✅ config.php (already correct)
- ✅ search-create.php (already correct)
- ✅ verify-birthdate.php (already correct)
- ✅ All other step files (career-objectives, education, etc.)

## ✅ Step 4: Test Installation (15 minutes)

### Test 1: Search & Login
- [ ] Go to search-create.php
- [ ] Search for existing user (email: armelcruz831@gmail.com)
- [ ] Click result
- [ ] Enter birthdate: 2005-12-07
- [ ] Should see dashboard

### Test 2: Dashboard
- [ ] Dashboard loads correctly
- [ ] Profile section shows user info
- [ ] My Resumes section shows existing CV(s)
- [ ] Each CV has Edit/View/Delete buttons

### Test 3: Create New CV
- [ ] Click "Create New Resume" button
- [ ] Fill out all steps (personal info → references)
- [ ] Save and preview
- [ ] Click "Finish & Go to Dashboard"
- [ ] Should see 2 CVs in dashboard now

### Test 4: Edit CV
- [ ] Click "Edit" on any CV
- [ ] Should see single-page editor (edit-cv-inline.php)
- [ ] Change something (e.g., objective)
- [ ] Click "Save Changes"
- [ ] Should see success message

### Test 5: View CV
- [ ] Click "View" on any CV
- [ ] Should see formatted resume
- [ ] Print/PDF button should work

### Test 6: Delete CV
- [ ] Click "Delete" on a CV
- [ ] Confirm deletion
- [ ] CV should be removed from list
- [ ] Photo file should be deleted from server

### Test 7: Logout
- [ ] Click "Logout" in sidebar
- [ ] Should redirect to search-create.php
- [ ] Session should be destroyed

### Test 8: Account Deletion (Optional - careful!)
- [ ] Go to Account Settings
- [ ] Click "Delete Account"
- [ ] Confirm (warning about deleting all CVs)
- [ ] All CVs and photos should be deleted
- [ ] Should redirect to search-create.php

## ✅ Step 5: Verify File Cleanup
- [ ] Check uploads/photos/ folder
- [ ] Deleted CVs should have photos removed
- [ ] No orphaned photo files

## 🎉 Installation Complete!

If all tests pass, your CV Builder is now:
- ✅ Supporting multiple CVs per user
- ✅ Using single inline editing method
- ✅ Deleting files properly
- ✅ More secure with better validation
- ✅ Better user experience

## 📊 Quick Stats Check

After installation, you should have:
- **Users in database:** Check `SELECT DISTINCT user_id FROM personal_information;`
- **Total CVs:** Check `SELECT COUNT(*) FROM personal_information;`
- **CVs per user:** Check `SELECT user_id, COUNT(*) as cv_count FROM personal_information GROUP BY user_id;`

## 🆘 If Something Goes Wrong

### Database migration failed
1. Restore database backup
2. Check error message
3. Verify column names don't already exist
4. Try running migration line by line

### PHP files not working
1. Check file permissions (644 for PHP files)
2. Check error logs: /var/log/apache2/error.log
3. Verify all files were uploaded correctly
4. Make sure config.php has correct database credentials

### Photos not uploading
1. Check uploads/photos/ exists
2. Check permissions: `chmod 755 uploads/photos`
3. Check PHP upload settings in php.ini
4. Verify upload_max_filesize and post_max_size

### Can't see multiple CVs
1. Verify user_id column exists
2. Check if user_id is populated (should be email)
3. Run: `UPDATE personal_information SET user_id = email WHERE user_id IS NULL;`

## 📞 Need Help?

Check the full README.md for detailed troubleshooting and documentation.

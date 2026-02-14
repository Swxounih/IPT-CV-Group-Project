-- =====================================================
-- CV BUILDER DATABASE MIGRATION SCRIPT (Updated)
-- This script adds multiple CV support to your existing database
-- Your column names are already correct!
-- =====================================================

-- Step 1: Add user_id and cv_title to personal_information table
-- This enables multiple CVs per user
ALTER TABLE `personal_information` 
  ADD COLUMN `user_id` VARCHAR(255) DEFAULT NULL AFTER `id`,
  ADD COLUMN `cv_title` VARCHAR(255) DEFAULT 'My Resume' AFTER `user_id`;

-- Step 2: Create index on user_id for faster queries
CREATE INDEX `idx_user_id` ON `personal_information`(`user_id`);

-- Step 3: Populate user_id for existing records (using email as user_id)
UPDATE `personal_information` 
SET `user_id` = `email` 
WHERE `user_id` IS NULL;

-- =====================================================
-- VERIFICATION: Run these queries to check the changes
-- =====================================================

-- Check if columns were added successfully
-- SELECT * FROM personal_information LIMIT 5;

-- Check if user_id was populated
-- SELECT id, user_id, cv_title, given_name, surname, email FROM personal_information;

-- =====================================================
-- COMPLETE! Your database now supports multiple CVs per user
-- =====================================================

-- NOTES:
-- 1. Your existing column names are already correct (job_title, employer, level, etc.)
-- 2. Foreign key constraints are already set up with CASCADE delete
-- 3. Indexes are already in place
-- 4. We only added user_id and cv_title columns for multiple CV support

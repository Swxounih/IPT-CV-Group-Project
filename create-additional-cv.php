<?php
session_start();
require_once 'config.php';

// Check if user is verified
if (!isset($_SESSION['verified_user_id'])) {
    header('Location: search-create.php');
    exit();
}

// Clear any existing resume_data session to start fresh
unset($_SESSION['resume_data']);

// Initialize new CV session data
$_SESSION['resume_data'] = array(
    'personal_info_id' => null,
    'is_additional_cv' => true,
    'personal_info' => array(),
    'objective' => '',
    'education' => array(),
    'work_experience' => array(),
    'skills' => array(),
    'interests' => '',
    'references' => array()
);

// Redirect to personal information to start creating new CV
header('Location: personal-information.php');
exit();
?>
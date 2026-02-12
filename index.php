<?php
session_start();
require_once 'config.php';

// Initialize session data if not exists
if (!isset($_SESSION['resume_data'])) {
    $_SESSION['resume_data'] = array(
        'personal_info_id' => null,
        'personal_info' => array(),
        'objective' => '',
        'education' => array(),
        'work_experience' => array(),
        'skills' => array(),
        'interests' => '',
        'references' => array()
    );
}

// Test database connection
$conn = getDBConnection();
if ($conn) {
    $_SESSION['db_connected'] = true;
    closeDBConnection($conn);
} else {
    $_SESSION['db_connected'] = false;
}

// Redirect to first step
header('Location: search-create.php');
exit();
?>
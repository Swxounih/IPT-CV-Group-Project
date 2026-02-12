<?php
session_start();

// Initialize session data if not exists
if (!isset($_SESSION['resume_data'])) {
    $_SESSION['resume_data'] = array(
        'personal_info' => array(),
        'objective' => '',
        'education' => array(),
        'work_experience' => array(),
        'skills' => array(),
        'interests' => '',
        'references' => array()
    );
}

// Redirect to first step
header('Location: personal-information.php');
exit();
?>


<?php
session_start();
require_once 'config.php';

// Check if user is verified
if (!isset($_SESSION['verified_user_id'])) {
    header('Location: search-create.php');
    exit();
}

$user_id = $_SESSION['verified_user_id'];

$conn = getDBConnection();
$conn->begin_transaction();

try {
    // Get all photos for this user's CVs before deleting
    $sql = "SELECT id, photo FROM personal_information WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cv_ids = [];
    $photos = [];
    
    while ($row = $result->fetch_assoc()) {
        $cv_ids[] = $row['id'];
        if (!empty($row['photo'])) {
            $photos[] = $row['photo'];
        }
    }
    $stmt->close();
    
    // Delete all photo files
    foreach ($photos as $photo) {
        if (file_exists($photo)) {
            unlink($photo);
        }
    }
    
    // Delete all CVs and related data for this user
    // The foreign key constraints will cascade, but we'll be explicit
    $tables = ['career_objectives', 'education', 'work_experience', 'skills', 'interests', 'reference'];
    
    foreach ($cv_ids as $cv_id) {
        foreach ($tables as $table) {
            $sql = "DELETE FROM $table WHERE personal_info_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $cv_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Delete all personal information records (all CVs for this user)
    $sql = "DELETE FROM personal_information WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->close();
    
    $conn->commit();
    
    // Destroy session and logout
    session_destroy();
    
    echo "<script>
        alert('Account deleted successfully. All your resumes and data have been removed. We\\'re sorry to see you go.');
        window.location.href='search-create.php';
    </script>";
    
} catch (Exception $e) {
    $conn->rollback();
    echo "<script>
        alert('Error deleting account: " . addslashes($e->getMessage()) . "');
        window.location.href='dashboard.php';
    </script>";
}

closeDBConnection($conn);
?>
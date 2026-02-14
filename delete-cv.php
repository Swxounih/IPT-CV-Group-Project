<?php
session_start();
require_once 'config.php';

// Check if user is verified
if (!isset($_SESSION['verified_user_id'])) {
    header('Location: search-create.php');
    exit();
}

$cv_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['verified_user_id'];

if ($cv_id <= 0) {
    echo "<script>alert('Invalid CV ID'); window.location.href='dashboard.php';</script>";
    exit();
}

$conn = getDBConnection();

// Verify this CV belongs to the user
$sql = "SELECT photo, user_id FROM personal_information WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    closeDBConnection($conn);
    echo "<script>alert('CV not found'); window.location.href='dashboard.php';</script>";
    exit();
}

$cv_data = $result->fetch_assoc();
$stmt->close();

// Verify ownership
if ($cv_data['user_id'] !== $user_id) {
    closeDBConnection($conn);
    echo "<script>alert('Unauthorized access'); window.location.href='dashboard.php';</script>";
    exit();
}

// Begin transaction
$conn->begin_transaction();

try {
    // Delete uploaded photo file if exists
    if (!empty($cv_data['photo']) && file_exists($cv_data['photo'])) {
        unlink($cv_data['photo']);
    }
    
    // The foreign key constraints with CASCADE will automatically delete related records
    // But we'll do it explicitly for clarity
    $tables = ['career_objectives', 'education', 'work_experience', 'skills', 'interests', 'reference'];
    
    foreach ($tables as $table) {
        $sql = "DELETE FROM $table WHERE personal_info_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cv_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Delete personal information (main CV record)
    $sql = "DELETE FROM personal_information WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cv_id);
    $stmt->execute();
    $stmt->close();
    
    $conn->commit();
    
    // Check if user still has other CVs
    $sql = "SELECT COUNT(*) as cv_count FROM personal_information WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count_data = $result->fetch_assoc();
    $stmt->close();
    
    closeDBConnection($conn);
    
    // If no more CVs, redirect to search page, else go to dashboard
    if ($count_data['cv_count'] == 0) {
        session_destroy();
        echo "<script>alert('CV deleted successfully. You have no more resumes.'); window.location.href='search-create.php';</script>";
    } else {
        echo "<script>alert('CV deleted successfully.'); window.location.href='dashboard.php';</script>";
    }
    
} catch (Exception $e) {
    $conn->rollback();
    closeDBConnection($conn);
    echo "<script>alert('Error deleting CV: " . addslashes($e->getMessage()) . "'); window.location.href='dashboard.php';</script>";
}
?>
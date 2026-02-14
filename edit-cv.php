<?php
session_start();
require_once 'config.php';

// Check if user is verified
if (!isset($_SESSION['verified_user_id'])) {
    header('Location: search-create.php');
    exit();
}

// Get CV ID from URL
$cv_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['verified_user_id'];

// Verify this CV belongs to the user
if ($cv_id !== $user_id) {
    echo "<script>alert('Unauthorized access'); window.location.href='dashboard.php';</script>";
    exit();
}

$conn = getDBConnection();

// Load existing CV data into session for editing
$sql = "SELECT * FROM personal_information WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$personal_info = $result->fetch_assoc();
$stmt->close();

if (!$personal_info) {
    echo "<script>alert('CV not found'); window.location.href='dashboard.php';</script>";
    exit();
}

// Load all CV sections
// Get objective
$sql = "SELECT objective FROM career_objectives WHERE personal_info_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$objective = '';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $objective = $row['objective'];
}
$stmt->close();

// Get education
$sql = "SELECT * FROM education WHERE personal_info_id = ? ORDER BY start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$education = [];
while ($row = $result->fetch_assoc()) {
    $education[] = $row;
}
$stmt->close();

// Get work experience
$sql = "SELECT * FROM work_experience WHERE personal_info_id = ? ORDER BY start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$work_experience = [];
while ($row = $result->fetch_assoc()) {
    $work_experience[] = $row;
}
$stmt->close();

// Get skills
$sql = "SELECT * FROM skills WHERE personal_info_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$skills = [];
while ($row = $result->fetch_assoc()) {
    $skills[] = $row;
}
$stmt->close();

// Get interests
$sql = "SELECT interests FROM interests WHERE personal_info_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$interests = '';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $interests = $row['interests'];
}
$stmt->close();

// Get references
$sql = "SELECT * FROM reference WHERE personal_info_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$references = [];
while ($row = $result->fetch_assoc()) {
    $references[] = $row;
}
$stmt->close();

closeDBConnection($conn);

// Load data into session for editing workflow
$_SESSION['resume_data'] = array(
    'personal_info_id' => $cv_id,
    'is_editing' => true,
    'personal_info' => array(
        'photo' => $personal_info['photo'] ?? '',
        'given_name' => $personal_info['given_name'] ?? '',
        'middle_name' => $personal_info['middle_name'] ?? '',
        'surname' => $personal_info['surname'] ?? '',
        'extension' => $personal_info['extension'] ?? '',
        'gender' => $personal_info['gender'] ?? '',
        'birthdate' => $personal_info['birthdate'] ?? '',
        'birthplace' => $personal_info['birthplace'] ?? '',
        'civil_status' => $personal_info['civil_status'] ?? '',
        'email' => $personal_info['email'] ?? '',
        'phone' => $personal_info['phone'] ?? '',
        'address' => $personal_info['address'] ?? '',
        'website' => $personal_info['website'] ?? ''
    ),
    'objective' => $objective,
    'education' => $education,
    'work_experience' => $work_experience,
    'skills' => $skills,
    'interests' => $interests,
    'references' => $references
);

// Redirect to personal information page to start editing
header('Location: personal-information.php');
exit();
?>
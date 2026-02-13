<?php
session_start();
require_once 'config.php';

// Check if we have data to save
if (!isset($_SESSION['resume_data'])) {
    header('Location: personal-information.php');
    exit();
}

// Check if already saved (has personal_info_id)
if (isset($_SESSION['resume_data']['personal_info_id'])) {
    // Already saved, just show preview
    header('Location: preview.php');
    exit();
}

$conn = getDBConnection();
$conn->begin_transaction();

try {
    // 1. Insert Personal Information
    $personal = $_SESSION['resume_data']['personal_info'];
    $sql = "INSERT INTO personal_information (photo, given_name, middle_name, surname, extension, gender, birthdate, birthplace, civil_status, email, phone, address, website) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssss", 
        $personal['photo'], 
        $personal['given_name'], 
        $personal['middle_name'], 
        $personal['surname'], 
        $personal['extension'], 
        $personal['gender'], 
        $personal['birthdate'], 
        $personal['birthplace'], 
        $personal['civil_status'], 
        $personal['email'], 
        $personal['phone'], 
        $personal['address'], 
        $personal['website']
    );
    $stmt->execute();
    $personal_info_id = $conn->insert_id;
    $stmt->close();
    
    // Store the ID in session
    $_SESSION['resume_data']['personal_info_id'] = $personal_info_id;
    
    // 2. Insert Career Objective
    if (!empty($_SESSION['resume_data']['objective'])) {
        $sql = "INSERT INTO career_objectives (personal_info_id, objective) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $personal_info_id, $_SESSION['resume_data']['objective']);
        $stmt->execute();
        $stmt->close();
    }
    
    // 3. Insert Education entries
    if (!empty($_SESSION['resume_data']['education'])) {
        $sql = "INSERT INTO education (personal_info_id, degree, institution, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($_SESSION['resume_data']['education'] as $edu) {
            $stmt->bind_param("isssss", 
                $personal_info_id, 
                $edu['degree'], 
                $edu['institution'], 
                $edu['start_date'], 
                $edu['end_date'], 
                $edu['description']
            );
            $stmt->execute();
        }
        $stmt->close();
    }
    
    // 4. Insert Work Experience entries
    if (!empty($_SESSION['resume_data']['work_experience'])) {
        $sql = "INSERT INTO work_experience (personal_info_id, job_title, city, employer, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($_SESSION['resume_data']['work_experience'] as $exp) {
            $stmt->bind_param("issssss", 
                $personal_info_id, 
                $exp['job_title'], 
                $exp['city'], 
                $exp['employer'], 
                $exp['start_date'], 
                $exp['end_date'], 
                $exp['description']
            );
            $stmt->execute();
        }
        $stmt->close();
    }
    
    // 5. Insert Skills
    if (!empty($_SESSION['resume_data']['skills'])) {
        $sql = "INSERT INTO skills (personal_info_id, skill_name, level) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($_SESSION['resume_data']['skills'] as $skill) {
            $stmt->bind_param("iss", 
                $personal_info_id, 
                $skill['skill_name'], 
                $skill['level']
            );
            $stmt->execute();
        }
        $stmt->close();
    }
    
    // 6. Insert Interests
    if (!empty($_SESSION['resume_data']['interests'])) {
        $sql = "INSERT INTO interests (personal_info_id, interests) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $personal_info_id, $_SESSION['resume_data']['interests']);
        $stmt->execute();
        $stmt->close();
    }
    
    // 7. Insert References
    if (!empty($_SESSION['resume_data']['references'])) {
        $sql = "INSERT INTO reference (personal_info_id, company_name, contact_person, phone_number, email) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($_SESSION['resume_data']['references'] as $ref) {
            $stmt->bind_param("issss", 
                $personal_info_id, 
                $ref['company_name'], 
                $ref['contact_person'], 
                $ref['phone_number'], 
                $ref['email']
            );
            $stmt->execute();
        }
        $stmt->close();
    }
    
    // Commit the transaction
    $conn->commit();
    
    // Redirect to preview
    header('Location: preview.php');
    exit();
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo "Error saving data: " . $e->getMessage();
    echo "<br><a href='references.php'>Go back</a>";
}

closeDBConnection($conn);
?>

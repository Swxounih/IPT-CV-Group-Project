<?php
session_start();
require_once 'config.php';

// Check if we have data to save
if (!isset($_SESSION['resume_data'])) {
    header('Location: personal-information.php');
    exit();
}

$is_editing = isset($_SESSION['resume_data']['is_editing']) && $_SESSION['resume_data']['is_editing'];
$personal_info_id = $_SESSION['resume_data']['personal_info_id'] ?? null;

// Get user_id from session (email will be used as user_id for multiple CVs)
$user_id = $_SESSION['resume_data']['personal_info']['email'] ?? null;

// If already saved and not editing, just show preview
if ($personal_info_id && !$is_editing) {
    header('Location: preview.php');
    exit();
}

$conn = getDBConnection();
$conn->begin_transaction();

try {
    if ($is_editing && $personal_info_id) {
        // UPDATE EXISTING CV
        
        // 1. Update Personal Information
        $personal = $_SESSION['resume_data']['personal_info'];
        $sql = "UPDATE personal_information SET 
                photo = ?, given_name = ?, middle_name = ?, surname = ?, extension = ?, 
                gender = ?, birthdate = ?, birthplace = ?, civil_status = ?, 
                email = ?, phone = ?, address = ?, website = ?,
                user_id = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssssssi", 
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
            $personal['website'],
            $user_id,
            $personal_info_id
        );
        $stmt->execute();
        $stmt->close();
        
        // Delete existing related data before inserting new
        $tables = ['career_objectives', 'education', 'work_experience', 'skills', 'interests', 'reference'];
        foreach ($tables as $table) {
            $sql = "DELETE FROM $table WHERE personal_info_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $personal_info_id);
            $stmt->execute();
            $stmt->close();
        }
        
    } else {
        // CREATE NEW CV
        
        // 1. Insert Personal Information
        $personal = $_SESSION['resume_data']['personal_info'];
        $cv_title = isset($_SESSION['resume_data']['is_additional_cv']) ? 'Additional Resume' : 'My Resume';
        
        $sql = "INSERT INTO personal_information 
                (photo, given_name, middle_name, surname, extension, gender, birthdate, birthplace, 
                 civil_status, email, phone, address, website, user_id, cv_title) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssssss", 
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
            $personal['website'],
            $user_id,
            $cv_title
        );
        $stmt->execute();
        $personal_info_id = $conn->insert_id;
        $stmt->close();
        
        // Store the ID in session
        $_SESSION['resume_data']['personal_info_id'] = $personal_info_id;
        
        // Store user_id for verification
        $_SESSION['verified_user_id'] = $user_id;
    }
    
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
    
    // 4. Insert Work Experience entries (using correct column names: job_title, employer, city)
    if (!empty($_SESSION['resume_data']['work_experience'])) {
        $sql = "INSERT INTO work_experience (personal_info_id, job_title, employer, city, start_date, end_date, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($_SESSION['resume_data']['work_experience'] as $exp) {
            $stmt->bind_param("issssss", 
                $personal_info_id, 
                $exp['job_title'], 
                $exp['employer'], 
                $exp['city'] ?? '', 
                $exp['start_date'], 
                $exp['end_date'], 
                $exp['description']
            );
            $stmt->execute();
        }
        $stmt->close();
    }
    
    // 5. Insert Skills (using correct column name: level)
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
    
    // 7. Insert References (using correct column names: contact_person, company_name, phone_number)
    if (!empty($_SESSION['resume_data']['references'])) {
        $sql = "INSERT INTO reference (personal_info_id, contact_person, company_name, phone_number, email) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($_SESSION['resume_data']['references'] as $ref) {
            $stmt->bind_param("issss", 
                $personal_info_id, 
                $ref['contact_person'], 
                $ref['company_name'], 
                $ref['phone_number'], 
                $ref['email']
            );
            $stmt->execute();
        }
        $stmt->close();
    }
    
    // Commit the transaction
    $conn->commit();
    
    // Clear the editing flag
    unset($_SESSION['resume_data']['is_editing']);
    unset($_SESSION['resume_data']['is_additional_cv']);
    
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
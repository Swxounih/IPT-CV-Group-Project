<?php
session_start();
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if we have data to save
if (!isset($_SESSION['resume_data'])) {
    die("ERROR: No resume data in session. <a href='personal-information.php'>Start over</a>");
}

$is_editing = isset($_SESSION['resume_data']['is_editing']) && $_SESSION['resume_data']['is_editing'];
$personal_info_id = $_SESSION['resume_data']['personal_info_id'] ?? null;

// Get user_id from session (email will be used as user_id for multiple CVs)
$user_id = $_SESSION['resume_data']['personal_info']['email'] ?? null;

if (empty($user_id)) {
    die("ERROR: No email found in resume data. <a href='personal-information.php'>Go back</a>");
}

// ALWAYS SAVE (remove the early exit check)
// This ensures data is saved every time you click Preview

$conn = getDBConnection();
if (!$conn) {
    die("ERROR: Could not connect to database. Check your config.php settings.");
}

$conn->begin_transaction();

try {
    if ($is_editing && $personal_info_id) {
        // UPDATE EXISTING CV
        echo "<!-- Updating existing CV ID: $personal_info_id -->\n";
        
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
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
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
        echo "<!-- Creating new CV -->\n";
        
        // 1. Insert Personal Information
        $personal = $_SESSION['resume_data']['personal_info'];
        $cv_title = isset($_SESSION['resume_data']['is_additional_cv']) ? 'Additional Resume' : 'My Resume';
        
        $sql = "INSERT INTO personal_information 
                (photo, given_name, middle_name, surname, extension, gender, birthdate, birthplace, 
                 civil_status, email, phone, address, website, user_id, cv_title) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
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
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $personal_info_id = $conn->insert_id;
        echo "<!-- New CV ID: $personal_info_id -->\n";
        
        if (!$personal_info_id) {
            throw new Exception("Failed to get insert ID for personal information");
        }
        
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
        if (!$stmt) {
            throw new Exception("Career objectives prepare failed: " . $conn->error);
        }
        $stmt->bind_param("is", $personal_info_id, $_SESSION['resume_data']['objective']);
        $stmt->execute();
        $stmt->close();
        echo "<!-- Career objective saved -->\n";
    }
    
    // 3. Insert Education entries
    if (!empty($_SESSION['resume_data']['education'])) {
        $sql = "INSERT INTO education (personal_info_id, degree, institution, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Education prepare failed: " . $conn->error);
        }
        
        $count = 0;
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
            $count++;
        }
        $stmt->close();
        echo "<!-- $count education entries saved -->\n";
    }
    
    // 4. Insert Work Experience entries - FIXED VERSION
    if (!empty($_SESSION['resume_data']['work_experience'])) {
        $sql = "INSERT INTO work_experience (personal_info_id, job_title, employer, city, start_date, end_date, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Work experience prepare failed: " . $conn->error);
        }
        
        $count = 0;
        foreach ($_SESSION['resume_data']['work_experience'] as $exp) {
            // Assign to variables first to avoid pass-by-reference error
            $job_title = $exp['job_title'] ?? '';
            $employer = $exp['employer'] ?? '';
            $city = $exp['city'] ?? '';
            $start_date = $exp['start_date'] ?? '';
            $end_date = $exp['end_date'] ?? '';
            $description = $exp['description'] ?? '';
            
            $stmt->bind_param("issssss", 
                $personal_info_id, 
                $job_title, 
                $employer, 
                $city, 
                $start_date, 
                $end_date, 
                $description
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Work experience execute failed: " . $stmt->error);
            }
            $count++;
        }
        $stmt->close();
        echo "<!-- $count work experience entries saved -->\n";
    }
    
    // 5. Insert Skills
    if (!empty($_SESSION['resume_data']['skills'])) {
        $sql = "INSERT INTO skills (personal_info_id, skill_name, level) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Skills prepare failed: " . $conn->error);
        }
        
        $count = 0;
        foreach ($_SESSION['resume_data']['skills'] as $skill) {
            $skill_name = $skill['skill_name'] ?? '';
            $level = $skill['level'] ?? '';
            
            $stmt->bind_param("iss", 
                $personal_info_id, 
                $skill_name, 
                $level
            );
            $stmt->execute();
            $count++;
        }
        $stmt->close();
        echo "<!-- $count skills saved -->\n";
    }
    
    // 6. Insert Interests
    if (!empty($_SESSION['resume_data']['interests'])) {
        $sql = "INSERT INTO interests (personal_info_id, interests) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Interests prepare failed: " . $conn->error);
        }
        $stmt->bind_param("is", $personal_info_id, $_SESSION['resume_data']['interests']);
        $stmt->execute();
        $stmt->close();
        echo "<!-- Interests saved -->\n";
    }
    
    // 7. Insert References
    if (!empty($_SESSION['resume_data']['references'])) {
        $sql = "INSERT INTO reference (personal_info_id, contact_person, company_name, phone_number, email) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("References prepare failed: " . $conn->error);
        }
        
        $count = 0;
        foreach ($_SESSION['resume_data']['references'] as $ref) {
            $contact_person = $ref['contact_person'] ?? '';
            $company_name = $ref['company_name'] ?? '';
            $phone_number = $ref['phone_number'] ?? '';
            $email = $ref['email'] ?? '';
            
            $stmt->bind_param("issss", 
                $personal_info_id, 
                $contact_person, 
                $company_name, 
                $phone_number, 
                $email
            );
            $stmt->execute();
            $count++;
        }
        $stmt->close();
        echo "<!-- $count references saved -->\n";
    }
    
    // Commit the transaction
    $conn->commit();
    echo "<!-- Transaction committed successfully! -->\n";
    
    // Clear the editing flag
    unset($_SESSION['resume_data']['is_editing']);
    unset($_SESSION['resume_data']['is_additional_cv']);
    
    closeDBConnection($conn);
    
    // Show success message and redirect
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Saving Resume...</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #1ebbeb 0%, #3450ce 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
            }
            .success-box {
                background: white;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                text-align: center;
                max-width: 500px;
            }
            .success-icon {
                font-size: 60px;
                color: #10b981;
                margin-bottom: 20px;
            }
            h1 {
                color: #333;
                margin-bottom: 10px;
            }
            p {
                color: #666;
                margin-bottom: 20px;
                line-height: 1.6;
            }
            .loader {
                border: 4px solid #f3f3f3;
                border-top: 4px solid #000000;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
                margin: 20px auto;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body>
        <div class='success-box'>
            <div class='success-icon'>✓</div>
            <h1>Saved Successfully!</h1>
            <p>Your CV has been saved to the database.<br>Redirecting to preview...</p>
            <div class='loader'></div>
        </div>
        <script>
            setTimeout(function() {
                window.location.href = 'preview.php';
            }, 2000);
        </script>
    </body>
    </html>";
    exit();
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    closeDBConnection($conn);
    
    error_log("Error saving resume: " . $e->getMessage());
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Error Saving Resume</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px;
            }
            .error-box {
                background: white;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                max-width: 600px;
            }
            .error-icon {
                font-size: 60px;
                color: #ef4444;
                text-align: center;
                margin-bottom: 20px;
            }
            h1 {
                color: #ef4444;
                margin-bottom: 20px;
                text-align: center;
            }
            .error-details {
                background: #fee;
                border: 1px solid #fcc;
                padding: 15px;
                border-radius: 8px;
                margin: 20px 0;
                font-family: monospace;
                font-size: 14px;
                color: #c33;
                overflow-x: auto;
            }
            .btn {
                display: inline-block;
                padding: 12px 30px;
                background: #3b82f6;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                margin: 10px 5px;
                transition: all 0.3s ease;
            }
            .btn:hover {
                background: #2563eb;
                transform: translateY(-2px);
            }
            .btn-container {
                text-align: center;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class='error-box'>
            <div class='error-icon'>⚠️</div>
            <h1>Error Saving Resume</h1>
            <p>There was an error saving your resume to the database:</p>
            <div class='error-details'>
                <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "<br>
                <strong>File:</strong> " . htmlspecialchars($e->getFile()) . "<br>
                <strong>Line:</strong> " . $e->getLine() . "
            </div>
            <div class='btn-container'>
                <a href='references.php' class='btn'>Go Back</a>
                <a href='personal-information.php' class='btn'>Start Over</a>
            </div>
        </div>
    </body>
    </html>";
    exit();
}
?>
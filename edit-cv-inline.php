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

if ($cv_id <= 0) {
    echo "<script>alert('Invalid CV ID'); window.location.href='dashboard.php';</script>";
    exit();
}

$conn = getDBConnection();

// Verify this CV belongs to the user
$sql = "SELECT user_id FROM personal_information WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$cv_data = $result->fetch_assoc();
$stmt->close();

if (!$cv_data || $cv_data['user_id'] !== $user_id) {
    echo "<script>alert('Unauthorized access'); window.location.href='dashboard.php';</script>";
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();

        // Update personal information
        $sql = "UPDATE personal_information SET 
                given_name = ?, middle_name = ?, surname = ?, extension = ?,
                gender = ?, birthdate = ?, birthplace = ?, civil_status = ?,
                email = ?, phone = ?, address = ?, website = ?, 
                cv_title = ?, updated_at = NOW()
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $cv_title = $_POST['cv_title'] ?? 'My Resume';
        $stmt->bind_param("sssssssssssssi",
            $_POST['given_name'], $_POST['middle_name'], $_POST['surname'], $_POST['extension'],
            $_POST['gender'], $_POST['birthdate'], $_POST['birthplace'], $_POST['civil_status'],
            $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['website'], 
            $cv_title, $cv_id);
        $stmt->execute();
        $stmt->close();

        // Update career objective
        $sql = "DELETE FROM career_objectives WHERE personal_info_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cv_id);
        $stmt->execute();
        $stmt->close();

        if (!empty($_POST['objective'])) {
            $sql = "INSERT INTO career_objectives (personal_info_id, objective) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $cv_id, $_POST['objective']);
            $stmt->execute();
            $stmt->close();
        }

        // Handle education entries
        $sql = "DELETE FROM education WHERE personal_info_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cv_id);
        $stmt->execute();
        $stmt->close();

        if (isset($_POST['education']) && is_array($_POST['education'])) {
            $sql = "INSERT INTO education (personal_info_id, degree, institution, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            foreach ($_POST['education'] as $edu) {
                if (!empty($edu['degree']) || !empty($edu['institution'])) {
                    $stmt->bind_param("isssss", $cv_id, $edu['degree'], $edu['institution'], 
                                     $edu['start_date'] ?? null, $edu['end_date'] ?? null, $edu['description'] ?? null);
                    $stmt->execute();
                }
            }
            $stmt->close();
        }

        // Handle work experience (FIXED: using job_title, employer, city)
        $sql = "DELETE FROM work_experience WHERE personal_info_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cv_id);
        $stmt->execute();
        $stmt->close();

        if (isset($_POST['work_experience']) && is_array($_POST['work_experience'])) {
            $sql = "INSERT INTO work_experience (personal_info_id, job_title, employer, city, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            foreach ($_POST['work_experience'] as $work) {
                if (!empty($work['job_title']) || !empty($work['employer'])) {
                    $stmt->bind_param("issssss", $cv_id, $work['job_title'], $work['employer'], 
                                     $work['city'] ?? '', $work['start_date'] ?? null, $work['end_date'] ?? null, $work['description'] ?? null);
                    $stmt->execute();
                }
            }
            $stmt->close();
        }

        // Handle skills (FIXED: using level instead of proficiency)
        $sql = "DELETE FROM skills WHERE personal_info_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cv_id);
        $stmt->execute();
        $stmt->close();

        if (isset($_POST['skills']) && is_array($_POST['skills'])) {
            $sql = "INSERT INTO skills (personal_info_id, skill_name, level) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            foreach ($_POST['skills'] as $skill) {
                if (!empty($skill['skill_name'])) {
                    $stmt->bind_param("iss", $cv_id, $skill['skill_name'], $skill['level'] ?? 'Intermediate');
                    $stmt->execute();
                }
            }
            $stmt->close();
        }

        // Handle interests
        $sql = "DELETE FROM interests WHERE personal_info_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cv_id);
        $stmt->execute();
        $stmt->close();

        if (!empty($_POST['interests'])) {
            $sql = "INSERT INTO interests (personal_info_id, interests) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $cv_id, $_POST['interests']);
            $stmt->execute();
            $stmt->close();
        }

        // Handle references (FIXED: using contact_person, company_name, phone_number)
        $sql = "DELETE FROM reference WHERE personal_info_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cv_id);
        $stmt->execute();
        $stmt->close();

        if (isset($_POST['references']) && is_array($_POST['references'])) {
            $sql = "INSERT INTO reference (personal_info_id, contact_person, company_name, phone_number, email) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            foreach ($_POST['references'] as $ref) {
                if (!empty($ref['contact_person'])) {
                    $stmt->bind_param("issss", $cv_id, $ref['contact_person'], $ref['company_name'] ?? null, 
                                     $ref['phone_number'] ?? null, $ref['email'] ?? null);
                    $stmt->execute();
                }
            }
            $stmt->close();
        }

        $conn->commit();
        $success_message = "CV updated successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error updating CV: " . $e->getMessage();
    }
}

// Load existing CV data
$sql = "SELECT * FROM personal_information WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$personal_info = $result->fetch_assoc();
$stmt->close();

// Load objective
$sql = "SELECT objective FROM career_objectives WHERE personal_info_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$objective = $result->num_rows > 0 ? $result->fetch_assoc()['objective'] : '';
$stmt->close();

// Load education
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

// Load work experience
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

// Load skills
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

// Load interests
$sql = "SELECT interests FROM interests WHERE personal_info_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$interests = $result->num_rows > 0 ? $result->fetch_assoc()['interests'] : '';
$stmt->close();

// Load references
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit CV - <?php echo htmlspecialchars($personal_info['given_name'] . ' ' . $personal_info['surname']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
        }
        
        .header .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .header .back-btn:hover {
            background: white;
            color: #667eea;
        }
        
        .content {
            padding: 40px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        
        .section {
            margin-bottom: 40px;
            padding-bottom: 40px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .section:last-child {
            border-bottom: none;
        }
        
        .section-title {
            font-size: 22px;
            color: #1f2937;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
            display: inline-block;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-row.full {
            grid-template-columns: 1fr;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        input, textarea, select {
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s ease;
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
            line-height: 1.6;
        }
        
        .entry-item {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .entry-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .entry-header h4 {
            font-size: 18px;
            color: #1f2937;
        }
        
        .entry-number {
            background: #667eea;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }
        
        .btn-add {
            background: #10b981;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-add:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }
        
        .btn-remove-entry {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-remove-entry:hover {
            background: #dc2626;
        }
        
        .btn-save {
            background: #667eea;
            color: white;
            border: none;
            padding: 15px 50px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-save:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-container {
            margin-top: 40px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 20px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Edit Resume</h1>
            <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        </div>
        
        <div class="content">
            <?php if ($success_message): ?>
                <div class="alert alert-success">✓ <?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">✗ <?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <!-- Personal Information Section -->
                <div class="section">
                    <h2 class="section-title">Personal Information</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cv_title">Resume Title</label>
                            <input type="text" id="cv_title" name="cv_title" value="<?php echo htmlspecialchars($personal_info['cv_title'] ?? 'My Resume'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="given_name">First Name *</label>
                            <input type="text" id="given_name" name="given_name" value="<?php echo htmlspecialchars($personal_info['given_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($personal_info['middle_name'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="surname">Last Name *</label>
                            <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($personal_info['surname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="extension">Extension</label>
                            <input type="text" id="extension" name="extension" value="<?php echo htmlspecialchars($personal_info['extension'] ?? ''); ?>" placeholder="Jr., Sr., III">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="gender">Gender *</label>
                            <select id="gender" name="gender" required>
                                <option value="male" <?php echo $personal_info['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo $personal_info['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="birthdate">Date of Birth *</label>
                            <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($personal_info['birthdate']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="birthplace">Place of Birth *</label>
                            <input type="text" id="birthplace" name="birthplace" value="<?php echo htmlspecialchars($personal_info['birthplace']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="civil_status">Civil Status *</label>
                            <select id="civil_status" name="civil_status" required>
                                <option value="single" <?php echo $personal_info['civil_status'] === 'single' ? 'selected' : ''; ?>>Single</option>
                                <option value="married" <?php echo $personal_info['civil_status'] === 'married' ? 'selected' : ''; ?>>Married</option>
                                <option value="divorced" <?php echo $personal_info['civil_status'] === 'divorced' ? 'selected' : ''; ?>>Divorced</option>
                                <option value="widowed" <?php echo $personal_info['civil_status'] === 'widowed' ? 'selected' : ''; ?>>Widowed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($personal_info['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone *</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($personal_info['phone']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address"><?php echo htmlspecialchars($personal_info['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($personal_info['website'] ?? ''); ?>" placeholder="https://">
                        </div>
                    </div>
                </div>
                
                <!-- Career Objective Section -->
                <div class="section">
                    <h2 class="section-title">Career Objective</h2>
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="objective">Your Career Objective</label>
                            <textarea id="objective" name="objective" rows="4"><?php echo htmlspecialchars($objective); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Education Section -->
                <div class="section">
                    <h2 class="section-title">Education</h2>
                    <div id="education-container">
                        <?php foreach ($education as $index => $edu): ?>
                        <div class="entry-item education-item">
                            <div class="entry-header">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <span class="entry-number"><?php echo $index + 1; ?></span>
                                    <h4><?php echo htmlspecialchars($edu['degree']); ?></h4>
                                </div>
                                <button type="button" class="btn-remove-entry" onclick="removeEducation(this)">Remove</button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Degree/Certification</label>
                                    <input type="text" name="education[<?php echo $index; ?>][degree]" value="<?php echo htmlspecialchars($edu['degree']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Institution</label>
                                    <input type="text" name="education[<?php echo $index; ?>][institution]" value="<?php echo htmlspecialchars($edu['institution']); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="education[<?php echo $index; ?>][start_date]" value="<?php echo htmlspecialchars($edu['start_date'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="education[<?php echo $index; ?>][end_date]" value="<?php echo htmlspecialchars($edu['end_date'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-row full">
                                <div class="form-group">
                                    <label>Description/Honors</label>
                                    <textarea name="education[<?php echo $index; ?>][description]"><?php echo htmlspecialchars($edu['description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn-add" onclick="addEducation()">+ Add Education</button>
                </div>
                
                <!-- Work Experience Section -->
                <div class="section">
                    <h2 class="section-title">Work Experience</h2>
                    <div id="experience-container">
                        <?php foreach ($work_experience as $index => $work): ?>
                        <div class="entry-item work-item">
                            <div class="entry-header">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <span class="entry-number"><?php echo $index + 1; ?></span>
                                    <h4><?php echo htmlspecialchars($work['job_title']); ?></h4>
                                </div>
                                <button type="button" class="btn-remove-entry" onclick="removeExperience(this)">Remove</button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Job Title</label>
                                    <input type="text" name="work_experience[<?php echo $index; ?>][job_title]" value="<?php echo htmlspecialchars($work['job_title']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Company/Employer</label>
                                    <input type="text" name="work_experience[<?php echo $index; ?>][employer]" value="<?php echo htmlspecialchars($work['employer']); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>City/Location</label>
                                    <input type="text" name="work_experience[<?php echo $index; ?>][city]" value="<?php echo htmlspecialchars($work['city'] ?? ''); ?>">
                                </div>
                                <div class="form-group"></div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="work_experience[<?php echo $index; ?>][start_date]" value="<?php echo htmlspecialchars($work['start_date'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="work_experience[<?php echo $index; ?>][end_date]" value="<?php echo htmlspecialchars($work['end_date'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-row full">
                                <div class="form-group">
                                    <label>Job Description/Achievements</label>
                                    <textarea name="work_experience[<?php echo $index; ?>][description]"><?php echo htmlspecialchars($work['description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn-add" onclick="addExperience()">+ Add Experience</button>
                </div>
                
                <!-- Skills Section -->
                <div class="section">
                    <h2 class="section-title">Skills</h2>
                    <div id="skills-container">
                        <?php foreach ($skills as $index => $skill): ?>
                        <div class="entry-item skill-item">
                            <div class="entry-header">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <span class="entry-number"><?php echo $index + 1; ?></span>
                                    <h4><?php echo htmlspecialchars($skill['skill_name']); ?></h4>
                                </div>
                                <button type="button" class="btn-remove-entry" onclick="removeSkill(this)">Remove</button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Skill Name</label>
                                    <input type="text" name="skills[<?php echo $index; ?>][skill_name]" value="<?php echo htmlspecialchars($skill['skill_name']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Proficiency Level</label>
                                    <select name="skills[<?php echo $index; ?>][level]">
                                        <option value="Beginner" <?php echo $skill['level'] === 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                                        <option value="Intermediate" <?php echo $skill['level'] === 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                        <option value="Skillful" <?php echo $skill['level'] === 'Skillful' ? 'selected' : ''; ?>>Skillful</option>
                                        <option value="Experienced" <?php echo $skill['level'] === 'Experienced' ? 'selected' : ''; ?>>Experienced</option>
                                        <option value="Expert" <?php echo $skill['level'] === 'Expert' ? 'selected' : ''; ?>>Expert</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn-add" onclick="addSkill()">+ Add Skill</button>
                </div>
                
                <!-- Interests Section -->
                <div class="section">
                    <h2 class="section-title">Interests & Hobbies</h2>
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="interests">Your Interests and Hobbies</label>
                            <textarea id="interests" name="interests" rows="4"><?php echo htmlspecialchars($interests); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- References Section -->
                <div class="section">
                    <h2 class="section-title">References</h2>
                    <div id="references-container">
                        <?php foreach ($references as $index => $ref): ?>
                        <div class="entry-item reference-item">
                            <div class="entry-header">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <span class="entry-number"><?php echo $index + 1; ?></span>
                                    <h4><?php echo htmlspecialchars($ref['contact_person']); ?></h4>
                                </div>
                                <button type="button" class="btn-remove-entry" onclick="removeReference(this)">Remove</button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Contact Person</label>
                                    <input type="text" name="references[<?php echo $index; ?>][contact_person]" value="<?php echo htmlspecialchars($ref['contact_person']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Company Name</label>
                                    <input type="text" name="references[<?php echo $index; ?>][company_name]" value="<?php echo htmlspecialchars($ref['company_name'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="references[<?php echo $index; ?>][email]" value="<?php echo htmlspecialchars($ref['email'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="tel" name="references[<?php echo $index; ?>][phone_number]" value="<?php echo htmlspecialchars($ref['phone_number'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn-add" onclick="addReference()">+ Add Reference</button>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn-save">💾 Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let educationCount = <?php echo count($education); ?>;
        let experienceCount = <?php echo count($work_experience); ?>;
        let skillsCount = <?php echo count($skills); ?>;
        let referencesCount = <?php echo count($references); ?>;

        function addEducation() {
            const container = document.getElementById('education-container');
            const html = `
                <div class="entry-item education-item">
                    <div class="entry-header">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span class="entry-number">${educationCount + 1}</span>
                            <h4>New Education Entry</h4>
                        </div>
                        <button type="button" class="btn-remove-entry" onclick="removeEducation(this)">Remove</button>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Degree/Certification</label>
                            <input type="text" name="education[${educationCount}][degree]">
                        </div>
                        <div class="form-group">
                            <label>Institution</label>
                            <input type="text" name="education[${educationCount}][institution]">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="education[${educationCount}][start_date]">
                        </div>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="education[${educationCount}][end_date]">
                        </div>
                    </div>
                    <div class="form-row full">
                        <div class="form-group">
                            <label>Description/Honors</label>
                            <textarea name="education[${educationCount}][description]"></textarea>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            educationCount++;
        }

        function removeEducation(btn) {
            btn.closest('.education-item').remove();
        }

        function addExperience() {
            const container = document.getElementById('experience-container');
            const html = `
                <div class="entry-item work-item">
                    <div class="entry-header">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span class="entry-number">${experienceCount + 1}</span>
                            <h4>New Work Experience</h4>
                        </div>
                        <button type="button" class="btn-remove-entry" onclick="removeExperience(this)">Remove</button>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Job Title</label>
                            <input type="text" name="work_experience[${experienceCount}][job_title]">
                        </div>
                        <div class="form-group">
                            <label>Company/Employer</label>
                            <input type="text" name="work_experience[${experienceCount}][employer]">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>City/Location</label>
                            <input type="text" name="work_experience[${experienceCount}][city]">
                        </div>
                        <div class="form-group"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="work_experience[${experienceCount}][start_date]">
                        </div>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="work_experience[${experienceCount}][end_date]">
                        </div>
                    </div>
                    <div class="form-row full">
                        <div class="form-group">
                            <label>Job Description/Achievements</label>
                            <textarea name="work_experience[${experienceCount}][description]"></textarea>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            experienceCount++;
        }

        function removeExperience(btn) {
            btn.closest('.work-item').remove();
        }

        function addSkill() {
            const container = document.getElementById('skills-container');
            const html = `
                <div class="entry-item skill-item">
                    <div class="entry-header">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span class="entry-number">${skillsCount + 1}</span>
                            <h4>New Skill</h4>
                        </div>
                        <button type="button" class="btn-remove-entry" onclick="removeSkill(this)">Remove</button>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Skill Name</label>
                            <input type="text" name="skills[${skillsCount}][skill_name]">
                        </div>
                        <div class="form-group">
                            <label>Proficiency Level</label>
                            <select name="skills[${skillsCount}][level]">
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate" selected>Intermediate</option>
                                <option value="Skillful">Skillful</option>
                                <option value="Experienced">Experienced</option>
                                <option value="Expert">Expert</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            skillsCount++;
        }

        function removeSkill(btn) {
            btn.closest('.skill-item').remove();
        }

        function addReference() {
            const container = document.getElementById('references-container');
            const html = `
                <div class="entry-item reference-item">
                    <div class="entry-header">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span class="entry-number">${referencesCount + 1}</span>
                            <h4>New Reference</h4>
                        </div>
                        <button type="button" class="btn-remove-entry" onclick="removeReference(this)">Remove</button>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Contact Person</label>
                            <input type="text" name="references[${referencesCount}][contact_person]">
                        </div>
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="references[${referencesCount}][company_name]">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="references[${referencesCount}][email]">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="references[${referencesCount}][phone_number]">
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            referencesCount++;
        }

        function removeReference(btn) {
            btn.closest('.reference-item').remove();
        }
    </script>
</body>
</html>
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

// Load existing CV data first
$sql = "SELECT * FROM personal_information WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cv_id);
$stmt->execute();
$result = $stmt->get_result();
$personal_info = $result->fetch_assoc();
$stmt->close();

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();

        // Handle photo upload
        $photo_filename = $personal_info['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['photo']['tmp_name'];
            $file_name = $_FILES['photo']['name'];
            $file_size = $_FILES['photo']['size'];
            $file_type = mime_content_type($file_tmp);
            
            // Validate file size (max 5MB)
            if ($file_size > 5 * 1024 * 1024) {
                throw new Exception('File size exceeds 5MB limit');
            }
            
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception('Only JPG, PNG, and GIF files are allowed');
            }
            
            // Create uploads directory if it doesn't exist
            if (!is_dir('uploads/photos')) {
                mkdir('uploads/photos', 0755, true);
            }
            
            // Delete old photo if it exists
            if (!empty($personal_info['photo']) && file_exists('uploads/photos/' . $personal_info['photo'])) {
                unlink('uploads/photos/' . $personal_info['photo']);
            }
            
            // Generate unique filename
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $photo_filename = 'photo_' . $cv_id . '_' . time() . '.' . $file_ext;
            $file_path = 'uploads/photos/' . $photo_filename;
            
            if (!move_uploaded_file($file_tmp, $file_path)) {
                throw new Exception('Failed to upload photo');
            }
        }

        // Update personal information
        $sql = "UPDATE personal_information SET 
                given_name = ?, middle_name = ?, surname = ?, extension = ?,
                gender = ?, birthdate = ?, birthplace = ?, civil_status = ?,
                email = ?, phone = ?, address = ?, website = ?, 
                photo = ?, updated_at = NOW()
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        $given_name = $_POST['given_name'] ?? '';
        $middle_name = $_POST['middle_name'] ?? '';
        $surname = $_POST['surname'] ?? '';
        $extension = $_POST['extension'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $birthdate = $_POST['birthdate'] ?? '';
        $birthplace = $_POST['birthplace'] ?? '';
        $civil_status = $_POST['civil_status'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $website = $_POST['website'] ?? '';
        
        $stmt->bind_param("sssssssssssssi",
            $given_name, $middle_name, $surname, $extension,
            $gender, $birthdate, $birthplace, $civil_status,
            $email, $phone, $address, $website, 
            $photo_filename, $cv_id);
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
            $objective_text = $_POST['objective'];
            $stmt->bind_param("is", $cv_id, $objective_text);
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
                    $edu_degree = $edu['degree'] ?? '';
                    $edu_institution = $edu['institution'] ?? '';
                    $edu_start_date = $edu['start_date'] ?? null;
                    $edu_end_date = $edu['end_date'] ?? null;
                    $edu_description = $edu['description'] ?? null;
                    $stmt->bind_param("isssss", $cv_id, $edu_degree, $edu_institution, 
                                     $edu_start_date, $edu_end_date, $edu_description);
                    $stmt->execute();
                }
            }
            $stmt->close();
        }

        // Handle work experience
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
                    $work_job_title = $work['job_title'] ?? '';
                    $work_employer = $work['employer'] ?? '';
                    $work_city = $work['city'] ?? '';
                    $work_start_date = $work['start_date'] ?? null;
                    $work_end_date = $work['end_date'] ?? null;
                    $work_description = $work['description'] ?? null;
                    $stmt->bind_param("issssss", $cv_id, $work_job_title, $work_employer, 
                                     $work_city, $work_start_date, $work_end_date, $work_description);
                    $stmt->execute();
                }
            }
            $stmt->close();
        }

        // Handle skills
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
                    $skill_name = $skill['skill_name'] ?? '';
                    $skill_level = $skill['level'] ?? 'Intermediate';
                    $stmt->bind_param("iss", $cv_id, $skill_name, $skill_level);
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
            $interests_text = $_POST['interests'];
            $stmt->bind_param("is", $cv_id, $interests_text);
            $stmt->execute();
            $stmt->close();
        }

        // Handle references
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
                    $ref_contact_person = $ref['contact_person'] ?? '';
                    $ref_company_name = $ref['company_name'] ?? null;
                    $ref_phone_number = $ref['phone_number'] ?? null;
                    $ref_email = $ref['email'] ?? null;
                    $stmt->bind_param("issss", $cv_id, $ref_contact_person, $ref_company_name, 
                                     $ref_phone_number, $ref_email);
                    $stmt->execute();
                }
            }
            $stmt->close();
        }

        $conn->commit();
        $success_message = "Resume updated successfully!";
        
        // Reload data after update
        $sql = "SELECT * FROM personal_information WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cv_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $personal_info = $result->fetch_assoc();
        $stmt->close();
        
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error updating resume: " . $e->getMessage();
    }
}

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
    <title>Edit Resume - <?php echo htmlspecialchars($personal_info['given_name'] . ' ' . $personal_info['surname']); ?></title>
    <link rel="stylesheet" href="css/edit-cv-inline-style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Edit Your Resume</h1>
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>
        
        <div class="content">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data" id="cvForm">
                <!-- Personal Information Section -->
                <div class="section">
                    <h2 class="section-title">Personal Information</h2>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="photo">Profile Photo</label>
                            <div class="photo-upload-wrapper">
                                <div class="photo-upload-input">
                                    <label for="photo" class="file-input-label">Choose Photo</label>
                                    <input type="file" id="photo" name="photo" accept="image/*" style="display: none;">
                                    <small class="file-hint">Max 5MB. JPG, PNG, or GIF format</small>
                                </div>
                                <?php if (!empty($personal_info['photo'])): ?>
                                <div class="photo-preview-container">
                                    <img src="uploads/photos/<?php echo htmlspecialchars($personal_info['photo']); ?>" 
                                         alt="Profile Photo" 
                                         class="current-photo"
                                         id="photoPreview">
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="given_name">First Name</label>
                            <input type="text" id="given_name" name="given_name" 
                                   value="<?php echo htmlspecialchars($personal_info['given_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" 
                                   value="<?php echo htmlspecialchars($personal_info['middle_name'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="surname">Last Name</label>
                            <input type="text" id="surname" name="surname" 
                                   value="<?php echo htmlspecialchars($personal_info['surname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="extension">Extension</label>
                            <input type="text" id="extension" name="extension" 
                                   value="<?php echo htmlspecialchars($personal_info['extension'] ?? ''); ?>" 
                                   placeholder="Jr., Sr., III">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                <option value="male" <?php echo $personal_info['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo $personal_info['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="birthdate">Date of Birth</label>
                            <input type="date" id="birthdate" name="birthdate" 
                                   value="<?php echo htmlspecialchars($personal_info['birthdate']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="birthplace">Place of Birth</label>
                            <input type="text" id="birthplace" name="birthplace" 
                                   value="<?php echo htmlspecialchars($personal_info['birthplace']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="civil_status">Civil Status</label>
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
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($personal_info['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($personal_info['phone']); ?>" required>
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
                            <label for="website">Website / Portfolio</label>
                            <input type="url" id="website" name="website" 
                                   value="<?php echo htmlspecialchars($personal_info['website'] ?? ''); ?>" 
                                   placeholder="https://your-website.com">
                        </div>
                    </div>
                </div>
                
                <!-- Career Objective Section -->
                <div class="section">
                    <h2 class="section-title">Career Objective</h2>
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="objective">Your Career Objective</label>
                            <textarea id="objective" name="objective" rows="5" 
                                      placeholder="Describe your career goals and aspirations..."><?php echo htmlspecialchars($objective); ?></textarea>
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
                                    <h4><?php echo htmlspecialchars($edu['degree'] ?: 'Education Entry'); ?></h4>
                                </div>
                                <button type="button" class="btn-remove-entry" onclick="removeEducation(this)">Remove</button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Degree / Certification</label>
                                    <input type="text" name="education[<?php echo $index; ?>][degree]" 
                                           value="<?php echo htmlspecialchars($edu['degree']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Institution / School</label>
                                    <input type="text" name="education[<?php echo $index; ?>][institution]" 
                                           value="<?php echo htmlspecialchars($edu['institution']); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="education[<?php echo $index; ?>][start_date]" 
                                           value="<?php echo htmlspecialchars($edu['start_date'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="education[<?php echo $index; ?>][end_date]" 
                                           value="<?php echo htmlspecialchars($edu['end_date'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-row full">
                                <div class="form-group">
                                    <label>Description / Honors</label>
                                    <textarea name="education[<?php echo $index; ?>][description]" 
                                              placeholder="Achievements, honors, GPA, etc."><?php echo htmlspecialchars($edu['description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn-add" onclick="addEducation()">Add Education</button>
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
                                    <h4><?php echo htmlspecialchars($work['job_title'] ?: 'Work Experience'); ?></h4>
                                </div>
                                <button type="button" class="btn-remove-entry" onclick="removeExperience(this)">Remove</button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Job Title</label>
                                    <input type="text" name="work_experience[<?php echo $index; ?>][job_title]" 
                                           value="<?php echo htmlspecialchars($work['job_title']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Company / Employer</label>
                                    <input type="text" name="work_experience[<?php echo $index; ?>][employer]" 
                                           value="<?php echo htmlspecialchars($work['employer']); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>City / Location</label>
                                    <input type="text" name="work_experience[<?php echo $index; ?>][city]" 
                                           value="<?php echo htmlspecialchars($work['city'] ?? ''); ?>">
                                </div>
                                <div class="form-group"></div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="work_experience[<?php echo $index; ?>][start_date]" 
                                           value="<?php echo htmlspecialchars($work['start_date'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="work_experience[<?php echo $index; ?>][end_date]" 
                                           value="<?php echo htmlspecialchars($work['end_date'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-row full">
                                <div class="form-group">
                                    <label>Job Description / Achievements</label>
                                    <textarea name="work_experience[<?php echo $index; ?>][description]" 
                                              placeholder="Responsibilities, achievements, skills used..."><?php echo htmlspecialchars($work['description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn-add" onclick="addExperience()">Add Experience</button>
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
                                    <h4><?php echo htmlspecialchars($skill['skill_name'] ?: 'Skill'); ?></h4>
                                </div>
                                <button type="button" class="btn-remove-entry" onclick="removeSkill(this)">Remove</button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Skill Name</label>
                                    <input type="text" name="skills[<?php echo $index; ?>][skill_name]" 
                                           value="<?php echo htmlspecialchars($skill['skill_name']); ?>">
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
                    <button type="button" class="btn-add" onclick="addSkill()">Add Skill</button>
                </div>
                
                <!-- Interests Section -->
                <div class="section">
                    <h2 class="section-title">Interests & Hobbies</h2>
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="interests">Your Interests and Hobbies</label>
                            <textarea id="interests" name="interests" rows="4" 
                                      placeholder="Reading, Photography, Traveling, etc."><?php echo htmlspecialchars($interests); ?></textarea>
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
                                    <h4><?php echo htmlspecialchars($ref['contact_person'] ?: 'Reference'); ?></h4>
                                </div>
                                <button type="button" class="btn-remove-entry" onclick="removeReference(this)">Remove</button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Contact Person</label>
                                    <input type="text" name="references[<?php echo $index; ?>][contact_person]" 
                                           value="<?php echo htmlspecialchars($ref['contact_person']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Company Name</label>
                                    <input type="text" name="references[<?php echo $index; ?>][company_name]" 
                                           value="<?php echo htmlspecialchars($ref['company_name'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" name="references[<?php echo $index; ?>][email]" 
                                           value="<?php echo htmlspecialchars($ref['email'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="references[<?php echo $index; ?>][phone_number]" 
                                           value="<?php echo htmlspecialchars($ref['phone_number'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn-add" onclick="addReference()">Add Reference</button>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn-save">Save All Changes</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let educationCount = <?php echo count($education); ?>;
        let experienceCount = <?php echo count($work_experience); ?>;
        let skillsCount = <?php echo count($skills); ?>;
        let referencesCount = <?php echo count($references); ?>;

        // Photo preview
        document.getElementById('photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('photoPreview');
                    if (preview) {
                        preview.src = e.target.result;
                    } else {
                        const container = document.querySelector('.photo-upload-wrapper');
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'current-photo';
                        img.id = 'photoPreview';
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'photo-preview-container';
                        previewDiv.appendChild(img);
                        container.appendChild(previewDiv);
                    }
                };
                reader.readAsDataURL(file);
            }
        });

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
                            <label>Degree / Certification</label>
                            <input type="text" name="education[${educationCount}][degree]">
                        </div>
                        <div class="form-group">
                            <label>Institution / School</label>
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
                            <label>Description / Honors</label>
                            <textarea name="education[${educationCount}][description]" placeholder="Achievements, honors, GPA, etc."></textarea>
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
                            <label>Company / Employer</label>
                            <input type="text" name="work_experience[${experienceCount}][employer]">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>City / Location</label>
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
                            <label>Job Description / Achievements</label>
                            <textarea name="work_experience[${experienceCount}][description]" placeholder="Responsibilities, achievements, skills used..."></textarea>
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
                            <label>Email Address</label>
                            <input type="email" name="references[${referencesCount}][email]">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
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

        // Form submission animation
        document.getElementById('cvForm').addEventListener('submit', function() {
            const btn = document.querySelector('.btn-save');
            btn.textContent = 'Saving...';
            btn.style.opacity = '0.7';
            btn.disabled = true;
        });

        // Smooth scroll to top on success
        <?php if ($success_message): ?>
        window.scrollTo({ top: 0, behavior: 'smooth' });
        <?php endif; ?>
    </script>
</body>
</html>     
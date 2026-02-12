<?php
session_start();
require_once 'config.php';

// Check if personal info exists
if (!isset($_SESSION['resume_data']['personal_info_id'])) {
    header('Location: personal-information.php');
    exit();
}

// Handle reset
if (isset($_GET['reset'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Get all resume data from database
$conn = getDBConnection();
$personal_info_id = $_SESSION['resume_data']['personal_info_id'];

// Get personal information
$sql = "SELECT * FROM personal_information WHERE id = $personal_info_id";
$result = $conn->query($sql);
$personal = $result->fetch_assoc();

// Get career objective
$sql = "SELECT objective FROM career_objectives WHERE personal_info_id = $personal_info_id";
$result = $conn->query($sql);
$objective = '';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $objective = $row['objective'];
}

// Get education
$sql = "SELECT * FROM education WHERE personal_info_id = $personal_info_id ORDER BY start_date DESC";
$result = $conn->query($sql);
$education = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $education[] = $row;
    }
}

// Get work experience
$sql = "SELECT * FROM work_experience WHERE personal_info_id = $personal_info_id ORDER BY start_date DESC";
$result = $conn->query($sql);
$work_experience = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $work_experience[] = $row;
    }
}

// Get skills
$sql = "SELECT * FROM skills WHERE personal_info_id = $personal_info_id";
$result = $conn->query($sql);
$skills = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $skills[] = $row;
    }
}

// Get interests
$sql = "SELECT interests FROM interests WHERE personal_info_id = $personal_info_id";
$result = $conn->query($sql);
$interests = '';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $interests = $row['interests'];
}

// Get references
$sql = "SELECT * FROM reference WHERE personal_info_id = $personal_info_id";
$result = $conn->query($sql);
$references = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $references[] = $row;
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Preview</title>
    <style>
        body { 
            font-family: 'Georgia', serif; 
            max-width: 900px; 
            margin: 30px auto; 
            padding: 40px; 
            background: #f5f5f5;
        }
        .resume-container {
            background: white;
            padding: 50px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 10px 0;
            color: #333;
            font-size: 32px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
        }
        .section {
            margin: 30px 0;
        }
        .section h2 {
            color: #333;
            border-bottom: 2px solid #666;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 20px;
        }
        .section h3 {
            color: #555;
            margin: 15px 0 5px 0;
            font-size: 16px;
        }
        .section p {
            margin: 5px 0;
            color: #444;
            line-height: 1.6;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 15px 0;
        }
        .skill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .skill-item {
            background: #f0f0f0;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
        }
        button {
            padding: 12px 30px;
            cursor: pointer;
            font-size: 16px;
            border: none;
            border-radius: 5px;
        }
        .btn-back { background: #666; color: white; }
        .btn-print { background: #4CAF50; color: white; }
        .btn-reset { background: #ff4444; color: white; }
        @media print {
            body { background: white; }
            .resume-container { box-shadow: none; padding: 0; }
            .btn-container { display: none; }
        }
    </style>
</head>
<body>
    <div class="resume-container">
        <!-- Header Section -->
        <div class="header">
            <?php if (!empty($personal['photo']) && file_exists($personal['photo'])): ?>
                <img src="<?php echo htmlspecialchars($personal['photo']); ?>" alt="Profile Photo" class="photo">
            <?php endif; ?>
            
            <h1>
                <?php 
                echo htmlspecialchars($personal['given_name'] ?? '');
                if (!empty($personal['middle_name'])) echo ' ' . htmlspecialchars($personal['middle_name']);
                echo ' ' . htmlspecialchars($personal['surname'] ?? '');
                if (!empty($personal['extension'])) echo ' ' . htmlspecialchars($personal['extension']);
                ?>
            </h1>
            
            <p><?php echo htmlspecialchars($personal['address'] ?? ''); ?></p>
            <p>
                <?php echo htmlspecialchars($personal['phone'] ?? ''); ?> | 
                <?php echo htmlspecialchars($personal['email'] ?? ''); ?>
                <?php if (!empty($personal['website'])): ?>
                    | <?php echo htmlspecialchars($personal['website']); ?>
                <?php endif; ?>
            </p>
        </div>

        <!-- Personal Information -->
        <div class="section">
            <h2>Personal Information</h2>
            <div class="info-grid">
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($personal['birthdate'] ?? ''); ?></p>
                <p><strong>Place of Birth:</strong> <?php echo htmlspecialchars($personal['birthplace'] ?? ''); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars(ucfirst($personal['gender'] ?? '')); ?></p>
                <p><strong>Civil Status:</strong> <?php echo htmlspecialchars(ucfirst($personal['civil_status'] ?? '')); ?></p>
            </div>
        </div>

        <!-- Career Objective -->
        <?php if (!empty($objective)): ?>
        <div class="section">
            <h2>Career Objective</h2>
            <p><?php echo nl2br(htmlspecialchars($objective)); ?></p>
        </div>
        <?php endif; ?>

        <!-- Education -->
        <?php if (!empty($education)): ?>
        <div class="section">
            <h2>Education</h2>
            <?php foreach ($education as $edu): ?>
                <h3><?php echo htmlspecialchars($edu['degree']); ?></h3>
                <p><strong><?php echo htmlspecialchars($edu['institution']); ?></strong></p>
                <p><?php echo htmlspecialchars($edu['start_date']); ?> - <?php echo htmlspecialchars($edu['end_date']); ?></p>
                <?php if (!empty($edu['description'])): ?>
                    <p><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Work Experience -->
        <?php if (!empty($work_experience)): ?>
        <div class="section">
            <h2>Work Experience</h2>
            <?php foreach ($work_experience as $exp): ?>
                <h3><?php echo htmlspecialchars($exp['job_title']); ?></h3>
                <p><strong><?php echo htmlspecialchars($exp['employer']); ?></strong> - <?php echo htmlspecialchars($exp['city']); ?></p>
                <p><?php echo htmlspecialchars($exp['start_date']); ?> - <?php echo htmlspecialchars($exp['end_date']); ?></p>
                <?php if (!empty($exp['description'])): ?>
                    <p><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Skills -->
        <?php if (!empty($skills)): ?>
        <div class="section">
            <h2>Skills and Competencies</h2>
            <div class="skill-list">
                <?php foreach ($skills as $skill): ?>
                    <div class="skill-item">
                        <strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong> - 
                        <?php echo htmlspecialchars($skill['level']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Interests -->
        <?php if (!empty($interests)): ?>
        <div class="section">
            <h2>Interests and Hobbies</h2>
            <p><?php echo nl2br(htmlspecialchars($interests)); ?></p>
        </div>
        <?php endif; ?>

        <!-- References -->
        <?php if (!empty($references)): ?>
        <div class="section">
            <h2>References</h2>
            <?php foreach ($references as $ref): ?>
                <h3><?php echo htmlspecialchars($ref['contact_person']); ?></h3>
                <p><strong><?php echo htmlspecialchars($ref['company_name']); ?></strong></p>
                <p>Phone: <?php echo htmlspecialchars($ref['phone_number']); ?></p>
                <p>Email: <?php echo htmlspecialchars($ref['email']); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div class="btn-container">
        <button class="btn-back" onclick="window.location.href='references.php'">Back to Edit</button>
        <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
        <button class="btn-reset" onclick="if(confirm('Are you sure you want to start over? All data will be lost.')) window.location.href='preview.php?reset=1'">Start New Resume</button>
    </div>
</body>
</html>
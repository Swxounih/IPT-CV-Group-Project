<?php
session_start();
require_once 'config.php';

// Handle deleting a work experience entry from SESSION
if (isset($_GET['delete'])) {
    $index = (int)$_GET['delete'];
    if (isset($_SESSION['resume_data']['work_experience'][$index])) {
        array_splice($_SESSION['resume_data']['work_experience'], $index, 1);
    }
    header('Location: work-experience.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_experience'])) {
        // Add new work experience entry to SESSION (not database yet!)
        $experience = array(
            'job_title' => $_POST['job_title'] ?? '',
            'city' => $_POST['city'] ?? '',
            'employer' => $_POST['employer'] ?? '',
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'description' => $_POST['description'] ?? ''
        );
        
        if (!isset($_SESSION['resume_data']['work_experience'])) {
            $_SESSION['resume_data']['work_experience'] = array();
        }
        $_SESSION['resume_data']['work_experience'][] = $experience;
        
        header('Location: work-experience.php');
        exit();
    } elseif (isset($_POST['next'])) {
        header('Location: skills.php');
        exit();
    }
}

// Get existing work experience entries from SESSION
$experience_list = $_SESSION['resume_data']['work_experience'] ?? array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Experience - CV Builder</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/cv-builder-styles.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar-nav.php'; ?>
    
    <div class="main-content">
        <div class="form-container">
            
            <!-- Display existing work experience entries -->
            <?php if (!empty($experience_list)): ?>
                <div class="experience-list">
                    <div class="section-title">Added Work Experience</div>
                    <?php foreach ($experience_list as $index => $exp): ?>
                        <div class="experience-entry">
                            <h4><?php echo htmlspecialchars($exp['job_title']); ?> - <?php echo htmlspecialchars($exp['employer']); ?></h4>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($exp['city']); ?></p>
                            <p><strong>Period:</strong> <?php echo htmlspecialchars($exp['start_date']); ?> to <?php echo htmlspecialchars($exp['end_date']); ?></p>
                            <p><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                            <a href="work-experience.php?delete=<?php echo $index; ?>" onclick="return confirm('Are you sure you want to delete this entry?');">
                                <button type="button" class="delete-btn">Delete</button>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Form to add new work experience entry -->
            <div class="add-form">
                <div class="section-title">Add Work Experience Entry</div>
                <form action="work-experience.php" method="post">
                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label for="job_title">Job Title</label>
                            <input type="text" id="job_title" name="job_title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="city">City/Town</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="employer">Employer</label>
                            <input type="text" id="employer" name="employer" required>
                        </div>
                    </div>
                    
                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"></textarea>
                        </div>
                    </div>

                    <div class="add-btn-container">
                        <button type="submit" name="add_experience" class="add-btn">+ Add Experience</button>
                    </div>
                </form>
            </div>
            
            <!-- Sticky Navigation Buttons -->
            <div class="btn-container">
                <button type="button" class="back-btn" onclick="window.location.href='education.php'">Back</button>
                <form action="work-experience.php" method="post" style="display: inline;">
                    <button type="submit" name="next" class="next-btn">Next Step</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

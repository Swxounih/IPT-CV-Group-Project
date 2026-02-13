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
    <title>Work Experience</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 50px auto; padding: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button, input[type="submit"] { padding: 10px 20px; margin-top: 20px; cursor: pointer; }
        h3 { color: #333; }
        .btn-container { display: flex; gap: 10px; margin-top: 20px; }
        .experience-entry { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .experience-entry h4 { margin-top: 0; color: #555; }
        .delete-btn { background: #ff4444; color: white; border: none; padding: 5px 10px; border-radius: 3px; }
        .add-form { border: 2px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
        .info-note { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="info-note">
        ℹ️ <strong>Note:</strong> Your data will be saved to the database only after you complete all steps and click "Submit" on the final page.
    </div>
    
    <h3>Work Experience</h3>
    
    <!-- Display existing work experience entries -->
    <?php if (!empty($experience_list)): ?>
        <div style="margin-bottom: 30px;">
            <h4>Added Work Experience:</h4>
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
        <h4>Add Work Experience Entry</h4>
        <form action="work-experience.php" method="post">
            <label for="job_title">Job Title:</label>
            <input type="text" id="job_title" name="job_title" required>
            
            <label for="city">City/Town:</label>
            <input type="text" id="city" name="city" required>
            
            <label for="employer">Employer:</label>
            <input type="text" id="employer" name="employer" required>
            
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
            
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" cols="50"></textarea>

            <button type="submit" name="add_experience">Add Work Experience</button>
        </form>
    </div>
    
    <!-- Navigation buttons -->
    <form action="work-experience.php" method="post">
        <div class="btn-container">
            <button type="button" onclick="window.location.href='education.php'">Back</button>
            <input type="submit" name="next" value="Next">
        </div>
    </form>
</body>
</html>
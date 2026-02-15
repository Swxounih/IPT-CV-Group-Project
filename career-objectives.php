<?php
session_start();
require_once 'config.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store in SESSION only (not database yet!)
    $_SESSION['resume_data']['objective'] = $_POST['objective'] ?? '';
    header('Location: education.php');
    exit();
}

// Get existing data from session
$objective = $_SESSION['resume_data']['objective'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Objectives - CV Builder</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/cv-builder-styles.css">
    <style>
        /* Page-specific overrides if needed */
        .form-group {
            flex: 1;
        }
        
        .form-group textarea {
            flex: 1;
            min-height: 300px;
            resize: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar-nav.php'; ?>
    
    <div class="main-content">
        <div class="form-container">
            <form action="career-objectives.php" method="post" class="form-content">
                <div class="form-group">
                    <label for="objective">Objective</label>
                    <textarea 
                        id="objective" 
                        name="objective" 
                        required><?php echo htmlspecialchars($objective); ?></textarea>
                </div>
            </form>
            
            <!-- Sticky Navigation Buttons -->
            <div class="btn-container">
                <button type="button" class="back-btn" onclick="window.location.href='personal-information.php'">Back</button>
                <button type="button" class="next-btn" onclick="document.querySelector('form').submit()">Next Step</button>
            </div>
        </div>
    </div>
</body>
</html>
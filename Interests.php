<?php
session_start();
require_once 'config.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store in SESSION only (not database yet!)
    $_SESSION['resume_data']['interests'] = $_POST['interests'] ?? '';
    header('Location: references.php');
    exit();
}

// Get existing data from SESSION
$interests = $_SESSION['resume_data']['interests'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interests & Hobbies - CV Builder</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/cv-builder-styles.css">
    <style>
        /* Page-specific overrides */
        .form-row {
            flex: 1;
        }
        
        .form-row textarea {
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
            <form action="interests.php" method="post" class="form-content">
                <div class="form-row">
                    <div class="form-group">
                        <label for="interests">Interests and Hobbies</label>
                        <textarea id="interests" name="interests" placeholder="e.g., Photography, Hiking, Reading, Playing Guitar, Volunteering..."><?php echo htmlspecialchars($interests); ?></textarea>
                    </div>
                </div>
            </form>

            <!-- Sticky Navigation Buttons -->
            <div class="btn-container">
                <button type="button" class="back-btn" onclick="window.location.href='skills.php'">Back</button>
                <button type="button" class="next-btn" onclick="document.querySelector('form').submit()">Next Step</button>
            </div>
        </div>
    </div>
</body>
</html>
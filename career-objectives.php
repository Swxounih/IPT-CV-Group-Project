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
    <title>Career Objectives</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        input[type="submit"], button { padding: 10px 20px; margin-top: 20px; cursor: pointer; }
        h3 { color: #333; }
        .btn-container { display: flex; gap: 10px; }
        .info-note { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="info-note">
        ℹ️ <strong>Note:</strong> Your data will be saved to the database only after you complete all steps and click "Submit" on the final page.
    </div>
    
    <form action="career-objectives.php" method="post">
        <h3>Career Objectives</h3>

        <label for="objective">Objective:</label>
        <textarea id="objective" name="objective" rows="6" cols="50" required><?php echo htmlspecialchars($objective); ?></textarea>

        <div class="btn-container">
            <button type="button" onclick="window.location.href='personal-information.php'">Back</button>
            <input type="submit" value="Next">
        </div>
    </form>
</body>
</html>
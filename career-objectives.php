<?php
session_start();
require_once 'config.php';

// Check if personal info exists
if (!isset($_SESSION['resume_data']['personal_info_id'])) {
    header('Location: personal-information.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $personal_info_id = $_SESSION['resume_data']['personal_info_id'];
    $objective = $conn->real_escape_string($_POST['objective'] ?? '');
    
    // Insert into database
    $sql = "INSERT INTO career_objectives (personal_info_id, objective) 
            VALUES ('$personal_info_id', '$objective')";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['resume_data']['objective'] = $_POST['objective'] ?? '';
        closeDBConnection($conn);
        header('Location: education.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        closeDBConnection($conn);
    }
}

// Get existing data
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
    </style>
</head>
<body>
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
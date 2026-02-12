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
    $interests = $conn->real_escape_string($_POST['interests'] ?? '');
    
    // Check if interests already exist for this user
    $check_sql = "SELECT id FROM interests WHERE personal_info_id = $personal_info_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        // Update existing
        $sql = "UPDATE interests SET interests = '$interests' WHERE personal_info_id = $personal_info_id";
    } else {
        // Insert new
        $sql = "INSERT INTO interests (personal_info_id, interests) VALUES ('$personal_info_id', '$interests')";
    }
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['resume_data']['interests'] = $_POST['interests'] ?? '';
        closeDBConnection($conn);
        header('Location: references.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        closeDBConnection($conn);
    }
}

// Get existing data from database
$conn = getDBConnection();
$personal_info_id = $_SESSION['resume_data']['personal_info_id'];
$sql = "SELECT interests FROM interests WHERE personal_info_id = $personal_info_id";
$result = $conn->query($sql);
$interests = '';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $interests = $row['interests'];
}
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interests</title>
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
    <form action="interests.php" method="post">
        <h3>Interests and Hobbies</h3>

        <label for="interests">Interests and Hobbies:</label>
        <textarea id="interests" name="interests" rows="6" cols="50"><?php echo htmlspecialchars($interests); ?></textarea>

        <div class="btn-container">
            <button type="button" onclick="window.location.href='skills.php'">Back</button>
            <input type="submit" value="Next">
        </div>
    </form>
</body>
</html>
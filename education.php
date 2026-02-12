<?php
session_start();
require_once 'config.php';

// Check if personal info exists
if (!isset($_SESSION['resume_data']['personal_info_id'])) {
    header('Location: personal-information.php');
    exit();
}

// Handle deleting an education entry
if (isset($_GET['delete'])) {
    $conn = getDBConnection();
    $id = (int)$_GET['delete'];
    
    $sql = "DELETE FROM education WHERE id = $id AND personal_info_id = " . $_SESSION['resume_data']['personal_info_id'];
    $conn->query($sql);
    
    closeDBConnection($conn);
    header('Location: education.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    $personal_info_id = $_SESSION['resume_data']['personal_info_id'];
    
    if (isset($_POST['add_education'])) {
        // Add new education entry
        $degree = $conn->real_escape_string($_POST['degree'] ?? '');
        $institution = $conn->real_escape_string($_POST['institution'] ?? '');
        $start_date = $conn->real_escape_string($_POST['start_date'] ?? '');
        $end_date = $conn->real_escape_string($_POST['end_date'] ?? '');
        $description = $conn->real_escape_string($_POST['description'] ?? '');
        
        $sql = "INSERT INTO education (personal_info_id, degree, institution, start_date, end_date, description) 
                VALUES ('$personal_info_id', '$degree', '$institution', '$start_date', '$end_date', '$description')";
        
        if ($conn->query($sql) === TRUE) {
            closeDBConnection($conn);
            header('Location: education.php');
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['next'])) {
        closeDBConnection($conn);
        header('Location: work-experience.php');
        exit();
    }
    
    closeDBConnection($conn);
}

// Get existing education entries from database
$conn = getDBConnection();
$personal_info_id = $_SESSION['resume_data']['personal_info_id'];
$sql = "SELECT * FROM education WHERE personal_info_id = $personal_info_id ORDER BY start_date DESC";
$result = $conn->query($sql);
$education_list = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $education_list[] = $row;
    }
}
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Education and Qualifications</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 50px auto; padding: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button, input[type="submit"] { padding: 10px 20px; margin-top: 20px; cursor: pointer; }
        h3 { color: #333; }
        .btn-container { display: flex; gap: 10px; margin-top: 20px; }
        .education-entry { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .education-entry h4 { margin-top: 0; color: #555; }
        .delete-btn { background: #ff4444; color: white; border: none; padding: 5px 10px; border-radius: 3px; }
        .add-form { border: 2px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <h3>Education and Qualifications</h3>
    
    <!-- Display existing education entries -->
    <?php if (!empty($education_list)): ?>
        <div style="margin-bottom: 30px;">
            <h4>Added Education Entries:</h4>
            <?php foreach ($education_list as $edu): ?>
                <div class="education-entry">
                    <h4><?php echo htmlspecialchars($edu['degree']); ?> - <?php echo htmlspecialchars($edu['institution']); ?></h4>
                    <p><strong>Period:</strong> <?php echo htmlspecialchars($edu['start_date']); ?> to <?php echo htmlspecialchars($edu['end_date']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                    <a href="education.php?delete=<?php echo $edu['id']; ?>" onclick="return confirm('Are you sure you want to delete this entry?');">
                        <button type="button" class="delete-btn">Delete</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Form to add new education entry -->
    <div class="add-form">
        <h4>Add Education Entry</h4>
        <form action="education.php" method="post">
            <label for="degree">Degree/Qualification:</label>
            <input type="text" id="degree" name="degree" required>
            
            <label for="institution">Institution:</label>
            <input type="text" id="institution" name="institution" required>
            
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
            
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" cols="50"></textarea>

            <button type="submit" name="add_education">Add Education Entry</button>
        </form>
    </div>
    
    <!-- Navigation buttons -->
    <form action="education.php" method="post">
        <div class="btn-container">
            <button type="button" onclick="window.location.href='career-objectives.php'">Back</button>
            <input type="submit" name="next" value="Next">
        </div>
    </form>
</body>
</html>
<?php
session_start();
require_once 'config.php';

// Check if personal info exists
if (!isset($_SESSION['resume_data']['personal_info_id'])) {
    header('Location: personal-information.php');
    exit();
}

// Handle deleting a skill entry
if (isset($_GET['delete'])) {
    $conn = getDBConnection();
    $id = (int)$_GET['delete'];
    
    $sql = "DELETE FROM skills WHERE id = $id AND personal_info_id = " . $_SESSION['resume_data']['personal_info_id'];
    $conn->query($sql);
    
    closeDBConnection($conn);
    header('Location: skills.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    $personal_info_id = $_SESSION['resume_data']['personal_info_id'];
    
    if (isset($_POST['add_skill'])) {
        // Add new skill entry
        $skill_name = $conn->real_escape_string($_POST['skills'] ?? '');
        $level = $conn->real_escape_string($_POST['level'] ?? '');
        
        $sql = "INSERT INTO skills (personal_info_id, skill_name, level) 
                VALUES ('$personal_info_id', '$skill_name', '$level')";
        
        if ($conn->query($sql) === TRUE) {
            closeDBConnection($conn);
            header('Location: skills.php');
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['next'])) {
        closeDBConnection($conn);
        header('Location: interests.php');
        exit();
    }
    
    closeDBConnection($conn);
}

// Get existing skills from database
$conn = getDBConnection();
$personal_info_id = $_SESSION['resume_data']['personal_info_id'];
$sql = "SELECT * FROM skills WHERE personal_info_id = $personal_info_id";
$result = $conn->query($sql);
$skills_list = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $skills_list[] = $row;
    }
}
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 50px auto; padding: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button, input[type="submit"] { padding: 10px 20px; margin-top: 20px; cursor: pointer; }
        h3 { color: #333; }
        .btn-container { display: flex; gap: 10px; margin-top: 20px; }
        .skill-entry { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; }
        .delete-btn { background: #ff4444; color: white; border: none; padding: 5px 10px; border-radius: 3px; }
        .add-form { border: 2px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <h3>Skills and Competencies</h3>
    
    <!-- Display existing skills -->
    <?php if (!empty($skills_list)): ?>
        <div style="margin-bottom: 30px;">
            <h4>Added Skills:</h4>
            <?php foreach ($skills_list as $skill): ?>
                <div class="skill-entry">
                    <div>
                        <strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong> - 
                        <span style="color: #666;"><?php echo htmlspecialchars($skill['level']); ?></span>
                    </div>
                    <a href="skills.php?delete=<?php echo $skill['id']; ?>" onclick="return confirm('Are you sure you want to delete this skill?');">
                        <button type="button" class="delete-btn">Delete</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Form to add new skill -->
    <div class="add-form">
        <h4>Add Skill Entry</h4>
        <form action="skills.php" method="post">
            <label for="skills">Skill Name:</label>
            <input type="text" name="skills" id="skills" required>
            
            <label for="level">Level of Competency:</label>
            <select id="level" name="level" required>
                <option value="">Select Level</option>
                <option value="Expert">Expert</option>
                <option value="Experienced">Experienced</option>
                <option value="Skillful">Skillful</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Beginner">Beginner</option>
            </select>

            <button type="submit" name="add_skill">Add Skill</button>
        </form>
    </div>
    
    <!-- Navigation buttons -->
    <form action="skills.php" method="post">
        <div class="btn-container">
            <button type="button" onclick="window.location.href='work-experience.php'">Back</button>
            <input type="submit" name="next" value="Next">
        </div>
    </form>
</body>
</html>
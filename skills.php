<?php
session_start();
require_once 'config.php';

// Handle deleting a skill entry from SESSION
if (isset($_GET['delete'])) {
    $index = (int)$_GET['delete'];
    if (isset($_SESSION['resume_data']['skills'][$index])) {
        array_splice($_SESSION['resume_data']['skills'], $index, 1);
    }
    header('Location: skills.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_skill'])) {
        // Add new skill entry to SESSION (not database yet!)
        $skill = array(
            'skill_name' => $_POST['skills'] ?? '',
            'level' => $_POST['level'] ?? ''
        );
        
        if (!isset($_SESSION['resume_data']['skills'])) {
            $_SESSION['resume_data']['skills'] = array();
        }
        $_SESSION['resume_data']['skills'][] = $skill;
        
        header('Location: skills.php');
        exit();
    } elseif (isset($_POST['next'])) {
        header('Location: interests.php');
        exit();
    }
}

// Get existing skills from SESSION
$skills_list = $_SESSION['resume_data']['skills'] ?? array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills - CV Builder</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/cv-builder-styles.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar-nav.php'; ?>
    
    <div class="main-content">
        <div class="form-container">
            
            <!-- Display existing skills -->
            <?php if (!empty($skills_list)): ?>
                <div class="skills-list">
                    <div class="section-title">Added Skills</div>
                    <?php foreach ($skills_list as $index => $skill): ?>
                        <div class="skill-entry">
                            <div class="skill-info">
                                <h4><?php echo htmlspecialchars($skill['skill_name']); ?></h4>
                                <p>Level: <?php echo htmlspecialchars($skill['level']); ?></p>
                            </div>
                            <a href="skills.php?delete=<?php echo $index; ?>" onclick="return confirm('Are you sure you want to delete this skill?');">
                                <button type="button" class="delete-btn">Delete</button>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Form to add new skill -->
            <div class="add-form">
                <div class="section-title">Add Skill Entry</div>
                <form action="skills.php" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="skills">Skill Name</label>
                            <input type="text" id="skills" name="skills" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="level">Level of Competency</label>
                            <select id="level" name="level" required>
                                <option value="">Select Level</option>
                                <option value="Expert">Expert</option>
                                <option value="Experienced">Experienced</option>
                                <option value="Skillful">Skillful</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Beginner">Beginner</option>
                            </select>
                        </div>
                    </div>

                    <div class="add-btn-container">
                        <button type="submit" name="add_skill" class="add-btn">+ Add Skill</button>
                    </div>
                </form>
            </div>
            
            <!-- Sticky Navigation Buttons -->
            <div class="btn-container">
                <button type="button" class="back-btn" onclick="window.location.href='work-experience.php'">Back</button>
                <form action="skills.php" method="post" style="display: inline;">
                    <button type="submit" name="next" class="next-btn">Next Step</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

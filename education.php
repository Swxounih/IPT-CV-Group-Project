<?php
session_start();
require_once 'config.php';

// Handle deleting an education entry from SESSION
if (isset($_GET['delete'])) {
    $index = (int)$_GET['delete'];
    if (isset($_SESSION['resume_data']['education'][$index])) {
        array_splice($_SESSION['resume_data']['education'], $index, 1);
    }
    header('Location: education.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_education'])) {
        // Add new education entry to SESSION (not database yet!)
        $education = array(
            'degree' => $_POST['degree'] ?? '',
            'institution' => $_POST['institution'] ?? '',
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'description' => $_POST['description'] ?? ''
        );

        if (!isset($_SESSION['resume_data']['education'])) {
            $_SESSION['resume_data']['education'] = array();
        }
        $_SESSION['resume_data']['education'][] = $education;

        // Redirect to same page to clear form
        header('Location: education.php');
        exit();
    } elseif (isset($_POST['next'])) {
        // Proceed to next page
        header('Location: work-experience.php');
        exit();
    }
}

// Get existing education entries from SESSION
$education_list = $_SESSION['resume_data']['education'] ?? array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Education - CV Builder</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/cv-builder-styles.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar-nav.php'; ?>
    
    <div class="main-content">
        <div class="form-container">
            <!-- Display existing education entries -->
            <?php if (!empty($education_list)): ?>
                <div class="education-list">
                    <div class="section-title">Added Education Entries</div>
                    <?php foreach ($education_list as $index => $edu): ?>
                        <div class="education-entry">
                            <h4><?php echo htmlspecialchars($edu['degree']); ?> - <?php echo htmlspecialchars($edu['institution']); ?></h4>
                            <p><strong>Period:</strong> <?php echo htmlspecialchars($edu['start_date']); ?> to <?php echo htmlspecialchars($edu['end_date']); ?></p>
                            <?php if (!empty($edu['description'])): ?>
                                <p><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                            <?php endif; ?>
                            <a href="education.php?delete=<?php echo $index; ?>" onclick="return confirm('Are you sure you want to delete this entry?');">
                                <button type="button" class="delete-btn">Delete</button>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Form to add new education entry -->
            <div class="add-form">
                <div class="section-title">Add Education Entry</div>
                <form action="education.php" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="degree">Degree/Qualification</label>
                            <input type="text" id="degree" name="degree" required placeholder="e.g., Bachelor of Science in Computer Science">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="institution">Institution</label>
                            <input type="text" id="institution" name="institution" required placeholder="e.g., University of Example">
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
                            <label for="description">Description (Optional)</label>
                            <textarea id="description" name="description" placeholder="Achievements, honors, relevant coursework, etc."></textarea>
                        </div>
                    </div>

                    <div class="add-btn-container">
                        <button type="submit" name="add_education" class="add-btn">+ Add Education</button>
                    </div>
                </form>
            </div>

            <!-- Sticky Navigation Buttons -->
            <div class="btn-container">
                <button type="button" class="back-btn" onclick="window.location.href='career-objectives.php'">Back</button>
                <form action="education.php" method="post" style="display: inline;">
                    <button type="submit" name="next" class="next-btn">Next Step</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

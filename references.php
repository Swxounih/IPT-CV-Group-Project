<?php
session_start();
require_once 'config.php';

// Handle deleting a reference entry from SESSION
if (isset($_GET['delete'])) {
    $index = (int)$_GET['delete'];
    if (isset($_SESSION['resume_data']['references'][$index])) {
        array_splice($_SESSION['resume_data']['references'], $index, 1);
    }
    header('Location: references.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_reference'])) {
        // Add new reference entry to SESSION (not database yet!)
        $reference = array(
            'company_name' => $_POST['company_name'] ?? '',
            'contact_person' => $_POST['contact_person'] ?? '',
            'phone_number' => $_POST['phone_number'] ?? '',
            'email' => $_POST['email'] ?? ''
        );
        
        if (!isset($_SESSION['resume_data']['references'])) {
            $_SESSION['resume_data']['references'] = array();
        }
        $_SESSION['resume_data']['references'][] = $reference;
        
        header('Location: references.php');
        exit();
    } elseif (isset($_POST['submit'])) {
        header('Location: save_resume.php');
        exit();
    }
}

// Get existing references from SESSION
$references_list = $_SESSION['resume_data']['references'] ?? array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>References - CV Builder</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/cv-builder-styles.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar-nav.php'; ?>
    
    <div class="main-content">
        <div class="form-container">
            
            <!-- Display existing references -->
            <?php if (!empty($references_list)): ?>
                <div class="references-list">
                    <div class="section-title">Added References</div>
                    <?php foreach ($references_list as $index => $ref): ?>
                        <div class="reference-entry">
                            <h4><?php echo htmlspecialchars($ref['contact_person']); ?> - <?php echo htmlspecialchars($ref['company_name']); ?></h4>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($ref['phone_number']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($ref['email']); ?></p>
                            <a href="references.php?delete=<?php echo $index; ?>" onclick="return confirm('Are you sure you want to delete this reference?');">
                                <button type="button" class="delete-btn">Delete</button>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Form to add new reference -->
            <div class="add-form">
                <div class="section-title">Add Reference Entry</div>
                <form action="references.php" method="post">
                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label for="contact_person">Contact Person</label>
                            <input type="text" id="contact_person" name="contact_person" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input type="text" id="company_name" name="company_name" required>
                        </div>
                    </div>
                    
                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" id="phone_number" name="phone_number" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>

                    <div class="add-btn-container">
                        <button type="submit" name="add_reference" class="add-btn">+ Add Reference</button>
                    </div>
                </form>
            </div>
            
            <!-- Sticky Navigation Buttons -->
            <div class="btn-container">
                <button type="button" class="back-btn" onclick="window.location.href='interests.php'">Back</button>
                <form action="references.php" method="post" style="display: inline;">
                    <button type="submit" name="submit" class="preview-btn">Save All</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

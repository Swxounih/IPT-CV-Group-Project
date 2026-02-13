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
    <link rel="stylesheet" href="sidebar.css">
    <style>
        /* Page specific styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: Arial, sans-serif; 
            background: #e5e7eb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .main-content {
            position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    margin-left: 0;
    padding: 0;
    max-width: 750px;
    width: 100%;
        }
        
        .form-container {
            width: 100%;
            background: #ffffff;
            padding: 50px 60px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            max-height: calc(100vh - 80px);
            overflow-y: auto;
        }
        
        .form-content {
            display: flex;
            flex-direction: column;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }
        
        .form-row.two-cols {
            grid-template-columns: 1fr 1fr;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 7px;
            font-weight: 600;
        }
        
        input, textarea {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            background: white;
            color: #1f2937;
            transition: all 0.2s ease;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
            line-height: 1.6;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .add-form {
            
            padding-top: 30px;
            margin-top: 20px;
        }
        
        .section-title {
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .education-list {
            margin-bottom: 25px;
        }
        
        .education-entry {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            position: relative;
        }
        
        .education-entry h4 {
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .education-entry p {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        .education-entry p strong {
            color: #374151;
            font-weight: 600;
        }
        
        .delete-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .delete-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        
        .add-btn {
            background: #10b981;
            color: white;
            border: none;
            padding: 13px 35px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .add-btn:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }
        
        .btn-container {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
        }
        
        .next-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 13px 48px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .next-btn:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }
        
        /* Scrollbar styling */
        .form-container::-webkit-scrollbar {
            width: 7px;
        }
        
        .form-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        
        .form-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        
        .form-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        textarea::-webkit-scrollbar {
            width: 7px;
        }
        
        textarea::-webkit-scrollbar-track {
            background: #f9fafb;
            border-radius: 10px;
        }
        
        textarea::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }
        
        textarea::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        @media (max-width: 1200px) {
            .main-content {
                transform: translate(-50%, -50%);
                max-width: 90%;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .main-content {
                transform: translate(-50%, -50%);
                max-width: 100%;
            }
            
            .form-container {
                padding: 30px 25px;
            }
            
            .form-row.two-cols {
                grid-template-columns: 1fr;
            }
            
            .btn-container {
                flex-direction: column;
            }
            
            .next-btn,
            .add-btn {
                width: 100%;
            }
        }
    </style>
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
                <form action="education.php" method="post" class="form-content">
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

                    <div class="btn-container">
                        <button type="submit" name="add_education" class="add-btn">+ Add Education</button>
                    </div>
                </form>
            </div>

            <!-- Navigation buttons -->
            <form action="education.php" method="post">
                <div class="btn-container"  padding-top: 30px; margin-top: 30px;">
                    <button type="submit" name="next" class="next-btn">Next Step</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
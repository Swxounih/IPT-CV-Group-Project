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
        
        .skills-list {
            margin-bottom: 30px;
        }
        
        .skill-entry {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .skill-entry h4 {
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .skill-entry p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }
        
        .skill-info {
            flex: 1;
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
        }
        
        .delete-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
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
        
        input, select {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            color: #1f2937;
            transition: all 0.2s ease;
            font-family: Arial, sans-serif;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        select {
            cursor: pointer;
        }
        
        .btn-container {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
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
            
            .add-btn,
            .next-btn {
                width: 100%;
            }
            
            .skill-entry {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .delete-btn {
                margin-top: 10px;
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

                    <div class="btn-container">
                        <button type="submit" name="add_skill" class="add-btn">+ Add Skill</button>
                    </div>
                </form>
            </div>
            
            <!-- Navigation buttons -->
            <form action="skills.php" method="post">
                <div class="btn-container" padding-top: 30px; margin-top: 30px;">
                    <button type="submit" name="next" class="next-btn">Next Step</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
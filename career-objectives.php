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
    <title>Career Objectives - CV Builder</title>
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
            background: #ffffff;
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
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            min-height: 400px;
            display: flex;
            flex-direction: column;
        }
        
        .form-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            flex: 1;
        }
        
        label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        textarea {
            width: 100%;
            flex: 1;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            background: white;
            color: #1f2937;
            transition: all 0.2s ease;
            resize: none;
            min-height: 300px;
            line-height: 1.6;
        }
        
        textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn-container {
            display: flex;
            justify-content: flex-end;
            margin-top: auto;
            padding-top: 30px;
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
                min-height: 350px;
            }
            
            textarea {
                min-height: 200px;
            }
            
            .next-btn {
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
            <form action="career-objectives.php" method="post" class="form-content">
                <div class="form-group">
                    <label for="objective">Objective</label>
                    <textarea 
                        id="objective" 
                        name="objective" 
                        required><?php echo htmlspecialchars($objective); ?></textarea>
                </div>

                <div class="btn-container">
                    <button type="submit" class="next-btn">Next Step</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
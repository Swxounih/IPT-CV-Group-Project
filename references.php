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
            background: linear-gradient(135deg, #1ebbeb 0%, #3450ce 100%);
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
            min-height: 740px;
            background: #ffffff;
            padding: 50px 60px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            max-height: calc(100vh - 80px);
            overflow-y: auto;
        }
        
        .references-list {
            margin-bottom: 30px;
        }
        
        .reference-entry {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            position: relative;
        }
        
        .reference-entry h4 {
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .reference-entry p {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        .reference-entry p strong {
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
            margin-top: 10px;
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
            border-top: 2px solid #e5e7eb;
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
        
        input {
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
        
        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .add-btn {
            background: #10b981;
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
        
        .add-btn:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }
        
        .back-btn {
            background: #e5e7eb;
            color: #374151;
            border: none;
            padding: 13px 48px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            min-width: 222px;
        }
        
        .back-btn:hover {
            background: #d1d5db;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .preview-btn {
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
        
        .preview-btn:hover {
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
                flex-direction: column-reverse;
            }
            
            .add-btn,
            .back-btn,
            .preview-btn {
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

                    <div class="btn-container">
                        <button type="submit" name="add_reference" class="add-btn">+ Add Reference</button>
                    </div>
                </form>
            </div>
            
            <!-- Navigation buttons -->
            <form action="references.php" method="post">
                <div class="btn-container" padding-top: 30px; margin-top: 30px;>
                    <button type="button" class="back-btn" onclick="window.location.href='interests.php'">Back</button>
                    <button type="submit" name="submit" class="preview-btn">Preview Resume</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
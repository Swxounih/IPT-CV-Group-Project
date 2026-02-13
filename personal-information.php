<?php
session_start();
require_once 'config.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload
    $photo_path = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_path = $upload_dir . uniqid() . '.' . $file_extension;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
    }
    
    // Store in SESSION (not database yet!)
    $_SESSION['resume_data']['personal_info'] = array(
        'photo' => $photo_path,
        'given_name' => $_POST['given_name'] ?? '',
        'middle_name' => $_POST['middle_name'] ?? '',
        'surname' => $_POST['surname'] ?? '',
        'extension' => $_POST['extension'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'birthdate' => $_POST['birthdate'] ?? '',
        'birthplace' => $_POST['birthplace'] ?? '',
        'civil_status' => $_POST['civil_status'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'address' => $_POST['address'] ?? '',
        'website' => $_POST['website'] ?? ''
    );
    
    header('Location: career-objectives.php');
    exit();
}

// Get existing data from session
$data = $_SESSION['resume_data']['personal_info'] ?? array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Information - CV Builder</title>
    <link rel="stylesheet" href="sidebar.css">
    <style>
        /* Page specific styles */
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .form-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h3 { 
            color: #2c3e50; 
            margin-bottom: 10px;
            font-size: 24px;
        }
        
        .form-subtitle {
            color: #7f8c8d;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        label { 
            display: block; 
            margin-top: 15px; 
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        
        input, select, textarea { 
            width: 100%; 
            padding: 10px; 
            margin-top: 5px; 
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #1abc9c;
        }
        
        input[type="submit"], button { 
            padding: 12px 25px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        input[type="submit"] {
            background-color: #1abc9c;
            color: white;
        }
        
        input[type="submit"]:hover {
            background-color: #16a085;
        }
        
        button {
            background-color: #95a5a6;
            color: white;
        }
        
        button:hover {
            background-color: #7f8c8d;
        }
        
        .btn-container { 
            display: flex; 
            gap: 10px;
            margin-top: 30px;
            justify-content: flex-end;
        }
        
        .info-note { 
            background: #fff3cd; 
            padding: 15px; 
            border-left: 4px solid #ffc107; 
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .section-divider {
            margin: 30px 0 20px 0;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
        }
        
        .section-title {
            color: #2c3e50;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Dito yung sidebar -->
    <?php include 'sidebar-nav.php'; ?>
    
    <div class="main-content">
        <div class="form-container">
            <div class="info-note">
                ℹ️ <strong>Note:</strong> Your data will be saved to the database only after you complete all steps and click "Submit" on the final page.
            </div>
            
            <form action="personal-information.php" method="post" enctype="multipart/form-data">
                <h3>Personal Information</h3>
                <p class="form-subtitle">Step 1 of 8 - Let's start with your basic information</p>
                
                <label for="photo">Profile Photo:</label>
                <input type="file" id="photo" name="photo" accept="image/*">
                
                <label for="given_name">Given Name: <span style="color: red;">*</span></label>
                <input type="text" id="given_name" name="given_name" value="<?php echo htmlspecialchars($data['given_name'] ?? ''); ?>" required placeholder="Enter your first name">
                
                <label for="middle_name">Middle Name:</label>
                <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($data['middle_name'] ?? ''); ?>" placeholder="Enter your middle name (optional)">
                
                <label for="surname">Surname: <span style="color: red;">*</span></label>
                <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($data['surname'] ?? ''); ?>" required placeholder="Enter your last name">
                
                <label for="extension">Extension:</label>
                <input type="text" id="extension" name="extension" value="<?php echo htmlspecialchars($data['extension'] ?? ''); ?>" placeholder="Jr., Sr., III, etc.">
                
                <label for="gender">Gender: <span style="color: red;">*</span></label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male" <?php echo ($data['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo ($data['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                </select>
                
                <label for="birthdate">Date of Birth: <span style="color: red;">*</span></label>
                <input type="date" name="birthdate" id="birthdate" value="<?php echo htmlspecialchars($data['birthdate'] ?? ''); ?>" required>
                
                <label for="birthplace">Place of Birth: <span style="color: red;">*</span></label>
                <input type="text" name="birthplace" id="birthplace" value="<?php echo htmlspecialchars($data['birthplace'] ?? ''); ?>" placeholder="City, Country" required>
                
                <label for="civil_status">Civil Status: <span style="color: red;">*</span></label>
                <select id="civil_status" name="civil_status" required>
                    <option value="">Select Status</option>
                    <option value="single" <?php echo ($data['civil_status'] ?? '') === 'single' ? 'selected' : ''; ?>>Single</option>
                    <option value="married" <?php echo ($data['civil_status'] ?? '') === 'married' ? 'selected' : ''; ?>>Married</option>
                    <option value="divorced" <?php echo ($data['civil_status'] ?? '') === 'divorced' ? 'selected' : ''; ?>>Divorced</option>
                    <option value="widowed" <?php echo ($data['civil_status'] ?? '') === 'widowed' ? 'selected' : ''; ?>>Widowed</option>
                </select>

                <div class="section-divider">
                    <div class="section-title">Contact Information</div>
                </div>
                
                <label for="email">Email Address: <span style="color: red;">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" required placeholder="your.email@example.com">
                
                <label for="phone">Phone Number: <span style="color: red;">*</span></label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>" required placeholder="+1 234 567 8900">
                
                <label for="address">Complete Address: <span style="color: red;">*</span></label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($data['address'] ?? ''); ?>" required placeholder="Street, City, State, ZIP">
                
                <label for="website">Website / Portfolio:</label>
                <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($data['website'] ?? ''); ?>" placeholder="https://yourwebsite.com">

                <div class="btn-container">
                    <button type="button" onclick="window.location.href='search-create.php'">Cancel</button>
                    <input type="submit" value="Next Step →">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
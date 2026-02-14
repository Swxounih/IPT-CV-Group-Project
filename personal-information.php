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
        'nationality' => $_POST['nationality'] ?? '',
        'driving_license' => $_POST['driving_license'] ?? ''
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
            padding: 30px 50px;
            border-radius: 15px;
            box-shadow:0 10px 40px rgba(0,0,0,0.15);
            min-height: 650px;
        }
        
        .photo-section {
            display: flex;
            gap: 35px;
            margin-bottom: 30px;
        }
        
        .photo-upload {
            flex-shrink: 0;
        }
        
        .photo-preview {
            width: 140px;
            height: 140px;
            background: #f9fafb;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .photo-preview:hover {
            border-color: #9ca3af;
        }
        
        .photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        
        .photo-preview.has-image img {
            display: block;
        }
        
        .photo-preview.has-image .photo-placeholder {
            display: none;
        }
        
        .photo-placeholder {
            text-align: center;
            color: #9ca3af;
        }
        
        .camera-icon {
            width: 50px;
            height: 50px;
            background: #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 24px;
            color: #6b7280;
        }
        
        .photo-text {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        
        #photo {
            display: none;
        }
        
        .form-fields {
            flex-grow: 1;
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
        
        .form-row.name-cols {
            grid-template-columns: 2fr 1fr;
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
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 35px;
        }
        
        .btn-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .page-title {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 25px;
    padding-bottom: 15px;
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
            
            .photo-section {
                flex-direction: column;
                align-items: center;
            }
            
            .form-row.two-cols,
            .form-row.name-cols {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    
    <!-- Sidebar -->
    <?php include 'sidebar-nav.php'; ?>
    
    <div class="main-content">
        <div class="form-container">
            <h2 class="page-title">Personal Information</h2>
            <form action="personal-information.php" method="post" enctype="multipart/form-data">
                <div class="photo-section">
                    <div class="photo-upload">
                        <label for="photo" class="photo-preview" id="photoPreview">
                            <img id="previewImage" src="<?php echo htmlspecialchars($data['photo'] ?? ''); ?>" alt="Preview">
                            <div class="photo-placeholder">
                                <div class="photo-text">Add photo</div>
                            </div>
                        </label>
                        <input type="file" id="photo" name="photo" accept="image/*">
                    </div>
                    
                    <div class="form-fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="surname">Surname</label>
                                <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($data['surname'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row name-cols">
                            <div class="form-group">
                                <label for="given_name">First Name</label>
                                <input type="text" id="given_name" name="given_name" value="<?php echo htmlspecialchars($data['given_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="extension">Ext. Name</label>
                                <input type="text" id="extension" name="extension" value="<?php echo htmlspecialchars($data['extension'] ?? ''); ?>" placeholder="Jr., Sr., III">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="middle_name">Middle Name</label>
                                <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($data['middle_name'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-row two-cols">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">SELECT</option>
                            <option value="male" <?php echo ($data['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($data['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="birthdate">Date of Birth</label>
                        <input type="date" name="birthdate" id="birthdate" value="<?php echo htmlspecialchars($data['birthdate'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-row two-cols">
                    <div class="form-group">
                        <label for="birthplace">Place of Birth</label>
                        <input type="text" name="birthplace" id="birthplace" value="<?php echo htmlspecialchars($data['birthplace'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="civil_status">Marital Status</label>
                        <select id="civil_status" name="civil_status" required>
                            <option value="">SELECT</option>
                            <option value="single" <?php echo ($data['civil_status'] ?? '') === 'single' ? 'selected' : ''; ?>>Single</option>
                            <option value="married" <?php echo ($data['civil_status'] ?? '') === 'married' ? 'selected' : ''; ?>>Married</option>
                            <option value="divorced" <?php echo ($data['civil_status'] ?? '') === 'divorced' ? 'selected' : ''; ?>>Divorced</option>
                            <option value="widowed" <?php echo ($data['civil_status'] ?? '') === 'widowed' ? 'selected' : ''; ?>>Widowed</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row two-cols">
                    <div class="form-group">
                        <label for="email">E-mail Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nationality">Nationality</label>
                        <input type="text" id="nationality" name="nationality" value="<?php echo htmlspecialchars($data['nationality'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row two-cols">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="driving_license">Driving License</label>
                        <input type="text" id="driving_license" name="driving_license" value="<?php echo htmlspecialchars($data['driving_license'] ?? ''); ?>">
                    </div>
                </div>

                <div class="btn-container">
                    <button type="submit" class="next-btn">Next Step</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Photo preview functionality
        const photoInput = document.getElementById('photo');
        const photoPreview = document.getElementById('photoPreview');
        const previewImage = document.getElementById('previewImage');
        
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    photoPreview.classList.add('has-image');
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Check if there's already an image
        if (previewImage.src && previewImage.src !== window.location.href) {
            photoPreview.classList.add('has-image');
        }
    </script>
</body>
</html>
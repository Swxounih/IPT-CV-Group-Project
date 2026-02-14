<?php
session_start();
require_once 'config.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload with enhanced security validation
    $photo_path = $_SESSION['resume_data']['personal_info']['photo'] ?? ''; // Keep existing photo if no new upload
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        // Security: Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_type = $_FILES['photo']['type'];
        $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        
        // Security: Validate file size (max 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB in bytes
        
        // Security: Validate actual file content
        $image_info = getimagesize($_FILES['photo']['tmp_name']);
        
        if (in_array($file_type, $allowed_types) && 
            in_array($file_extension, $allowed_extensions) &&
            $_FILES['photo']['size'] <= $max_size &&
            $image_info !== false) {
            
            $upload_dir = 'uploads/photos/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true); // More secure permissions
            }
            
            // Delete old photo if exists
            if (!empty($photo_path) && file_exists($photo_path)) {
                unlink($photo_path);
            }
            
            // Generate unique filename
            $photo_path = $upload_dir . uniqid('photo_', true) . '.' . $file_extension;
            move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
        } else {
            echo "<script>alert('Invalid file. Please upload a valid image (JPG, PNG, GIF, WEBP) under 5MB.');</script>";
        }
    }
    
    // Validate and sanitize inputs
    $given_name = trim($_POST['given_name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $phone = preg_replace('/[^0-9+\-() ]/', '', $_POST['phone'] ?? '');
    
    if (empty($given_name) || empty($surname) || !$email) {
        echo "<script>alert('Please fill in all required fields correctly.');</script>";
    } else {
        // Store in SESSION (not database yet!)
        $_SESSION['resume_data']['personal_info'] = array(
            'photo' => $photo_path,
            'given_name' => $given_name,
            'middle_name' => trim($_POST['middle_name'] ?? ''),
            'surname' => $surname,
            'extension' => trim($_POST['extension'] ?? ''),
            'gender' => $_POST['gender'] ?? '',
            'birthdate' => $_POST['birthdate'] ?? '',
            'birthplace' => trim($_POST['birthplace'] ?? ''),
            'civil_status' => $_POST['civil_status'] ?? '',
            'email' => $email,
            'phone' => $phone,
            'address' => trim($_POST['address'] ?? ''),
            'website' => filter_var($_POST['website'] ?? '', FILTER_VALIDATE_URL) ?: ''
        );
        
        header('Location: career-objectives.php');
        exit();
    }
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
            background: linear-gradient(to bottom, #1e5bb8 50px, #ffffff 50px, #ffffff 100%);
            min-height: 100vh;
            padding: 0;
        }
        
        .header {
            background: #1e5bb8;
            color: white;
            padding: 15px 30px;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
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
            max-width: 850px;
            width: 100%;
            background: #ffffff;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        
        .photo-section {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .photo-upload {
            flex-shrink: 0;
        }
        
        .photo-preview {
            width: 150px;
            height: 150px;
            background: white;
            border: 2px solid #999;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
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
            color: #999;
        }
        
        .camera-icon {
            width: 60px;
            height: 60px;
            background: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 30px;
            color: #999;
        }
        
        .photo-text {
            font-size: 13px;
            color: #999;
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
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-row.two-cols {
            grid-template-columns: 2fr 1fr;
        }
        
        .form-row.three-cols {
            grid-template-columns: repeat(3, 1fr);
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            font-size: 11px;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #a0aec0;
            border-radius: 4px;
            font-size: 14px;
            background: white;
            color: #2d3748;
            font-family: Arial, sans-serif;
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #1e5bb8;
            box-shadow: 0 0 0 2px rgba(30, 91, 184, 0.1);
        }
        
        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%234a5568' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 30px;
        }
        
        .btn-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }
        
        .next-btn {
            background: #1e5bb8;
            color: white;
            border: none;
            padding: 12px 40px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .next-btn:hover {
            background: #164a9a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 91, 184, 0.3);
        }
        
        @media (max-width: 768px) {
            .photo-section {
                flex-direction: column;
            }
            
            .form-row.two-cols,
            .form-row.three-cols {
                grid-template-columns: 1fr;
            }
            
            .form-container {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    
    <!-- Sidebar -->
    <?php include 'sidebar-nav.php'; ?>
    
    <div class="main-content">
        <div class="form-container">
            <form action="personal-information.php" method="post" enctype="multipart/form-data">
                <div class="photo-section">
                    <div class="photo-upload">
                        <label for="photo" class="photo-preview" id="photoPreview">
                            <img id="previewImage" src="<?php echo htmlspecialchars($data['photo'] ?? ''); ?>" alt="Preview">
                            <div class="photo-placeholder">
                                <div class="camera-icon">📷</div>
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
                        
                        <div class="form-row two-cols">
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
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="address">Complete Address</label>
                        <textarea id="address" name="address" rows="2" placeholder="Street, Barangay, City, Province"><?php echo htmlspecialchars($data['address'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="website">Website (Optional)</label>
                        <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($data['website'] ?? ''); ?>" placeholder="https://your-portfolio.com">
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
        
        // Auto-save functionality
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                // Save to localStorage as backup
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });
                localStorage.setItem('cv_draft_personal_info', JSON.stringify(data));
            });
        });
        
        // Load draft on page load if exists
        window.addEventListener('load', function() {
            const draft = localStorage.getItem('cv_draft_personal_info');
            if (draft && confirm('Would you like to restore your previous draft?')) {
                const data = JSON.parse(draft);
                Object.keys(data).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input && input.type !== 'file') {
                        input.value = data[key];
                    }
                });
            }
        });
    </script>
</body>
</html>
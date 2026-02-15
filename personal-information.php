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

        if (
            in_array($file_type, $allowed_types) &&
            in_array($file_extension, $allowed_extensions) &&
            $_FILES['photo']['size'] <= $max_size &&
            $image_info !== false
        ) {

            $upload_dir = 'uploads/photos/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true); // More secure permissions
            }

            // Delete old photo if exists
            if (!empty($photo_path) && file_exists('uploads/photos/' . $photo_path)) {
                unlink('uploads/photos/' . $photo_path);
            }

            // Generate unique filename (store only filename, not full path)
            $filename = uniqid('photo_', true) . '.' . $file_extension;
            $photo_path = $filename;
            move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $filename);
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
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/cv-builder-styles.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar-nav.php'; ?>

    <div class="main-content">
        <div class="form-container">
            <h2 class="page-title">Personal Information</h2>
            <form action="personal-information.php" method="post" enctype="multipart/form-data">
                <div class="form-content-wrapper">
                <div class="photo-section">
                    <div class="photo-upload">
                        <label for="photo" class="photo-preview" id="photoPreview">
                            <img id="previewImage" src="<?php echo !empty($data['photo']) ? 'uploads/photos/' . htmlspecialchars($data['photo']) : ''; ?>" alt="Preview">
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
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($data['address'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="website">Website (Optional)</label>
                        <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($data['website'] ?? ''); ?>" placeholder="https://your-portfolio.com">
                    </div>
                </div>
                </div>

                <!-- Sticky Navigation Buttons -->
                <div class="btn-container">
                    <button type="button" class="back-btn" onclick="window.location.href='index.php'">Back To Home</button>
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
                };
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

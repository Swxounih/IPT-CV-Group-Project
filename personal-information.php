<?php
session_start();
require_once 'config.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
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
    
    // Prepare data
    $given_name = $conn->real_escape_string($_POST['given_name'] ?? '');
    $middle_name = $conn->real_escape_string($_POST['middle_name'] ?? '');
    $surname = $conn->real_escape_string($_POST['surname'] ?? '');
    $extension = $conn->real_escape_string($_POST['extension'] ?? '');
    $gender = $conn->real_escape_string($_POST['gender'] ?? '');
    $birthdate = $conn->real_escape_string($_POST['birthdate'] ?? '');
    $birthplace = $conn->real_escape_string($_POST['birthplace'] ?? '');
    $civil_status = $conn->real_escape_string($_POST['civil_status'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    $website = $conn->real_escape_string($_POST['website'] ?? '');
    
    // Insert into database
    $sql = "INSERT INTO personal_information (photo, given_name, middle_name, surname, extension, gender, birthdate, birthplace, civil_status, email, phone, address, website) 
            VALUES ('$photo_path', '$given_name', '$middle_name', '$surname', '$extension', '$gender', '$birthdate', '$birthplace', '$civil_status', '$email', '$phone', '$address', '$website')";
    
    if ($conn->query($sql) === TRUE) {
        $personal_info_id = $conn->insert_id;
        
        // Store in session
        $_SESSION['resume_data']['personal_info_id'] = $personal_info_id;
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
        
        closeDBConnection($conn);
        header('Location: career-objectives.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        closeDBConnection($conn);
    }
}

// Get existing data
$data = $_SESSION['resume_data']['personal_info'] ?? array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Information</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        input[type="submit"], button { width: auto; padding: 10px 20px; margin-top: 20px; cursor: pointer; }
        h3 { color: #333; }
        .btn-container { display: flex; gap: 10px; }
    </style>
</head>
<body>
    <form action="personal-information.php" method="post" enctype="multipart/form-data">
        <h3>Personal Information</h3>
        
        <label for="photo">Photo:</label>
        <input type="file" id="photo" name="photo" accept="image/*">
        
        <label for="given_name">Given Name:</label>
        <input type="text" id="given_name" name="given_name" value="<?php echo htmlspecialchars($data['given_name'] ?? ''); ?>" required>
        
        <label for="middle_name">Middle Name:</label>
        <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($data['middle_name'] ?? ''); ?>">
        
        <label for="surname">Surname:</label>
        <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($data['surname'] ?? ''); ?>" required>
        
        <label for="extension">Extension:</label>
        <input type="text" id="extension" name="extension" value="<?php echo htmlspecialchars($data['extension'] ?? ''); ?>" placeholder="Jr., Sr., III, etc.">
        
        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="male" <?php echo ($data['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?php echo ($data['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
        </select>
        
        <label for="birthdate">Date of Birth:</label>
        <input type="date" name="birthdate" id="birthdate" value="<?php echo htmlspecialchars($data['birthdate'] ?? ''); ?>" required>
        
        <label for="birthplace">Place of Birth:</label>
        <input type="text" name="birthplace" id="birthplace" value="<?php echo htmlspecialchars($data['birthplace'] ?? ''); ?>" placeholder="Place of Birth" required>
        
        <label for="civil_status">Civil Status:</label>
        <select id="civil_status" name="civil_status" required>
            <option value="">Select Status</option>
            <option value="single" <?php echo ($data['civil_status'] ?? '') === 'single' ? 'selected' : ''; ?>>Single</option>
            <option value="married" <?php echo ($data['civil_status'] ?? '') === 'married' ? 'selected' : ''; ?>>Married</option>
            <option value="divorced" <?php echo ($data['civil_status'] ?? '') === 'divorced' ? 'selected' : ''; ?>>Divorced</option>
            <option value="widowed" <?php echo ($data['civil_status'] ?? '') === 'widowed' ? 'selected' : ''; ?>>Widowed</option>
        </select>

        <h4>Contact Information</h4>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" required>
        
        <label for="phone">Phone:</label>
        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>" required>
        
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($data['address'] ?? ''); ?>" required>
        
        <label for="website">Website:</label>
        <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($data['website'] ?? ''); ?>" placeholder="https://example.com">

        <div class="btn-container">
            <button type="button" onclick="window.location.href='search-create.php'">Back</button>
            <input type="submit" value="Next">
        </div>
    </form>
</body>
</html>
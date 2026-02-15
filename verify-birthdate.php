<?php
session_start();
require_once 'config.php';

// Get resume ID from URL
$resume_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($resume_id <= 0) {
    header('Location: search-create.php');
    exit();
}

// Get the birthdate for this resume
$conn = getDBConnection();
$sql = "SELECT given_name, surname, birthdate, user_id FROM personal_information WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resume_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    closeDBConnection($conn);
    echo "<script>alert('Resume not found'); window.location.href='search-create.php';</script>";
    exit();
}

$user_data = $result->fetch_assoc();
$stmt->close();
closeDBConnection($conn);

// Process verification
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_birthdate = $_POST['birthdate'] ?? '';
    
    if ($entered_birthdate === $user_data['birthdate']) {
        // Verification successful - use user_id (email) from database, not resume_id
        $_SESSION['verified_user_id'] = $user_data['user_id'];
        $_SESSION['user_name'] = $user_data['given_name'] . ' ' . $user_data['surname'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error_message = 'Incorrect birthdate. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Identity - CV Builder</title>
    <style>
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
        
        .verify-container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
        }
        
        .verify-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .verify-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .verify-header p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .user-info h2 {
            color: #667eea;
            font-size: 22px;
            margin-bottom: 5px;
        }
        
        .user-info p {
            color: #666;
            font-size: 13px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        input[type="date"] {
            width: 100%;
            padding: 14px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        input[type="date"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        
        .btn-container {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-verify {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-cancel {
            background: #e1e8ed;
            color: #666;
        }
        
        .btn-cancel:hover {
            background: #cbd5e0;
        }
        
        .security-note {
            margin-top: 25px;
            padding: 15px;
            background: #e8f4fd;
            border-left: 4px solid #3b82f6;
            border-radius: 5px;
        }
        
        .security-note p {
            color: #1e40af;
            font-size: 13px;
            line-height: 1.5;
        }
        
        @media (max-width: 768px) {
            .verify-container {
                padding: 35px 25px;
            }
            
            .btn-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-header">
            <h1>Verify Your Identity</h1>
            <!-- <p>Please enter your birthdate to access your account</p> -->
        </div>
        
        <div class="user-info">
            <h2><?php echo htmlspecialchars($user_data['given_name'] . ' ' . $user_data['surname']); ?></h2>
            <p>Account Verification</p>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="birthdate">Enter Your Birthdate</label>
                <input type="date" id="birthdate" name="birthdate" required>
            </div>
            
            <div class="btn-container">
                <button type="button" class="btn btn-cancel" onclick="window.location.href='search-create.php'">Cancel</button>
                <button type="submit" class="btn btn-verify">Verify & Continue</button>
            </div>
        </form>
        
        <div class="security-note">
            <p>🛡️ <strong>Security Notice:</strong> We use your birthdate to verify your identity and protect your personal information.</p>
        </div>
    </div>
</body>
</html>

<?php
session_start();
require_once 'config.php';

// Check if personal info exists
if (!isset($_SESSION['resume_data']['personal_info_id'])) {
    header('Location: personal-information.php');
    exit();
}

// Handle deleting a reference entry
if (isset($_GET['delete'])) {
    $conn = getDBConnection();
    $id = (int)$_GET['delete'];
    
    $sql = "DELETE FROM reference WHERE id = $id AND personal_info_id = " . $_SESSION['resume_data']['personal_info_id'];
    $conn->query($sql);
    
    closeDBConnection($conn);
    header('Location: references.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    $personal_info_id = $_SESSION['resume_data']['personal_info_id'];
    
    if (isset($_POST['add_reference'])) {
        // Add new reference entry
        $company_name = $conn->real_escape_string($_POST['company_name'] ?? '');
        $contact_person = $conn->real_escape_string($_POST['contact_person'] ?? '');
        $phone_number = $conn->real_escape_string($_POST['phone_number'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        
        $sql = "INSERT INTO reference (personal_info_id, company_name, contact_person, phone_number, email) 
                VALUES ('$personal_info_id', '$company_name', '$contact_person', '$phone_number', '$email')";
        
        if ($conn->query($sql) === TRUE) {
            closeDBConnection($conn);
            header('Location: references.php');
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['submit'])) {
        closeDBConnection($conn);
        header('Location: preview.php');
        exit();
    }
    
    closeDBConnection($conn);
}

// Get existing references from database
$conn = getDBConnection();
$personal_info_id = $_SESSION['resume_data']['personal_info_id'];
$sql = "SELECT * FROM reference WHERE personal_info_id = $personal_info_id";
$result = $conn->query($sql);
$references_list = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $references_list[] = $row;
    }
}
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>References</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 50px auto; padding: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button, input[type="submit"] { padding: 10px 20px; margin-top: 20px; cursor: pointer; }
        h3 { color: #333; }
        .btn-container { display: flex; gap: 10px; margin-top: 20px; }
        .reference-entry { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .reference-entry h4 { margin-top: 0; color: #555; }
        .delete-btn { background: #ff4444; color: white; border: none; padding: 5px 10px; border-radius: 3px; }
        .add-form { border: 2px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <h3>References</h3>
    
    <!-- Display existing references -->
    <?php if (!empty($references_list)): ?>
        <div style="margin-bottom: 30px;">
            <h4>Added References:</h4>
            <?php foreach ($references_list as $ref): ?>
                <div class="reference-entry">
                    <h4><?php echo htmlspecialchars($ref['contact_person']); ?> - <?php echo htmlspecialchars($ref['company_name']); ?></h4>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($ref['phone_number']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($ref['email']); ?></p>
                    <a href="references.php?delete=<?php echo $ref['id']; ?>" onclick="return confirm('Are you sure you want to delete this reference?');">
                        <button type="button" class="delete-btn">Delete</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Form to add new reference -->
    <div class="add-form">
        <h4>Add Reference Entry</h4>
        <form action="references.php" method="post">
            <label for="company_name">Company Name:</label>
            <input type="text" name="company_name" id="company_name" required>
            
            <label for="contact_person">Contact Person:</label>
            <input type="text" name="contact_person" id="contact_person" required>
            
            <label for="phone_number">Phone Number:</label>
            <input type="tel" name="phone_number" id="phone_number" required>
            
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <button type="submit" name="add_reference">Add Reference</button>
        </form>
    </div>
    
    <!-- Navigation buttons -->
    <form action="references.php" method="post">
        <div class="btn-container">
            <button type="button" onclick="window.location.href='interests.php'">Back</button>
            <input type="submit" name="submit" value="Preview Resume">
        </div>
    </form>
</body>
</html>
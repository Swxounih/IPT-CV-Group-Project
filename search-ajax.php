<?php
require_once 'config.php';

header('Content-Type: application/json');

$searchTerm = $_GET['term'] ?? '';

if (empty($searchTerm)) {
    echo json_encode(['success' => false, 'message' => 'No search term provided']);
    exit();
}

$conn = getDBConnection();
$searchTerm = $conn->real_escape_string($searchTerm);

// Search in personal_information table
$sql = "SELECT 
            id,
            CONCAT(given_name, ' ', IFNULL(middle_name, ''), ' ', surname, ' ', IFNULL(extension, '')) as name,
            email,
            phone,
            address
        FROM personal_information 
        WHERE 
            given_name LIKE '%$searchTerm%' OR 
            middle_name LIKE '%$searchTerm%' OR 
            surname LIKE '%$searchTerm%' OR 
            email LIKE '%$searchTerm%' OR
            phone LIKE '%$searchTerm%' OR
            address LIKE '%$searchTerm%'
        ORDER BY given_name, surname
        LIMIT 20";

$result = $conn->query($sql);
$results = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $results[] = array(
            'id' => $row['id'],
            'name' => trim($row['name']),
            'email' => $row['email'],
            'phone' => $row['phone'],
            'address' => $row['address']
        );
    }
}

closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'results' => $results,
    'count' => count($results)
]);
?>
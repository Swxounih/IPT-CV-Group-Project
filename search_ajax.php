<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Get search term
$searchTerm = $_GET['term'] ?? '';

if (empty($searchTerm)) {
    echo json_encode(['success' => false, 'message' => 'No search term provided']);
    exit();
}

$conn = getDBConnection();

// Prepare search query - search in personal_information table
// Also search in skills table for skill-based searches
$searchTerm = $conn->real_escape_string($searchTerm);
$searchPattern = "%{$searchTerm}%";

$sql = "SELECT DISTINCT pi.id, pi.given_name, pi.middle_name, pi.surname, pi.email, pi.phone, pi.address 
    FROM personal_information pi
    LEFT JOIN skills s ON pi.id = s.personal_info_id
    WHERE pi.cv_title = 'My Resume' AND (
        pi.given_name LIKE ? 
        OR pi.middle_name LIKE ? 
        OR pi.surname LIKE ? 
        OR pi.email LIKE ? 
        OR pi.phone LIKE ? 
        OR pi.address LIKE ?
        OR s.skill_name LIKE ?
    )
    ORDER BY pi.surname, pi.given_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $searchPattern, $searchPattern, $searchPattern, $searchPattern, $searchPattern, $searchPattern, $searchPattern);
$stmt->execute();
$result = $stmt->get_result();

$results = array();
while ($row = $result->fetch_assoc()) {
    $fullName = trim($row['given_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['surname']);
    $results[] = array(
        'id' => $row['id'],
        'name' => $fullName,
        'email' => $row['email'] ?? '',
        'phone' => $row['phone'] ?? '',
        'address' => $row['address'] ?? ''
    );
}

$stmt->close();
closeDBConnection($conn);

if (count($results) > 0) {
    echo json_encode(['success' => true, 'results' => $results]);
} else {
    echo json_encode(['success' => false, 'message' => 'No results found']);
}
?>

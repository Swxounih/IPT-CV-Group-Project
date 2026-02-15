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

// Prepare search query - PRIVACY FOCUSED: Only return id, name, and birthplace
// Search in personal_information and skills tables
$searchTerm = $conn->real_escape_string($searchTerm);
$searchPattern = "%{$searchTerm}%";

// Updated query to only select id, name fields, and birthplace
$sql = "SELECT DISTINCT 
            pi.id, 
            pi.given_name, 
            pi.middle_name, 
            pi.surname, 
            pi.extension,
            pi.birthplace
        FROM personal_information pi
        LEFT JOIN skills s ON pi.id = s.personal_info_id
        WHERE pi.cv_title = 'My Resume' AND (
            pi.given_name LIKE ? 
            OR pi.middle_name LIKE ? 
            OR pi.surname LIKE ? 
            OR pi.birthplace LIKE ?
            OR CONCAT(pi.given_name, ' ', pi.surname) LIKE ?
            OR s.skill_name LIKE ?
        )
        ORDER BY pi.surname, pi.given_name
        LIMIT 50";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", 
    $searchPattern, 
    $searchPattern, 
    $searchPattern, 
    $searchPattern,
    $searchPattern,
    $searchPattern
);
$stmt->execute();
$result = $stmt->get_result();

$results = array();
while ($row = $result->fetch_assoc()) {
    // Construct full name with proper spacing
    $nameParts = array_filter([
        $row['given_name'] ?? '',
        $row['middle_name'] ?? '',
        $row['surname'] ?? '',
        $row['extension'] ?? ''
    ]);
    
    $fullName = trim(implode(' ', $nameParts));
    
    // Privacy-focused response - only name and birthplace
    $results[] = array(
        'id' => $row['id'],
        'name' => $fullName,
        'birthplace' => $row['birthplace'] ?? 'Not specified'
    );
}

$stmt->close();
closeDBConnection($conn);

if (count($results) > 0) {
    echo json_encode([
        'success' => true, 
        'results' => $results,
        'count' => count($results),
        'privacy_notice' => 'For privacy protection, only name and birthplace are displayed'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'No results found',
        'results' => []
    ]);
}
?>
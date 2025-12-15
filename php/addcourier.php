<?php
require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

// Validate required fields
if (!isset($_POST['courierName']) || !isset($_POST['phoneNumber']) || 
    !isset($_POST['deliveryHub'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$courierName = $_POST['courierName'];
$phoneNumber = $_POST['phoneNumber']; 
$deliveryHub = $_POST['deliveryHub'];

// Connect to MongoDB
$conn = new MongoDB\Client("mongodb://localhost:27017");
$collection = $conn->logistics->couriers;

// Check if courier with same name and phone number already exists
$existingCourier = $collection->findOne([
    'courierName' => $courierName,
    'phoneNumber' => $phoneNumber
]);

if ($existingCourier) {
    http_response_code(409); // Conflict status code
    echo json_encode([
        'success' => false,
        'error' => 'Courier with this name and phone number already exists'
    ]);
    exit;
}

// Prepare courier document
$courierDocument = [
    'courierName' => $courierName,
    'phoneNumber' => $phoneNumber,
    'deliveryHub' => $deliveryHub
];

// Only add rating if it's provided
if (isset($_POST['rating']) && $_POST['rating'] !== '') {
    $courierDocument['rating'] = (int)$_POST['rating'];
}

// Insert courier
$result = $collection->insertOne($courierDocument);

if ($result->getInsertedCount() > 0) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to add courier']);
}
?>
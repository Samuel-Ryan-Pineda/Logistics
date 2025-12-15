<?php
require '../vendor/autoload.php';

header('Content-Type: application/json');

// Validate required fields
if (!isset($_POST['customerName']) || !isset($_POST['phoneNumber']) || !isset($_POST['address'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$customerName = $_POST['customerName'];
$phoneNumber = '+63' . $_POST['phoneNumber'];
$address = $_POST['address'];

$conn = new MongoDB\Client("mongodb://localhost:27017");
$db = $conn->logistics;
$collection = $db->customers;

// Check if customer with same name and phone number already exists
$existingCustomer = $collection->findOne([
    'name' => $customerName,
    'phone' => $phoneNumber
]);

if ($existingCustomer) {
    http_response_code(409); // Conflict status code
    echo json_encode([
        'success' => false,
        'error' => 'Customer with this name and phone number already exists'
    ]);
    exit;
}

// Insert customer
$result = $collection->insertOne([
    "name" => $customerName,
    "phone" => $phoneNumber,
    "address" => $address
]);

if ($result->getInsertedCount() > 0) {
    echo json_encode([
        'success' => true, 
        'message' => 'Customer has been successfully added.',
        'customerId' => (string)$result->getInsertedId()
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to add customer'
    ]);
}
?>

<?php
require '../vendor/autoload.php';

header('Content-Type: application/json');

// Connect to MongoDB
$conn = new MongoDB\Client("mongodb://localhost:27017");
$db = $conn->logistics;
$collection = $db->customers;

// Fetch all customers
$customers = $collection->find();
$customersList = [];

// Convert cursor to array
foreach ($customers as $customer) {
    $customersList[] = [
        'name' => $customer['name'],
        'phone' => $customer['phone'],
        'address' => $customer['address']
    ];
}

// Return JSON response
echo json_encode(['success' => true, 'customers' => $customersList]);
?>

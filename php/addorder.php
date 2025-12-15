<?php
require '../vendor/autoload.php';

header('Content-Type: application/json');

// Connect to MongoDB
$conn = new MongoDB\Client("mongodb://localhost:27017");
$db = $conn->logistics;
$ordersCollection = $db->orders;

// Validate required fields
if (!isset($_POST['customerName']) || !isset($_POST['customerPhone']) || !isset($_POST['customerAddress']) ||
    !isset($_POST['item']) || !isset($_POST['quantity']) || !isset($_POST['amount']) ||
    !isset($_POST['weight']) || !isset($_POST['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Get form data
$customerName = $_POST['customerName'];
$customerPhone = $_POST['customerPhone'];
$customerAddress = $_POST['customerAddress'];
$item = $_POST['item'];
$quantity = $_POST['quantity'];
$amount = $_POST['amount'];
$weight = $_POST['weight'];
$status = $_POST['status'];

// Validate data types
if (!is_numeric($quantity) || !is_numeric($amount) || !is_numeric($weight)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Quantity, amount, and weight must be numeric values'
    ]);
    exit;
}

// Find or create customer first
$customersCollection = $db->customers;
$customer = $customersCollection->findOne(['name' => $customerName, 'phone' => $customerPhone]);

if (!$customer) {
    $customerResult = $customersCollection->insertOne([
        "name" => $customerName,
        "phone" => $customerPhone,
        "address" => $customerAddress
    ]);
    $customerId = $customerResult->getInsertedId();
} else {
    $customerId = $customer->_id;
}

// Create order document with customer reference
$result = $ordersCollection->insertOne([
    "customerId" => $customerId,
    "item" => $item,
    "quantity" => $quantity,
    "amount" => $amount,
    "weight" => $weight,
    "status" => $status
]);

if ($result->getInsertedCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Order has been successfully added.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add order.']);
}
?>
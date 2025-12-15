<?php
require '../vendor/autoload.php';

header('Content-Type: application/json');

// Connect to MongoDB
$conn = new MongoDB\Client("mongodb://localhost:27017");
$db = $conn->logistics;
$collection = $db->orders;

// Fetch all unassigned orders with customer details using aggregation
$pipeline = [
    ['$match' => ['courierId' => ['$exists' => false]]],
    ['$lookup' => [
        'from' => 'customers',
        'localField' => 'customerId',
        'foreignField' => '_id',
        'as' => 'customerDetails'
    ]],
    ['$unwind' => '$customerDetails']
];

$orders = $collection->aggregate($pipeline);
$ordersList = [];

// Convert cursor to array and format response
foreach ($orders as $order) {
    $ordersList[] = [
        '_id' => (string)$order['_id'],
        'customer' => [
            'name' => $order['customerDetails']['name'],
            'phone' => isset($order['customerDetails']['phone']) ? $order['customerDetails']['phone'] : '',
            'address' => isset($order['customerDetails']['address']) ? $order['customerDetails']['address'] : ''
        ],
        'item' => $order['item'],
        'quantity' => $order['quantity'],
        'amount' => $order['amount'],
        'weight' => $order['weight']
    ];
}

// Return JSON response
echo json_encode(['success' => true, 'orders' => $ordersList]);
?>
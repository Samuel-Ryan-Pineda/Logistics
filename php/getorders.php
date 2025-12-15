<?php
require '../vendor/autoload.php';

header('Content-Type: application/json');

// Connect to MongoDB
$conn = new MongoDB\Client("mongodb://localhost:27017");
$db = $conn->logistics;
$collection = $db->orders;

// Fetch all orders with customer and courier details using aggregation
$pipeline = [
    ['$lookup' => [
        'from' => 'customers',
        'localField' => 'customerId',
        'foreignField' => '_id',
        'as' => 'customerDetails'
    ]],
    ['$unwind' => '$customerDetails'],
    ['$lookup' => [
        'from' => 'couriers',
        'localField' => 'courierId',
        'foreignField' => '_id',
        'as' => 'courierDetails'
    ]],
    ['$unwind' => ['path' => '$courierDetails', 'preserveNullAndEmptyArrays' => true]]
];

$orders = $collection->aggregate($pipeline);
$ordersList = [];

// Convert cursor to array
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
        'weight' => $order['weight'],
        'status' => $order['status'],
        'dateDelivered' => isset($order['dateDelivered']) ? $order['dateDelivered']->toDateTime()->format('Y-m-d H:i:s') : null,
        'courier' => isset($order['courierDetails']) && isset($order['courierDetails']['courierName']) ? $order['courierDetails']['courierName'] : '-'
    ];
}

// Return JSON response
echo json_encode(['success' => true, 'orders' => $ordersList]);
?>
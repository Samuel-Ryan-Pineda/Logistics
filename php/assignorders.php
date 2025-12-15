<?php
require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

header('Content-Type: application/json');

// Get POST data
if (!isset($_POST['courierId']) || !isset($_POST['orderIds']) || empty($_POST['orderIds'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$courierId = $_POST['courierId'];
$orderIds = $_POST['orderIds'];

// Connect to MongoDB
$client = new Client("mongodb://localhost:27017");
$collection = $client->logistics->orders;

// Convert order IDs to MongoDB ObjectIds
$orderObjectIds = array_map(function($id) {
    return new ObjectId($id);
}, $orderIds);

// Convert courier ID to MongoDB ObjectId
$courierObjectId = new ObjectId($courierId);

// Update all selected orders with the courier ID and set status to 'to be delivered'
$result = $collection->updateMany(
    ['_id' => ['$in' => $orderObjectIds]],
    ['$set' => [
        'courierId' => $courierObjectId,
        'status' => 'to be delivered'
    ]]
);

if ($result->getModifiedCount() > 0) {
    echo json_encode([
        'success' => true,
        'message' => 'Orders assigned successfully',
        'modifiedCount' => $result->getModifiedCount()
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No orders were updated'
    ]);
}
?>
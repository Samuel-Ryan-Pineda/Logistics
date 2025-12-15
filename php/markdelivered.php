<?php
require '../vendor/autoload.php';

header('Content-Type: application/json');

// Connect to MongoDB
$conn = new MongoDB\Client("mongodb://localhost:27017");
$db = $conn->logistics;
$collection = $db->orders;

// Get order ID from POST request
$orderId = isset($_POST['orderId']) ? new MongoDB\BSON\ObjectId($_POST['orderId']) : null;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required.']);
    exit;
}

// Check if order has an assigned courier
$order = $collection->findOne(['_id' => $orderId]);
if (!$order || !isset($order->courierId)) {
    echo json_encode(['success' => false, 'message' => 'Cannot mark order as delivered: No courier assigned.']);
    exit;
}

// Update order status and add dateDelivered in Philippine timezone (UTC+8)
$utcTime = new DateTime('now', new DateTimeZone('UTC'));
$phTime = new DateTime('now', new DateTimeZone('Asia/Manila'));
$utcTime->setTimestamp($phTime->getTimestamp());
$milliseconds = (int)($utcTime->format('U.u') * 1000);
$result = $collection->updateOne(
    ['_id' => $orderId],
    ['$set' => [
        'status' => 'delivered',
        'dateDelivered' => new MongoDB\BSON\UTCDateTime($milliseconds)
    ]]
);

if ($result->getModifiedCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Order marked as delivered successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update order status.']);
}
?>
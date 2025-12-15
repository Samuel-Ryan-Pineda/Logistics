<?php
require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

try {
    $client = new MongoDB\Client('mongodb://localhost:27017');
    $collection = $client->logistics->couriers;

    $couriers = $collection->find()->toArray();

    $formattedCouriers = array_map(function($courier) {
        return [
            'id' => (string)$courier->_id,
            'courierName' => $courier->courierName,
            'phoneNumber' => $courier->phoneNumber,
            'deliveryHub' => $courier->deliveryHub,
            'rating' => isset($courier->rating) ? (int)$courier->rating . ' ★' : ''
        ];
    }, $couriers);

    echo json_encode([
        'success' => true,
        'couriers' => $formattedCouriers
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
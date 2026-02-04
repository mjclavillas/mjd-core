<?php

$router->group(['prefix' => '/api'], function($router) {
    $router->get('/status', function() {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ONLINE']);
        exit;
    });

    $router->post('/status', function() {
        header('Content-Type: application/json');

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $response = [
            'status' => 'DATA_RECEIVED',
            'received_at' => date('Y-m-d H:i:s'),
            'your_payload' => $data ?? 'No data provided',
            'note' => 'This was a POST request processed by MJD-Core'
        ];

        echo json_encode($response);
        exit;
    });

});
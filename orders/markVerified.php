<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/invoice.php';

    $database = new Database();
    $db = $database->getConnection();

    $invoice = new Invoice($db);
    $lines = new InvoiceLines($db);

    $lines->invlineid = isset($_GET['id']) ? +$_GET['id']: die();

    if ($lines->markVerified()) {
        http_response_code(201);
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Successful Update.'
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Failed to update'
        ));
    }
?>
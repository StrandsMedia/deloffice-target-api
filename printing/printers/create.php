<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/printing.php';

    $database = new Database();
    $db = $database->getConnection();

    $printers = new Printer($db);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $printers->printerName = $data->printerName;

            if ($printers->createPrinter()) {
                http_response_code(201);

                echo json_encode(array(
                    'message' => 'Printing entry was created.'
                ));
            } else {
                http_response_code(503);

                echo json_encode(array(
                    'message' => 'Unable to create printing entry.'
                ));
            }

        } else {
            http_response_code(400);
            echo json_encode(array(
                'message' => 'Unable to create product. Data is incomplete or not found.'
            ));
        }
    }
?>
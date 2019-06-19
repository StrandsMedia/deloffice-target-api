<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/tender.php';

    $database = new Database();
    $db = $database->getConnection();

    $tender = new Tender($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tender->tid = $data->tid;

        $tender->cust_id = $data->cust_id;
        $tender->product = $data->product;
        $tender->estimated_quantity = $data->estimated_quantity;
        $tender->schedule = $data->schedule;
        $tender->receive_date = $data->receive_date;
        $tender->closing_date = $data->closing_date;
        $tender->actual_quantity = $data->actual_quantity;
        $tender->delivery = $data->delivery;
        $tender->product_quoted = $data->product_quoted;
        $tender->price_quoted = $data->price_quoted;
        $tender->attachment = $data->attachment;
        $tender->result = $data->result;
        $tender->comments = $data->comments;
        $tender->status = $data->status;
    
        if (isset($data)) {
            if ($tender->update()) {
                http_response_code(200);
        
                echo json_encode(array(
                    'message' => 'Tender Entry was updated.'
                ));
            } else {
                http_response_code(503);
        
                echo json_encode(array(
                    'message' => 'Unable to update tender entry.'
                ));
            }
        }
    }

?>
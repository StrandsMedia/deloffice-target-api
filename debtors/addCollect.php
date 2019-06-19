<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/debtors.php';

    $database = new Database();
    $db = $database->getConnection();

    $collect = new DebtCollect($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $collect->cust_id = $data->cust_id;
            $collect->pay_method = $data->pay_method;
            $collect->delivery_pay = $data->delivery_pay;
            $collect->amount = isset($data->amount) ? $data->amount : '0';
            $collect->type = $data->type;
            $collect->data = $data->data;
            $collect->region = $data->region;
            $collect->remarks = $data->remarks;
            if ($collect->createEntry()) {
                http_response_code(201);
                echo json_encode(array(
                    'status' => 'success',
                    'message' => 'Entry created successfully.'
                ));
            } else {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'An unknown error occured.'
                ));
            }
        } else {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Data was not found or is incomplete. Please try again later.'
            ));
        }
    }
?>
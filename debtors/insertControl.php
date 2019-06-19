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

    $control = new DebtorsControl($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $control->cust_id = $data->cust_id;
            $control->agent = $data->agent;
            $control->status = $data->status;
            $control->data = $data->data;

            if ($control->create()) {
                http_response_code(201);
                echo json_encode(
                    array(
                        'status' => 'success',
                        'message' => 'Successful creation of data.'
                    )
                );
            } else {
                http_response_code(503);
                echo json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'Failed to update entry.'
                    )
                );
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Data was not found or is incomplete. Please try again later.'
            ));
        }
    }
?>
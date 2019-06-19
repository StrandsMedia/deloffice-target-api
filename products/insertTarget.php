<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/products.php';

    $database = new Database();
    $db = $database->getConnection();

    $target = new Target($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $target->cust_id = $data->cust_id;
            $target->pf_id = $data->pf_id;
            $target->p_id = $data->p_id;
            $target->pricecat_id = $data->pricecat_id;
            $target->customprice = $data->customprice;
            $target->validity_date = isset($data->validity_date) ? date('Y-m-d H:i:s', strtotime($data->validity_date)) : NULL;
            $target->tar_notes = $data->tar_notes;
            $target->user = $data->user;

            if ($target->insertTgtPrice()) {
                echo json_encode(array(
                    'status' => 'success',
                    'message' => 'Target created successfully.'
                ));
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Error. Cannot insert target.'
            ));
        }
    }
?>
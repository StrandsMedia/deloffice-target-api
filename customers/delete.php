<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/customer.php';

    $database = new Database();
    $db = $database->getConnection();

    $customer = new DelCustomer($db);

    $data = json_decode(file_get_contents('php://input'));

    $customer->cust_id = $data->cust_id;

    if (isset($data)) {
        if ($customer->delete()) {
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Customer was successfully deleted.'
            ));
        } else {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Unable to delete product.'
            ));
        }
    }
?>
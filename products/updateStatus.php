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

    $statcust = new StatusCust($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $statcust->cust_id = $data->cust_id;
            $statcust->pf_id = $data->pf_id;
            $statcust->statusNum = $data->status;

            if ($statcust->getStatusByCustOne() > 0) {
                if ($statcust->updateStatus()) {
                    http_response_code(200);
                    echo json_encode(array(
                        'status' => 'success',
                        'message' => 'Status updated succesfully.'
                    ));
                }
            } else {
                if ($statcust->insertStatus()) {
                    http_response_code(200);
                    echo json_encode(array(
                        'status' => 'success',
                        'message' => 'Status updated succesfully.'
                    ));
                }
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Error. Cannot update status.'
            ));
        }
    }
?>
<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../config/db.php';
    include_once '../objects/invoice.php';
    include_once '../objects/purchases.php';

    $database = new Database();
    $db = $database->getConnection();

    $reqs = new PurchaseRequest($db);
    $reqprod = new PurchaseRequestProds($db);

    $lines = new InvoiceLines($db);

    $data = json_decode(file_get_contents("php://input"));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $reqs->req_id = $data->req_id;
            $reqs->completed = $data->status;

            if ($reqs->update()) {
                $lines->amendstatus = +$data->status + 1;
                $lines->invlineid = $data->invlineid;
                if ($lines->markStatus(+$data->type)) {
                    http_response_code(201);

                    echo json_encode(array(
                        'status' => 'success',
                        'message' => 'Entry created successfully.'
                    ));
                }
            }


        } else {
            http_response_code(400);
    
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Unable to create entry. No data was found.'
            ));
        }
    }
?>
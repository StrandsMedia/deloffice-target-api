<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/invoice.php';
    include_once '../objects/proforma.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $invoice = new Invoice($db);
    $proforma = new ProformaHistory($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $invoice->InvStatus = 8;
            $invoice->invoice_id = $data->invoice_id;
        
            $proforma->workflow_id = $data->workflow_id;
            $proforma->user = $data->user;
            $proforma->step = $invoice->InvStatus;
            $proforma->note = '';
            $proforma->comment = '';
        
            if ($proforma->create()) {
                if ($invoice->updateInvStatus()) {
                    http_response_code(200);
                    echo json_encode(array(
                        'status' => 'success',
                        'message' => 'Proforma invoice updated successfully.'
                    ));
                }
            } else {
                http_response_code(503);
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Failed to update invoice.'
                ));
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
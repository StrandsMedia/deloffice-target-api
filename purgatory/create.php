<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../config/db.php';
    include_once '../objects/invoice.php';
    include_once '../objects/purgatory.php';
    include_once '../objects/proforma.php';

    $database = new Database();
    $db = $database->getConnection();

    $invoice = new Invoice($db);
    $proforma = new ProformaHistory($db);
    $purgatory = new Purgatory($db);

    $data = json_decode(file_get_contents("php://input"));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $purgatory->invoice_id = $data->invoice_id;
            $purgatory->invlineid = $data->invlineid;
            $purgatory->p_id = $data->p_id;
            $purgatory->debit = $data->debit;
            $purgatory->credit = $data->credit;
            $purgatory->outstd = $data->outstd;
            $purgatory->entryType = $data->entryType;

            $invoice->InvStatus = 7;
            $invoice->invoice_id = $data->invoice_id;

            $proforma->workflow_id = $data->workflow_id;
            $proforma->user = $data->user;
            $proforma->step = $invoice->InvStatus;
            $proforma->note = '';
            $proforma->comment = '';

            if ($proforma->create()) {
                if ($purgatory->create()) {
                    if ($invoice->updateInvStatus()) {
                        http_response_code(201);
            
                        echo json_encode(array(
                            'status' => 'success',
                            'message' => 'Purgatory was successfully created'
                        ));
                    }
                } else {
                    http_response_code(503);
        
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Unable to create purgatory'
                    ));
                }
            }
        } else {
            http_response_code(400);
    
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Unable to create purgatory. No data was found.'
            ));
        }
    }
?>
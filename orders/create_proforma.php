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

    $workflow = new Workflow($db);
    $invoice = new Invoice($db);
    $proforma = new ProformaHistory($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $invoice->company_name = $data->company_name;
            $invoice->user = $data->user;
            $invoice->customerCode = $data->customerCode;
            $invoice->workflow_id = $data->workflow_id;

            $workflow->workflow_id = $data->workflow_id;
            $workflow->status = 25;

            $proforma->workflow_id = $data->workflow_id;
            $proforma->user = $data->user;
            $proforma->step = 1;
            $proforma->note = '';
            $proforma->comment = '';
            if ($workflow->update(4)) {
                if ($invoice->createProforma()) {
                    $invoice->invoice_id = $db->lastInsertId();
                    if ($invoice->updateInvRef()) {
                        if ($proforma->create()) {
                            http_response_code(201);
                            echo json_encode($invoice->invoice_id);
                        }
                    }
                }
            }
        } else {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Data was not found or is incomplete. Please try again later.'
            ));
        }
    }



?>
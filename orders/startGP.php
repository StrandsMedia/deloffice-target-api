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
    $workflow = new Workflow($db);
    $lines = new InvoiceLines($db);
    $proforma = new ProformaHistory($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            if (isset($data->workflow_id)) {
                if ($data->tr_status == 1) {
                    $invoice->company_name = $data->company_name;
                    $invoice->user = $data->user;
                    $invoice->customerCode = $data->customerCode;
                    $invoice->workflow_id = $data->workflow_id;
        
                    $invoice->invoice_id = $data->invoice_id;
    
                    $workflow->workflow_id = +$data->workflow_id;
                    $workflow->status = 26;
    
                    $invoice->InvStatus = 6;
        
                    $proforma->workflow_id = $data->workflow_id;
                    $proforma->user = $data->user;
                    $proforma->step = 6;
                    $proforma->note = '';
                    $proforma->comment = '';
                } else {
                    $invoice->company_name = $data->company_name;
                    $invoice->user = $data->user;
                    $invoice->customerCode = $data->customerCode;
                    $invoice->workflow_id = $data->workflow_id;
        
                    $invoice->invoice_id = $data->invoice_id;
    
                    $workflow->workflow_id = +$data->workflow_id;
                    $workflow->status = 26;
    
                    $invoice->InvStatus = 8;
        
                    $proforma->workflow_id = $data->workflow_id;
                    $proforma->user = $data->user;
                    $proforma->step = 8;
                    $proforma->note = '';
                    $proforma->comment = '';
                }
                

                if ($invoice->updateInvStatus()) {
                    $workflow->invoice_id = $invoice->invoice_id;

                    if ($workflow->update(5)) {
                        if ($proforma->create()) {

                            if ($data->tr_status == 1) {
                                http_response_code(201);
                                echo json_encode(array(
                                    'status' => 'success',
                                    'message' => 'Goods Preparation Session started.'
                                ));

                            } else {
                                http_response_code(201);
                                echo json_encode(array(
                                    'status' => 'success',
                                    'message' => 'Goods Verification Session started.'
                                ));
                            }
                        }
                    }
                }
                
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
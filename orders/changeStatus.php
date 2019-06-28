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
    $workflow = new Workflow($db);
    $workflowhist = new WorkflowHistory($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            switch (+$data->step) {
                case 1:
                    // Create Proforma Invoice
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
                    
                    if ($invoice->createProforma()) {
                        $invoice->invoice_id = $db->lastInsertId();
                        if ($invoice->updateInvRef()) {
                            $workflow->invoice_id = $invoice->invoice_id;
        
                            if ($workflow->update(5)) {
                                if ($proforma->create()) {
                                    http_response_code(201);
                                    echo json_encode($invoice->invoice_id);
                                }
                            }
                        }
                    }
                    break;
                case 2:
                    // Amend Proforma
                    $invoice->InvStatus = 2;
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
                    break;
                case 3:
                    // GP To Be Checked
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
                    break;
                case 4:
                    // Goods Ready
                    $workflow->status = 7;
                    $workflow->workflow_id = $data->workflow_id;                   
                
                    $workflowhist->workflow_id = $data->workflow_id;
                    $workflowhist->user = $data->user;
                    $workflowhist->step = $invoice->InvStatus;
                    $workflowhist->note = '';
                    $workflowhist->comment = '';
                
                    if ($workflowhist->insertHistory()) {
                        if ($workflow->update(4)) {
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
                    break;
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
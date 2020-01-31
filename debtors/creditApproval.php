<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/debtors.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);
    $delivery = new WorkflowDelivery($db);
    $history = new WorkflowHistory($db);

    $collect = new DebtCollect($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $history->workflow_id = $data->workflow_id;
            $history->user = $data->user;
            $history->step = $data->status;
            $history->note = null;
            $history->comment = $data->comment;
            if ($history->insertHistory()) {

                $history->workflow_id = $data->workflow_id;
                $history->user = $data->user;

                if ($data->status == 28) {
                    $status = 3;
                    $workflow->creditCtrl = 3;
                } else {
                    $status = 27;
                    $workflow->creditCtrl = 2;
                }

                $workflow->workflow_id = $data->workflow_id;

                $status = $workflow->getStatus();

                $history->step = $status;
                $history->note = null;
                $history->comment = $data->comment;

                $collect->cust_id = $data->cust_id;
                $collect->pay_method = 1;
                $collect->delivery_pay = 0;
                $collect->amount = isset($data->amount) ? $data->amount : '0';
                $collect->type = 0;
                $collect->data = $data->data;

                // Don't forget to change this line >> HARDCODED
                $collect->region = 'North';

                $collect->remarks = $data->comment;
                if ($collect->createEntry()) {
                    
                    if ($history->insertHistory()) {
                        $delivery->workflow_id = $data->workflow_id;
                        $delivery->delivery_status = $status;
                        if ($delivery->updateStatus()) {
                            $workflow->workflow_id = $data->workflow_id;

                            $workflow->status = $workflow->getStatus();
                            if ($workflow->update(6)) {
                                http_response_code(200);
                                echo json_encode(array(
                                    'status' => 'success',
                                    'message' => 'Process completed.'
                                ));
                            } else {
                                http_response_code(503);
                                echo json_encode(array(
                                    'status' => 'error',
                                    'message' => 'An unknown error occured.'
                                ));
                            }
                        } else {
                            http_response_code(503);
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => 'An unknown error occured.'
                            ));
                        }
                    } else {
                        http_response_code(503);
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'An unknown error occured.'
                        ));
                    }
                } else {
                    http_response_code(503);
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'An unknown error occured.'
                    ));
                }


            } else {
                http_response_code(503);
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'An unknown error occured.'
                ));
            }
        }
    }
?>
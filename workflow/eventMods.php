<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);
    $delivery = new WorkflowDelivery($db);
    $history = new WorkflowHistory($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $step = $data->step;
            $workflow->workflow_id = $data->workflow_id;
            $delivery->workflow_id = $data->workflow_id;
            $history->workflow_id = $data->workflow_id;
            $history->user = $data->user;

            switch ($step) {
                case 1:
                    $workflow->status = 7;
                    if ($workflow->update(4)) {
                        $delivery->workflow_id = $data->workflow_id;
                        $delivery->delivery_status = 7;
                        if ($delivery->updateStatus()) {
                            $history->step = 23;
                            if ($history->insertHistory()) {
                                http_response_code(201);
                                echo json_encode(array(
                                    'status' => 'success',
                                    'message' => 'Update successful'
                                ));
                            } else {
                                echo json_encode(array(
                                    'status' => 'error',
                                    'message' => 'Update was not completed.'
                                ));
                            }
                        } else {
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => 'Update was not completed.'
                            ));
                        }
                    }
                    break;
                case 2:
                    $workflow->urgent = $data->urgent;
                    if ($workflow->urgentOrPurchase('urgent')) {
                        $history->step = 22;
                        if ($history->insertHistory()) {
                            http_response_code(201);
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => 'Update successful'
                            ));
                        } else {
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => 'Update was not completed.'
                            ));
                        }
                    } else {
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'Update was not completed.'
                        ));
                    }
                    break;
                case 3:
                    $workflow->urgent = $data->urgent;
                    if ($workflow->urgentOrPurchase('purchase')) {
                        $history->step = 21;
                        if ($history->insertHistory()) {
                            http_response_code(201);
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => 'Update successful'
                            ));
                        } else {
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => 'Update was not completed.'
                            ));
                        }
                    } else {
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'Update was not completed.'
                        ));
                    }
                    break;
                case 4:
                    $workflow->invoiceNo = $data->invoiceNo;
                    if ($workflow->updateInvoice()) {
                        $delivery->invoice_no = $data->invoiceNo;
                        if ($delivery->updateInvoice()) {
                            $history->note = $data->invoiceNo;
                            $history->step = 24;
                            if ($history->insertHistory()) {
                                http_response_code(201);
                                echo json_encode(array(
                                    'status' => 'success',
                                    'message' => 'Update successful'
                                ));
                            } else {
                                echo json_encode(array(
                                    'status' => 'error',
                                    'message' => 'Update was not completed.'
                                ));
                            }
                        } else {
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => 'Update was not completed.'
                            ));
                        }
                    } else {
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'Update was not completed.'
                        ));
                    }
                    break;
                case 5:
                    $delivery->comments = $data->instructions;
                    if ($delivery->updateComments()) {
                        $history->step = 19;
                        $history->comment = $data->instructions;
                        if ($history->insertHistory()) {
                            http_response_code(201);
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => 'Update successful'
                            ));
                        } else {
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => 'Update was not completed.'
                            ));
                        }
                    } else {
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'Update was not completed.'
                        ));
                    }
                    break;
                case 6:
                    $delivery->purchase = $data->instructions;
                    if ($delivery->updatePurchase()) {
                        $history->step = 20;
                        $history->comment = $data->instructions;
                        if ($history->insertHistory()) {
                            http_response_code(201);
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => 'Update successful'
                            ));
                        } else {
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => 'Update was not completed.'
                            ));
                        }
                    } else {
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'Update was not completed.'
                        ));
                    }
                    break;
            }
        }
    }
?>
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
            $workflow->workflow_id = $data->workflow_id;
            $workflow->status = $data->step;

            $history->workflow_id = $data->workflow_id;
            $history->step = $data->step;
            $history->user = $data->user;
            $history->comment = $data->comment;

            $delivery->workflow_id = $data->workflow_id;
            $delivery->delivery_status = $data->step;

            if ($workflow->update(4)) {
                if ($delivery->updateStatus()) {
                    if ($history->insertHistory()) {
                        echo json_encode(
                            array(
                                'status' => 'success',
                                'message' => 'Update successful.'
                            )
                        );
                    } else {
                        http_response_code(503);
                        echo json_encode(
                            array(
                                'status' => 'error',
                                'message' => 'Update failed.'
                            )
                        );
                    }
                } else {
                    http_response_code(503);
                    echo json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'Update failed.'
                        )
                    );
                }
            } else {
                http_response_code(503);
                echo json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'Update failed.'
                    )
                );
            }
            
        } else {
            http_response_code(503);
            echo json_encode(
                array(
                    'status' => 'error',
                    'message' => 'Update failed.'
                )
            );
        }
    }


?>
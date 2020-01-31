<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/tables.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $tables = new PrepTables($db);
    $workflow = new Workflow($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $tables->tableId = $data->tableId;
            $tables->status = 0;

            $workflow->tableId = 0;
            $workflow->workflow_id = $data->workflow_id;

            if ($workflow->updateOnTableSession()) {
                if ($tables->updateStatus()) {
                    echo json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Successful update'
                        )
                    );
                } else {
                    echo json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'Failed to update'
                        )
                    );
                }
            } else {
                echo json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'Failed to update'
                    )
                );
            }
        }
    }
?>
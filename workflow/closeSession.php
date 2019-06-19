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
    $session = new WorkflowSession($db);
    
    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $session->status = 1;
            $session->user = $data->user;
            $session->driver = $data->driver;
            $session->vehicle = $data->vehicle;
            $session->sessionID = $data->sessionID;
            $session->region = $data->region;
            if ($session->closeSession()) {
                $workflow->status = 8;
                $workflow->vehicleNo = $data->vehicle;
                $workflow->sessionID = $data->sessionID;
                if ($workflow->updateOnSession()) {
                    $stmt = $workflow->getSession();

                    if ($stmt->rowCount() > 0) {
                        $count = 0;
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            extract($row);
                            $history->workflow_id = $workflow_id;
                            $history->user = $data->user;
                            $history->step = 9;
                            $history->note = $data->vehicle;

                            if ($history->insertHistory()) {
                                $count++;
                            }
                        }
                        if ($count === $stmt->rowCount()) {
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => 'Operation successful. Session was closed.'
                            ));
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
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
    $session = new WorkflowSession($db);
    $history = new WorkflowHistory($db);
    $delivery = new WorkflowDelivery($db);

    $data = json_decode(file_get_contents('php://input'));

    $session->vehicle = $data->vehicle;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($session->isThereSession() === 0) {
            if ($session->createSession()) {
                $lastId = $db->lastInsertId(); // fetch last inserted ID; after success.
            } else {
                echo json_encode(
                    array('message' => 'Unable to create session.')
                );
            }
        } else {
            $lastId = $session->sessionNumber();
        }
    
        foreach($data->invoices as $i => $invoice) {
            $workflow->status = $data->status;
            $workflow->vehicleNo = $data->vehicle;
            $workflow->sessionID = $lastId;
    
            if($workflow->onSession($invoice->wfid)) {
                $history->user = $data->user;
                $history->step = $data->status;
                $history->note = $data->vehicle;
                $history->comment = $data->vehicle;
        
                if ($history->insertHistory()) {
                    $delivery->vehicle = $data->vehicle;
                    $delivery->status = $data->status;
                    $delivery->jobID = $lastId;
            
                    if ($delivery->updateOnSession($invoice->wfid)) {
                        echo json_encode(
                            array('message' => 'Operation successful.')
                        );
                    };
                };
            };
        } 
    }



    
?>
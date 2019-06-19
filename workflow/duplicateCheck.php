<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);

    $workflow->invoiceNo = isset($_GET['id']) ? $_GET['id'] : die();
    $workflow->workflow_id = isset($_GET['wf']) ? $_GET['wf'] : null;

    $stmt = $workflow->findInvoice();
    $num = $stmt->rowCount();

    if ($num == 1) {
        echo json_encode(array(
            'result' => true
        ));
    } else {
        echo json_encode(array(
            'result' => false
        ));
    }
?>
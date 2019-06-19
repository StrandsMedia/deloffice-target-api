<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/workflow.php';
    include_once '../objects/invoice.php';

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);
    $details = new WorkflowDetails($db);
    $lines = new InvoiceLines($db);

    $workflow->workflow_id = isset($_GET['id']) ? $_GET['id'] : die();

    $products = $details->getParsedProducts($workflow->workflow_id);

    if (isset($products)) {
        http_response_code(200);
        echo json_encode($products);
    } else {
        $products = $lines->getParsedProducts($workflow->workflow_id);

        if (isset($products)) {
            http_response_code(200);
            echo json_encode($products);
        } else {
            echo json_encode(array(
                'workflow_id' => $workflow->workflow_id,
                'message' => 'Error retrieving data.'
            ));
        }
    }

?>
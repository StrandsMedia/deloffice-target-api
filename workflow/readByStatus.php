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

    $workflow->status = isset($_GET['s']) ? $_GET['s'] : die();

    $stmt = $workflow->readByStatus();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $workflow_arr = array();
        $workflow_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $workflow_item = array(
                'company_name' => $company_name,
                'creditNo' => $creditNo,
                'cust_id' => $cust_id,
                'invoiceNo' => $invoiceNo,
                'invoice_id' => $invoice_id,
                'orderNo' => $orderNo,
                'purchase' => $purchase,
                'sessionID' => $sessionID,
                'status' => $status,
                'time' => $time,
                'urgent' => $urgent,
                'vehicleNo' => $vehicleNo,
                'workflow_id' => $workflow_id,
                'purchaseIns' => $purchaseIns
            );

            array_push($workflow_arr['records'], $workflow_item);
        }

        echo json_encode($workflow_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }

?>
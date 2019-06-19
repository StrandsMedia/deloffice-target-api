<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');

    include_once '../config/db.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);

    $stmt = $workflow->goodsReady();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $workflow_arr = array();
        $workflow_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $workflow_item = array(
                'workflow_id' => $workflow_id,
                'time' => $time,
                'company_name' => $company_name,
                'status' => $status,
                'urgent' => $urgent,
                'cust_id' => $cust_id,
                'orderNo' => $orderNo,
                'purchase' => $purchase,
                'invoiceNo' => $invoiceNo,
                'creditNo' => $creditNo,
                'vehicleNo' => $vehicleNo,
                'sessionID' => $sessionID,
                'invoice_id' => $invoice_id
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
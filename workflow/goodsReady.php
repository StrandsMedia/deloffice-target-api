<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');

    include_once '../config/db.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);

    $stmt = $workflow->goodsReady(1);
    $stmt2 = $workflow->goodsReady(2);
    $stmt3 = $workflow->goodsReady(3);
    $num = $stmt->rowCount() + $stmt2->rowCount() + $stmt3->rowCount();

    if ($num > 0) {
        $workflow_arr = array();
        $workflow_arr['records'] = array();

        $temp_array = array();

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

            array_push($temp_array, $workflow_item);
        }

        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            extract($row2);

            $workflow_item2 = array(
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

            array_push($temp_array, $workflow_item2);
        }

        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            extract($row3);

            $workflow_item3 = array(
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

            array_push($temp_array, $workflow_item3);
        }

        $workflow->array_sort_by_column($temp_array, 'time', SORT_DESC);
        $workflow_arr['records'] = $temp_array;

        http_response_code(200);
        echo json_encode($workflow_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
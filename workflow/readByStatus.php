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

    $stmt = $workflow->readByStatus(1);
    $stmt_2 = $workflow->readByStatus(2);
    $stmt_3 = $workflow->readByStatus(3);
    $num = $stmt->rowCount() + $stmt2->rowCount() + $stmt3->rowCount();

    if ($num > 0) {
        $workflow_arr = array();
        $workflow_arr['records'] = array();
        
        $temp_array = array();

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

            array_push($temp_array, $workflow_item);
        }

        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            extract($row2);

            $workflow_item2 = array(
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

            array_push($temp_array, $workflow_item2);
        }

        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            extract($row3);

            $workflow_item3 = array(
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
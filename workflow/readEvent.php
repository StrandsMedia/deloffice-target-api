<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/workflow.php';
    include_once '../objects/invoice.php';
    include_once '../objects/proforma.php';

    function array_sort_by_column(&$arr, $col, $dir = SORT_DESC) {
        $sort_col = array();
        foreach($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);
    $details = new WorkflowDetails($db);
    $history = new WorkflowHistory($db);
    $lines = new InvoiceLines($db);

    $proforma = new ProformaHistory($db);

    $workflow->workflow_id = isset($_GET['id']) ? $_GET['id'] : die();

    $stmt = $workflow->readEvent();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $event = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $event_item = array(
                'workflow_id' => $workflow_id,
                'time' => $time,
                'status' => $status,
                'urgent' => $urgent,
                'cust_id' => $cust_id,
                'orderNo' => $orderNo,
                'purchase' => $purchase,
                'invoiceNo' => $invoiceNo,
                'creditNo' => $creditNo,
                'vehicleNo' => $vehicleNo,
                'sessionID' => $sessionID,
                'invoice_id' => $invoice_id,
                'dinstr' => $dinstr,
                'pinstr' => $pinstr,
                'company_name' => $company_name
            );

            $event_item['products'] = $details->getProducts($workflow_id);

            if (!isset($event_item['products'])) {
                $event_item['products'] = $lines->getProducts($workflow_id);
            }

            if (!isset($invoice_id)) {
                $event_item['invoice_id'] = $workflow->getInvoiceId($workflow_id);
            }

            $stmt2 = $history->readHistory($workflow_id);
            $stmt3 = $proforma->readByWF($workflow_id);

            $num2 = $stmt2->rowCount();

            if ($num2 > 0) {
                $history = array();
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {

                    $history_item = array(
                        'historyid' => $row2['historyid'],
                        'workflow_id' => $row2['workflow_id'],
                        'time' => $row2['time'],
                        'user' => $row2['user'],
                        'note' => $row2['note'],
                        'comment' => $row2['comment'],
                        'step' => $row2['step'],
                        'stepname' => $row2['stepname'],
                        'sales_rep' => $row2['sales_rep']
                    );

                    array_push($history, $history_item);
                }

                if ($stmt3->rowCount() > 0) {
                    while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                        $history_item = array(
                            'historyid' => $row3['history_id'],
                            'workflow_id' => $row3['workflow_id'],
                            'time' => $row3['time'],
                            'user' => $row3['user'],
                            'note' => $row3['note'],
                            'comment' => $row3['comment'],
                            'step' => $row3['step'],
                            'stepname' => $row3['stepname'],
                            'sales_rep' => $row3['sales_rep']
                        );

                        array_push($history, $history_item);
                    }
                }

                array_sort_by_column($history, 'time', SORT_ASC);

                $event_item['history'] = $history;
            }

            array_push($event, $event_item);
        }

        http_response_code(200);
        echo json_encode($event);

    } else {
        echo json_encode(array(
            'workflow_id' => $_GET['id'],
            'message' => 'Error: Data not found.'
        ));
    }

?>
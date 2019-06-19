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
    $history = new WorkflowHistory($db);
    $lines = new InvoiceLines($db);

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

            $stmt2 = $history->readHistory($workflow_id);

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
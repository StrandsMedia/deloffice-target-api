<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    // include_once '../objects/pastel.php';
    include_once '../objects/invoice.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    // $srv_database = new DelServerDatabase();
    // $srvdb = $srv_database->getConnection();

    $invoice = new Invoice($db);
    $workflow = new Workflow($db);
    $history = new WorkflowHistory($db);

    // if (isset($srvdb)) {
    //     $postar = new PostAR($srvdb);
    // }

    $data = json_decode(file_get_contents('php://input'));

    $case = isset($data->case) ? +$data->case : die();

    $stmt = $workflow->read($case, 1);
    $stmt2 = $workflow->read($case, 2);
    $stmt3 = $workflow->read($case, 3);
    $num = $stmt->rowCount() + $stmt2->rowCount() + $stmt3->rowCount();

    if ($num > 0) {
        $workflow_arr = array();
        $workflow_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $workflow_item = array(
                'workflow_id' => $workflow_id,
                // 'time' => $history->readLastUpdateDate($workflow_id),
                'time' => $time,
                'company_name' => $company_name,
                'customerCode' => $customerCode,
                'status' => $status,
                'urgent' => $urgent,
                'cust_id' => $cust_id,
                'orderNo' => $orderNo,
                'purchase' => $purchase,
                'invoiceNo' => $invoiceNo,
                'creditNo' => isset($creditNo) ? $creditNo : '',
                'vehicleNo' => $vehicleNo,
                'sessionID' => $sessionID,
                'invoice_id' => isset($invoice_id) ? $invoice_id : '',
                // 'allocs' => $postar->ifAlloc($invoiceNo)
            );

            $inv_data = $invoice->getInvInfoByWF($workflow_id);
            if (isset($inv_data)) {
                $workflow_item['invoice_id'] = $inv_data['invoice_id'];
                $workflow_item['InvStatus'] = $inv_data['InvStatus'];
                $workflow_item['invRef'] = $inv_data['invRef'];
            }

            array_push($workflow_arr['records'], $workflow_item);
        }

        $workflow->array_sort_by_column($workflow_arr['records'], 'time', SORT_DESC);

        http_response_code(200);
        echo json_encode($workflow_arr);
    } else {
        http_response_code(404);
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/pastel.php';
    include_once '../objects/invoice.php';
    include_once '../objects/proforma.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    // $srv_database = new DelServerDatabase();
    // $srvdb = $srv_database->getConnection();

    $invoice = new Invoice($db);
    $lines = new InvoiceLines($db);
    $workflow = new Workflow($db);
    $history = new WorkflowHistory($db);

    $proforma = new ProformaHistory($db);

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

        $temp_array = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $workflow_item = array(
                'workflow_id' => $workflow_id,
                // 'time' => $history->readLastUpdateDate($workflow_id),
                'time' => $time,
                'company_name' => $company_name,
                'company' => 'DEL',
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
                'data' => 1,
                'creditCtrl' => $creditCtrl,
                'lastUser' => $proforma->findUser($workflow_id)
            );

            $invoice->workflow_id = $workflow_id;
            $workflow_item['amending'] = $invoice->getAmendStatus();

            $inv_data = $invoice->getInvInfoByWF($workflow_id);

            if (isset($inv_data)) {
                $workflow_item['invoice_id'] = $inv_data['invoice_id'];
                $workflow_item['InvStatus'] = $inv_data['InvStatus'];
                $workflow_item['invRef'] = $inv_data['invRef'];
            }

            if ($inv_data['invoice_id']) {
                $lines->invoice_id = $inv_data['invoice_id'];
                $response = $lines->getStatus();

                $workflow_item['amend'] = $response['amend'];
                $workflow_item['purchase'] = $response['purchase'];
                $workflow_item['transfer'] = $response['transfer'];

                $workflow_item['amendstatus'] = $response['amendstatus'];
                $workflow_item['purchasestatus'] = $response['purchasestatus'];
                $workflow_item['transferstatus'] = $response['transferstatus'];
            }

            if ($workflow_item['status'] == 5) {
                $srv_database = new DelServerDatabase();
                $srvdb = $srv_database->getConnection();

                if (isset($srvdb)) {
                    $invpastel = new InvNum($srvdb);
                    $invpastel->Description = str_pad($workflow_item['workflow_id'], 8, '0', STR_PAD_LEFT);

                    $invoice_num = $invpastel->fetchInvoiceByRef();
                    if (isset($invoice_num) && $invoice_num != null) {
                        $workflow->workflow_id = $workflow_item['workflow_id'];
                        $workflow->status = 6;
                        $workflow->invoiceNo = $invoice_num;
                        if ($workflow->update(1)) {
    
                        }
                    }

                }
            }

            array_push($temp_array, $workflow_item);
        }

        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            extract($row2);

            $workflow_item2 = array(
                'workflow_id' => $workflow_id,
                // 'time' => $history->readLastUpdateDate($workflow_id),
                'time' => $time,
                'company_name' => $company_name,
                'company' => 'RNS',
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
                'data' => 2,
                'creditCtrl' => $creditCtrl,
                'lastUser' => $proforma->findUser($workflow_id)
            );

            $invoice->workflow_id = $workflow_id;
            $workflow_item['amending'] = $invoice->getAmendStatus();

            $inv_data = $invoice->getInvInfoByWF($workflow_id);

            if (isset($inv_data)) {
                $workflow_item2['invoice_id'] = $inv_data['invoice_id'];
                $workflow_item2['InvStatus'] = $inv_data['InvStatus'];
                $workflow_item2['invRef'] = $inv_data['invRef'];
            }

            if ($inv_data['invoice_id']) {
                $lines->invoice_id = $inv_data['invoice_id'];
                $response = $lines->getStatus();

                $workflow_item['amend'] = $response['amend'];
                $workflow_item['purchase'] = $response['purchase'];
                $workflow_item['transfer'] = $response['transfer'];

                $workflow_item['amendstatus'] = $response['amendstatus'];
                $workflow_item['purchasestatus'] = $response['purchasestatus'];
                $workflow_item['transferstatus'] = $response['transferstatus'];
            }

            if ($workflow_item['status'] == 5) {
                $srv_database = new RnsServerDatabase();
                $srvdb = $srv_database->getConnection();

                if (isset($srvdb)) {
                    $invpastel = new InvNum($srvdb);
                    $invpastel->Description = str_pad($workflow_item['workflow_id'], 8, '0', STR_PAD_LEFT);

                    $invoice_num = $invpastel->fetchInvoiceByRef();

                    $workflow->workflow_id = $workflow_item['workflow_id'];
                    $workflow->invoiceNo = $invoice_num;
                    if ($workflow->update(1)) {

                    }
                }
            }

            array_push($temp_array, $workflow_item2);
        }

        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            extract($row3);

            $workflow_item3 = array(
                'workflow_id' => $workflow_id,
                // 'time' => $history->readLastUpdateDate($workflow_id),
                'time' => $time,
                'company_name' => $company_name,
                'company' => 'PNP',
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
                'data' => 3,
                'creditCtrl' => $creditCtrl,
                'lastUser' => $proforma->findUser($workflow_id)
            );

            $invoice->workflow_id = $workflow_id;
            $workflow_item['amending'] = $invoice->getAmendStatus();

            $inv_data = $invoice->getInvInfoByWF($workflow_id);

            if (isset($inv_data)) {
                $workflow_item3['invoice_id'] = $inv_data['invoice_id'];
                $workflow_item3['InvStatus'] = $inv_data['InvStatus'];
                $workflow_item3['invRef'] = $inv_data['invRef'];
            }

            if ($inv_data['invoice_id']) {
                $lines->invoice_id = $inv_data['invoice_id'];
                $response = $lines->getStatus();

                $workflow_item['amend'] = $response['amend'];
                $workflow_item['purchase'] = $response['purchase'];
                $workflow_item['transfer'] = $response['transfer'];

                $workflow_item['amendstatus'] = $response['amendstatus'];
                $workflow_item['purchasestatus'] = $response['purchasestatus'];
                $workflow_item['transferstatus'] = $response['transferstatus'];
            }

            if ($workflow_item['status'] == 5) {
                $srv_database = new PnpServerDatabase();
                $srvdb = $srv_database->getConnection();

                if (isset($srvdb)) {
                    $invpastel = new InvNum($srvdb);
                    $invpastel->Description = str_pad($workflow_item['workflow_id'], 8, '0', STR_PAD_LEFT);

                    $invoice_num = $invpastel->fetchInvoiceByRef();

                    $workflow->workflow_id = $workflow_item['workflow_id'];
                    $workflow->invoiceNo = $invoice_num;
                    if ($workflow->update(1)) {

                    }
                }
            }

            array_push($temp_array, $workflow_item3);
        }
        
        $workflow->array_sort_by_column($temp_array, 'time', SORT_DESC);
        $workflow_arr['records'] = $temp_array;

        http_response_code(200);
        echo json_encode($workflow_arr);
    } else {
        // http_response_code(404);
        echo json_encode(
            array(
                'message' => 'No records found.',
                'records' => array()
            )
        );
    }
?>
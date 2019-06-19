<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/workflow.php';
    include_once '../objects/pastel.php';

    $database = new Database();
    $db = $database->getConnection();

    $srv_database = new ServerDatabase();
    $srvdb = $srv_database->getConnection();

    $workflow = new Workflow($db);
    $inv = new InvNum($srvdb);

    $stmt = $inv->findCompletion();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $inv_arr = array();
        $inv_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $invoice_item = array(
                'OrderDate' => $OrderDate,
                'OrderNum' => $OrderNum,
                'InvNumber' => $InvNumber,
                'Name' => $Name,
                'Account' => $Account
            );

            $extra_data = $workflow->readCompletion($InvNumber);

            if ($extra_data) {
                $invoice_item['status'] = $extra_data['step'];
                $invoice_item['workflow_id'] = $extra_data['workflow_id'];
                $invoice_item['sessionID'] = $extra_data['jobID'];
            }

            if ($extra_data) {
                if (+$extra_data['status'] > 9) {
                    unset($invoice_item);
                } else {
                    array_push($inv_arr['records'], $invoice_item);
                }
            }

        }

        http_response_code(200);
        echo json_encode($inv_arr);
    }
?>
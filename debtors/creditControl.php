<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/customer.php';
    include_once '../objects/workflow.php';
    include_once '../objects/pastel.php';

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);
    $customer = new Customer($db);

    $stmt = $workflow->creditCtrl();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $workflow_arr = array();
        $workflow_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $control_item = array(
                'workflow_id' => $workflow_id,
                'time' => $time,
                'status' => $status,
                'cust_id' => $cust_id,
                'invoice_id' => $invoice_id,
                'data' => $data,
                'step' => $step
            );

            switch (+$data) {
                case 1:
                    $control_item['company'] = 'DEL';
                    break;
                case 2:
                    $control_item['company'] = 'RNS';
                    break;
                case 3:
                    $control_item['company'] = 'PNP';
                    break;
            }

            $custdata = $customer->getCustDetails($data, $cust_id);

            $control_item['company_name'] = $custdata['company_name'];
            $control_item['customerCode'] = $custdata['customerCode'];

            switch (+$data) {
                case 1:
                    $srv_database = new DelServerDatabase();
                    $srvdb = $srv_database->getConnection();
                    break;
                case 2:
                    $srv_database = new RnsServerDatabase();
                    $srvdb = $srv_database->getConnection();
                    break;
                case 3:
                    $srv_database = new PnpServerDatabase();
                    $srvdb = $srv_database->getConnection();
                    break;
            }

            if (isset($srvdb)) {
                $client = new Client($srvdb);
                $postar = new PostAR($srvdb);
            }

            if (isset($srvdb)) {
                if (isset($control_item['customerCode'])) {
                    $link = $client->getDCLink($control_item['customerCode']);
                    $term = $client->getTerms($control_item['customerCode']);

                    $postar->AccountLink = $link;

                    if ($postar->getOutstanding($term) > 0) {
                        $control_item['amt'] = $postar->getOutstanding($term);
                        $control_item['issue'] = 'Account Over Term';
                    }
                } else {
                    $control_item['issue'] = 'Customer Code Missing';
                }
            }

            array_push($workflow_arr['records'], $control_item);
        }

        echo json_encode($workflow_arr);
    } else {
        echo json_encode(array(
            'records' => array()
        ));
    }


?>
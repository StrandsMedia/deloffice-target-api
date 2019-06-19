<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/debtors.php';
    include_once '../objects/pastel.php';

    $database = new Database();
    $db = $database->getConnection();

    $control = new DebtorsControl($db);
    $control_cmt = new DebtorsCtrlCmt($db);

    $control->dc_id = isset($_GET['id']) ? +$_GET['id'] : die();
    $data = isset($_GET['d']) ? +$_GET['d'] : die();

    switch ($data) {
        case 1:
            $srvdatabase = new DelServerDatabase();
            $srvdb = $srvdatabase->getConnection();
            break;
        case 2:
            $srvdatabase = new RnsServerDatabase();
            $srvdb = $srvdatabase->getConnection();
            break;
        case 3:
            $srvdatabase = new PnpServerDatabase();
            $srvdb = $srvdatabase->getConnection();
            break;
    }

    $stmt = $control->readOne($data);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $control_arr = array();
        $control_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $control_item = array(
                'dc_id' => $dc_id,
                'cust_id' => $cust_id,
                'customerCode' => $customerCode,
                'company_name' => $company_name,
                'agent' => $agent,
                'dispute' => isset($dispute) ? $dispute : null,
                'contact_person_acc' => isset($contact_person_acc) ? $contact_person_acc : $contact_person,
                'tel_acc' => isset($tel_acc) ? $tel_acc : $tel,
                'fax_acc' => isset($fax_acc) ? $fax_acc : $fax,
                'mob_acc' => isset($mob_acc) ? $mob_acc : $mob,
                'email_acc' => isset($email_acc) ? $email_acc : $email,
                'address' => isset($address) ? $address : null,
                'sales_rep' => $sales_rep,
                'inv' => $inv,
                'dn' => $dn,
                'crn' => $crn,
                'oth' => $oth,
                'adjs' => $adjs,
                'payment' => $payment,
                'issue' => $issue,
                'reminder' => $reminder,
                'task' => $task,
                'cheque' => $cheque,
                'debt_id' => $debt_id,
                'status' => $status,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            if (isset($data)) {
                $client = new Client($srvdb);
                switch ($data) {
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
                if ($customerCode) {
                    $client_info = $client->getInfo($customerCode);
    
                    $control_item['Physical1'] = $client_info['Physical1'];
                    $control_item['Physical2'] = $client_info['Physical2'];
                    $control_item['Physical3'] = $client_info['Physical3'];
                    $control_item['Physical4'] = $client_info['Physical4'];
                    $control_item['Physical5'] = $client_info['Physical5'];
                    $control_item['Tax_Number'] = $client_info['Tax_Number'];
                    $control_item['Registration'] = $client_info['Registration'];
                }
            }

            $control_item['comments'] = array();

            $cmts = $control_cmt->getComments($dc_id);

            if ($cmts->rowCount() > 0) {
                while ($row2 = $cmts->fetch(PDO::FETCH_ASSOC)) {
                    $comment_item = array(
                        'sales_rep' => $row2['sales_rep'],
                        'dc_comment' => $row2['dc_comment'],
                        'createdAt' => $row2['createdAt'],
                    );

                    array_push($control_item['comments'], $comment_item);
                }
            }

            array_push($control_arr['records'], $control_item);
        }

        http_response_code(200);
        echo json_encode($control_arr);
    } else {
        http_response_code(400);
        echo json_encode(array(
            'message' => 'No records found.',
            'records' => array()
        ));
    }
    
?>
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

    $collect = new DebtCollect($db);

    $remn = new DebtReminder($db);

    $keywords = isset($_GET['s']) ? $_GET['s'] : die();

    $stmt1 = $control->search($keywords, 1);
    $stmt2 = $control->search($keywords, 2);
    $stmt3 = $control->search($keywords, 3);

    $num = $stmt1->rowCount() + $stmt2->rowCount() + $stmt3->rowCount();

    if ($num > 0) {
        $control_arr = array();
        $control_arr['records'] = array();

        $temp_array = array();

        while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $control_item = array(
                'dc_id' => $dc_id,
                'cust_id' => $cust_id,
                'customerCode' => $customerCode,
                'company_name' => $company_name,
                'company' => 'DEL',
                'data' => $data,
                'agent' => $agent,
                'dispute' => $dispute,
                'contact_person_acc' => $contact_person_acc,
                'tel_acc' => $tel_acc,
                'fax_acc' => $fax_acc,
                'mob_acc' => $mob_acc,
                'email_acc' => $email_acc,
                'address' => isset($address) ? $address : null,
                'sales_rep' => $sales_rep,
                'inv' => $inv,
                'dn' => $dn,
                'crn' => $crn,
                'oth' => $oth,
                'adjs' => $adjs,
                'issue' => $issue,
                'reminder' => $reminder,
                'task' => $task,
                'cheque' => $cheque,
                'debt_id' => $debt_id,
                'status' => $status,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                'comment' => $control_cmt->getLastComment($dc_id),
                'reminder_procedure' => $remn->readAvailable(1, $cust_id)
            );

            if (isset($control_item['debt_id']) && $control_item['debt_id'] !== '') {
                $control_item['collect'] = $collect->readActiveEntry($debt_id);
            }

            array_push($temp_array, $control_item);
        }
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            extract($row2);

            $control_item2 = array(
                'dc_id' => $dc_id,
                'cust_id' => $cust_id,
                'customerCode' => $customerCode,
                'company_name' => $company_name,
                'company' => 'RNS',
                'data' => $data,
                'agent' => $agent,
                'dispute' => $dispute,
                'contact_person_acc' => $contact_person_acc,
                'tel_acc' => $tel_acc,
                'fax_acc' => $fax_acc,
                'mob_acc' => $mob_acc,
                'email_acc' => $email_acc,
                'address' => $address,
                'sales_rep' => $sales_rep,
                'inv' => $inv,
                'dn' => $dn,
                'crn' => $crn,
                'oth' => $oth,
                'adjs' => $adjs,
                'issue' => $issue,
                'reminder' => $reminder,
                'task' => $task,
                'cheque' => $cheque,
                'debt_id' => $debt_id,
                'status' => $status,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                'comment' => $control_cmt->getLastComment($dc_id),
                'reminder_procedure' => $remn->readAvailable(2, $cust_id)
            );

            if (isset($control_item2['debt_id']) && $control_item2['debt_id'] !== '') {
                $control_item2['collect'] = $collect->readActiveEntry($debt_id);
            }

            array_push($temp_array, $control_item2);
        }
        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            extract($row3);

            $control_item3 = array(
                'dc_id' => $dc_id,
                'cust_id' => $cust_id,
                'customerCode' => $customerCode,
                'company_name' => $company_name,
                'company' => 'PNP',
                'data' => $data,
                'agent' => $agent,
                'dispute' => $dispute,
                'contact_person_acc' => $contact_person_acc,
                'tel_acc' => $tel_acc,
                'fax_acc' => $fax_acc,
                'mob_acc' => $mob_acc,
                'email_acc' => $email_acc,
                'address' => $address,
                'sales_rep' => $sales_rep,
                'inv' => $inv,
                'dn' => $dn,
                'crn' => $crn,
                'oth' => $oth,
                'adjs' => $adjs,
                'issue' => $issue,
                'reminder' => $reminder,
                'task' => $task,
                'cheque' => $cheque,
                'debt_id' => $debt_id,
                'status' => $status,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                'comment' => $control_cmt->getLastComment($dc_id),
                'reminder_procedure' => $remn->readAvailable(3, $cust_id),

            );

            if (isset($control_item3['debt_id']) && $control_item3['debt_id'] !== '') {
                $control_item3['collect'] = $collect->readActiveEntry($debt_id);
            }

            array_push($temp_array, $control_item3);
        }

        $control->array_sort_by_column($temp_array, 'dc_id', SORT_DESC);
        $control_arr['records'] = $temp_array;

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
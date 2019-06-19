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

    $reminder = new DebtReminder($db);
    $dbtcomment = new DebtReminderComment($db);

    $reminder->status = isset($_GET['s']) ? $_GET['s'] : die();

    $stmt1 = $reminder->readArchive(1);
    $stmt2 = $reminder->readArchive(2);
    $stmt3 = $reminder->readArchive(3);
    $num = $stmt1->rowCount() + $stmt2->rowCount() + $stmt3->rowCount();

    if ($num > 0) {
        $reminder_arr = array();
        $reminder_arr['records'] = array();

        $temp_array = array();

        while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $reminder_item = array(
                'dbt_rem_id' => $dbt_rem_id,
                'cust_id' => $cust_id,
                'company_name' => $company_name,
                'company' => 'DEL',
                'customerCode' => $customerCode,
                'amt' => $amt,
                'amtpaid' => $amtpaid,
                'comment' => $dbtcomment->readLast($dbt_rem_id),
                'status' => $status,
                'courtstatus' => $courtstatus,
                'sentDate' => $sentDate,
                'courtDate' => $courtDate,
                'data' => $data
            );

            if ($customerCode) {
                $srv_database = new DelServerDatabase();
                $srvdb = $srv_database->getConnection();

                $client = new Client($srvdb);

                $client_info = $client->getInfo($customerCode);

                $reminder_item['Physical1'] = $client_info['Physical1'];
                $reminder_item['Physical2'] = $client_info['Physical2'];
                $reminder_item['Physical3'] = $client_info['Physical3'];
                $reminder_item['Physical4'] = $client_info['Physical4'];
                $reminder_item['Physical5'] = $client_info['Physical5'];
                $reminder_item['Tax_Number'] = $client_info['Tax_Number'];
                $reminder_item['Registration'] = $client_info['Registration'];
            }

            array_push($temp_array, $reminder_item);
        }
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            extract($row2);
            $reminder_item = array(
                'dbt_rem_id' => $dbt_rem_id,
                'cust_id' => $cust_id,
                'company_name' => $company_name,
                'company' => 'RNS',
                'customerCode' => $customerCode,
                'amt' => $amt,
                'amtpaid' => $amtpaid,
                'comment' => $comment,
                'status' => $status,
                'courtstatus' => $courtstatus,
                'sentDate' => $sentDate,
                'courtDate' => $courtDate,
                'data' => $data
            );

            if ($customerCode) {
                $srv_database = new RnsServerDatabase();
                $srvdb = $srv_database->getConnection();

                $client = new Client($srvdb);

                $client_info = $client->getInfo($customerCode);

                $reminder_item['Physical1'] = $client_info['Physical1'];
                $reminder_item['Physical2'] = $client_info['Physical2'];
                $reminder_item['Physical3'] = $client_info['Physical3'];
                $reminder_item['Physical4'] = $client_info['Physical4'];
                $reminder_item['Physical5'] = $client_info['Physical5'];
                $reminder_item['Tax_Number'] = $client_info['Tax_Number'];
                $reminder_item['Registration'] = $client_info['Registration'];
            }

            array_push($temp_array, $reminder_item);
        }
        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            extract($row3);
            $reminder_item = array(
                'dbt_rem_id' => $dbt_rem_id,
                'cust_id' => $cust_id,
                'company_name' => $company_name,
                'company' => 'PNP',
                'customerCode' => $customerCode,
                'amt' => $amt,
                'amtpaid' => $amtpaid,
                'comment' => $comment,
                'status' => $status,
                'courtstatus' => $courtstatus,
                'sentDate' => $sentDate,
                'courtDate' => $courtDate,
                'data' => $data
            );

            if ($customerCode) {
                $srv_database = new PnpServerDatabase();
                $srvdb = $srv_database->getConnection();

                $client = new Client($srvdb);

                $client_info = $client->getInfo($customerCode);

                $reminder_item['Physical1'] = $client_info['Physical1'];
                $reminder_item['Physical2'] = $client_info['Physical2'];
                $reminder_item['Physical3'] = $client_info['Physical3'];
                $reminder_item['Physical4'] = $client_info['Physical4'];
                $reminder_item['Physical5'] = $client_info['Physical5'];
                $reminder_item['Tax_Number'] = $client_info['Tax_Number'];
                $reminder_item['Registration'] = $client_info['Registration'];
            }

            array_push($temp_array, $reminder_item);
        }

        $reminder->array_sort_by_column($temp_array, 'sentDate', SORT_DESC);
        $reminder_arr['records'] = $temp_array;

        http_response_code(200);
        echo json_encode($reminder_arr);
    } else {
        http_response_code(404);
        echo json_encode(array(
            'message' => 'No entries found.',
            'records' => array()
        ));
    }

?>
<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/debtors.php';

    $database = new Database();
    $db = $database->getConnection();

    $collect = new DebtCollect($db);

    $collect->collected = isset($_GET['c']) ? $_GET['c'] : die();
    $collect->type = isset($_GET['t']) ? $_GET['t'] : die();

    $stmt1 = $collect->readEntries(1);
    $stmt2 = $collect->readEntries(2);
    $stmt3 = $collect->readEntries(3);

    $num = $stmt1->rowCount() + $stmt2->rowCount() + $stmt3->rowCount();

    if ($num > 0) {
        $collect_arr = array();
        $collect_arr['records'] = array();

        $temp_array = array();

        while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $collect_item1 = array(
                'company_name' => $company_name . ' (DEL)',
                'contact_person' => $contact_person,
                'tel' => $tel,
                'address' => $address,
                'cust_id' => $cust_id,
                'debt_id' => $debt_id,
                'pay_method' => $pay_method,
                'delivery_pay' => $delivery_pay,
                'collected' => $collected,
                'comment' => $comment,
                'type' => $type,
                'amount' => $amount,
                'remarks' => $remarks,
                'region' => $region,
                'data' => $data,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($temp_array, $collect_item1);

        }
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            extract($row2);

            $collect_item2 = array(
                'company_name' => $company_name . ' (RNS)',
                'contact_person' => $contact_person,
                'tel' => $tel,
                'address' => $address,
                'cust_id' => $cust_id,
                'debt_id' => $debt_id,
                'pay_method' => $pay_method,
                'delivery_pay' => $delivery_pay,
                'collected' => $collected,
                'comment' => $comment,
                'type' => $type,
                'amount' => $amount,
                'remarks' => $remarks,
                'region' => $region,
                'data' => $data,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($temp_array, $collect_item2);
        }
        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            extract($row3);

            $collect_item3 = array(
                'company_name' => $company_name . ' (PNP)',
                'contact_person' => $contact_person,
                'tel' => $tel,
                'address' => $address,
                'cust_id' => $cust_id,
                'debt_id' => $debt_id,
                'pay_method' => $pay_method,
                'delivery_pay' => $delivery_pay,
                'collected' => $collected,
                'comment' => $comment,
                'type' => $type,
                'amount' => $amount,
                'remarks' => $remarks,
                'region' => $region,
                'data' => $data,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($temp_array, $collect_item3);
        }

        $collect->array_sort_by_column($temp_array, 'createdAt', SORT_DESC);
        $collect_arr['records'] = $temp_array;

        http_response_code(200);
        echo json_encode($collect_arr);
    } else {
        // http_response_code(404);
        echo json_encode(array(
            'message' => 'No entries found.',
            'records' => array()
        ));
    }
?>
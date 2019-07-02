<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/customer.php';

    $database = new Database();
    $db = $database->getConnection();

    $customer = new DelCustomer($db);
    $customer2 = new RnsCustomer($db);
    $customer3 = new PnpCustomer($db);
    
    $keywords = isset($_GET["s"]) ? $_GET["s"] : null;

    $stmt = $customer->read($keywords);
    $stmt2 = $customer2->read($keywords);
    $stmt3 = $customer3->read($keywords);

    $num = $stmt->rowCount() + $stmt2->rowCount() + $stmt3->rowCount();

    if ($num > 0) {
        $customer_arr = array();
        $customer_arr['records'] = array();

        $temp_array = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $customer_item = array(
                'cust_id' => $cust_id,
                'company_name' => $company_name,
                'company' => 'DEL',
                'contact_person' => $contact_person,
                'tel' => $tel,
                'fax' => $fax,
                'mob' => $mob,
                'email' => $email,
                'updatedAt' => $updatedAt,
                'notes' => html_entity_decode($notes),
                'data' => 1
            );

            array_push($temp_array, $customer_item);
        }

        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            extract($row2);

            $customer_item2 = array(
                'cust_id' => $cust_id,
                'company_name' => $company_name,
                'company' => 'RNS',
                'contact_person' => $contact_person,
                'tel' => $tel,
                'fax' => $fax,
                'mob' => $mob,
                'email' => $email,
                'updatedAt' => $updatedAt,
                'notes' => html_entity_decode($notes),
                'data' => 2
            );

            array_push($temp_array, $customer_item2);
        }
        
        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            extract($row3);

            $customer_item3 = array(
                'cust_id' => $cust_id,
                'company_name' => $company_name,
                'company' => 'PNP',
                'contact_person' => $contact_person,
                'tel' => $tel,
                'fax' => $fax,
                'mob' => $mob,
                'email' => $email,
                'updatedAt' => $updatedAt,
                'notes' => html_entity_decode($notes),
                'data' => 3
            );

            array_push($temp_array, $customer_item3);
        }

        $customer->array_sort_by_column($temp_array, 'updatedAt', SORT_DESC);
        $customer_arr['records'] = $temp_array;

        http_response_code(200);
        echo json_encode($customer_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
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

    $keywords = isset($_GET["s"]) ? $_GET["s"] : null;

    $stmt = $customer->read($keywords);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $customer_arr = array();
        $customer_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $customer_item = array(
                'cust_id' => $cust_id,
                'company_name' => $company_name,
                'contact_person' => $contact_person,
                'tel' => $tel,
                'fax' => $fax,
                'mob' => $mob,
                'email' => $email,
                'updatedAt' => $updatedAt,
                'notes' => html_entity_decode($notes)
            );

            array_push($customer_arr['records'], $customer_item);
        }

        echo json_encode($customer_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
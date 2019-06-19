<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');

    include_once '../config/db.php';
    include_once '../objects/customer.php';

    $database = new Database();
    $db = $database->getConnection();

    $customer = new DelCustomer($db);

    $keywords = isset($_GET["s"]) ? $_GET["s"] : "";

    $stmt = $customer->search($keywords);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $customer_arr = array();
        $customer_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $customer_item = array(
                'cust_id' => $cust_id,
                'company_name' => $company_name,
                'customerCode' => $customerCode
            );

            array_push($customer_arr['records'], $customer_item);
        }

        echo json_encode($customer_arr);
    } else {
        echo json_encode(
            array('message' => 'No results found. Try again.')
        );
    }
?>
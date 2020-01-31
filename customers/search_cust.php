<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');

    include_once '../config/db.php';
    include_once '../objects/customer.php';
    include_once '../objects/pastel.php';

    $database = new Database();
    $db = $database->getConnection();

    if (isset($_GET['d'])) {
       switch ($_GET['d']) {
            case '1':
                $srv_database = new DelServerDatabase();
                $srvdb = $srv_database->getConnection();
                break;
            case '2':
                $srv_database = new RnsServerDatabase();
                $srvdb = $srv_database->getConnection();
                break;
            case '3':
                $srv_database = new PnpServerDatabase();
                $srvdb = $srv_database->getConnection();
                break;
       }
       switch ($_GET['d']) {
            case '1':
                $customer = new DelCustomer($db);
                break;
            case '2':
                $customer = new RnsCustomer($db);
                break;
            case '3':
                $customer = new PnpCustomer($db);
                break;
       }

       $client = new Client($srvdb);
       
    } else {
        $srv_database = new ServerDatabase();
        $srvdb = $srv_database->getConnection();

        $customer = new DelCustomer($db);
        $client = new Client($srvdb);
    }

    $keywords = isset($_GET["s"]) ? $_GET["s"] : "";

    $stmt = $customer->searchCust($keywords);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $customer_arr = array();
        $customer_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $customer_item = array(
                'cust_id' => $cust_id,
                'company_name' => $company_name,
                'customerCode' => $customerCode,
                'address' => $address
            );

            if (!isset($_GET['d'])) {
                if ($customerCode) {
                    $client_info = $client->getInfo($customerCode);
    
                    $customer_item['Physical1'] = $client_info['Physical1'];
                    $customer_item['Physical2'] = $client_info['Physical2'];
                    $customer_item['Physical3'] = $client_info['Physical3'];
                    $customer_item['Physical4'] = $client_info['Physical4'];
                    $customer_item['Physical5'] = $client_info['Physical5'];
                    $customer_item['Tax_Number'] = $client_info['Tax_Number'];
                    $customer_item['Registration'] = $client_info['Registration'];
                }
            } else {
                switch ($_GET['d']) {
                    case '1':
                        $customer_item['company'] = 'DEL';
                        $customer_item['data'] = 1;
                        break;
                    case '2':
                        $customer_item['company'] = 'RNS';
                        $customer_item['data'] = 2;
                        break;
                    case '3':
                        $customer_item['company'] = 'PNP';
                        $customer_item['data'] = 3;
                        break;
                }

                if ($customerCode) {
                    $client_info = $client->getInfo($customerCode);
    
                    $customer_item['Physical1'] = $client_info['Physical1'];
                    $customer_item['Physical2'] = $client_info['Physical2'];
                    $customer_item['Physical3'] = $client_info['Physical3'];
                    $customer_item['Physical4'] = $client_info['Physical4'];
                    $customer_item['Physical5'] = $client_info['Physical5'];
                    $customer_item['Tax_Number'] = $client_info['Tax_Number'];
                    $customer_item['Registration'] = $client_info['Registration'];
                }
            }

            array_push($customer_arr['records'], $customer_item);
        }

        echo json_encode($customer_arr);
    } else {
        echo json_encode(
            array('message' => 'No results found. Try again.')
        );
    }
?>
<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    function date_compare($a, $b)
    {
        $t1 = strtotime($a['lastorderdate']);
        $t2 = strtotime($b['lastorderdate']);
        return $t2 - $t1;
    }

    include_once '../../config/db.php';
    include_once '../../objects/comments.php';
    include_once '../../objects/customer.php';
    include_once '../../objects/pastel.php';
    include_once '../../objects/products.php';

    $database = new Database();
    $db = $database->getConnection();

    $srv_database = new DelServerDatabase();
    $srvdb = $srv_database->getConnection();

    $customer = new DelCustomer($db);
    $cmt = new SalesComment($db);
    $productdb = new Products($db);

    $client = new Client($srvdb);
    $inv = new InvNum($srvdb);

    $customer->sector = isset($_GET['s']) ? $_GET['s'] : die();
    $customer->subsector = isset($_GET['ss']) ? $_GET['ss'] : null;
    $stmt = $customer->findBySector();

    if ($stmt->rowCount() > 0) {
        $data_arr = array();
        $data_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $inv->AccountID = $client->getDCLink($customerCode);

            $data_item = array(
                'cust_id' => $cust_id,
                'Account' => $customerCode,
                'Name' => $company_name,
                'DCLink' => $inv->AccountID
            );

            $com_item = $cmt->readLastComment(+$data_item['cust_id']);

            $cat = isset($_GET['c']) ? $_GET['c'] : null;
            $cat2 = isset($_GET['sc']) ? $_GET['sc'] : null;

            $product_array = "";

            if (isset($cat)) {
                $product_array = $productdb->getProductCodesAlt($cat, $cat2);
            }

            $add_item = $inv->fetchMktReport($product_array);

            if (isset($add_item)) {
                $data_item['current'] = $add_item['current'];
                $data_item['thirty'] = $add_item['thirty'];
                $data_item['sixty'] = $add_item['sixty'];
                $data_item['lastorderdate'] = $add_item['lastorderdate'];
            }

            if (isset($com_item)) {
                $data_item['comment'] = $com_item;
            }

            array_push($data_arr['records'], $data_item);
        }

        usort($data_arr['records'], 'date_compare');

        http_response_code(200);
        echo json_encode($data_arr);
    } else {
        http_response_code(404);
        echo json_encode(array(
            'status' => 'error',
            'message' => 'No records found',
            'records' => array()
        ));
    }


    // if (isset($_GET['d'])) {
    //     switch ($_GET['d']) {
    //         case '1':
    //             $database = new DelServerDatabase();
    //             $db = $database->getConnection();
    //             break;
    //         case '2':
    //             $database = new RnsServerDatabase();
    //             $db = $database->getConnection();
    //             break;
    //         case '3':
    //             $database = new PnpServerDatabase();
    //             $db = $database->getConnection();
    //             break;
    //     }
    // } else {
    //     $database = new DelServerDatabase();
    //     $db = $database->getConnection();
    // }

    // $inv = new InvNum($db);

    // $inv->AccountID = isset($_GET['id']) ? $_GET['id'] : die();

    // $stmt = $inv->fetchMktReport();
    // $num = $stmt->rowCount();

    // $data_arr = array();
    // $data_arr['records'] = array();

    // while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //     extract($row);

    //     $data_item = array(
    //         'DCLink' => $DCLink,
    //         'Account' => $Account,
    //         'Name' => $Name,
    //         'current' => $current,
    //         'thirty' => $thirty,
    //         'sixty' => $sixty,
    //         'lastorderdate' => $lastorderdate
    //     );

    //     array_push($data_arr['records'], $data_item);
    // }

    // echo json_encode($data_arr);
?>
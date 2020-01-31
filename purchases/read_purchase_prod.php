<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/purchases.php';
    include_once '../objects/products.php';
    include_once '../objects/customer.php';

    $database = new Database();
    $db = $database->getConnection();

    $reqs = new PurchaseRequest($db);
    $reqprods = new PurchaseRequestProds($db);

    $prchs = new PurchaseInit($db);
    $prchsprods = new PurchaseInitProds($db);

    $customer = new Customer($db);
    $product = new Products($db);

    $reqprods->p_id = isset($_GET['id']) ? $_GET['id'] : die();
    $type = isset($_GET['s']) ? $_GET['s'] : die();

    $data_arr = array();
    $data_arr['requests'] = array();

    $stmt1 = $reqprods->readByProd($type);

    if ($stmt1->rowCount() > 0) {
        while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {

            $request_item = array(
                'reqprod_id' => $row1['reqprod_id'],
                'req_id' => $row1['req_id'],
                'p_id' => $row1['p_id'],
                'des1' => $row1['des1'],
                'des2' => $row1['des2'],
                'des3' => $row1['des3'],
                'qty' => $row1['qty'],
                'createdAt' => $row1['createdAt'],
                'updatedAt' => $row1['updatedAt'],
                'cust_id' => $row1['cust_id'],
                'data' => $row1['data'],
                'workflow_id' => $row1['workflow_id'],
                'completed' => $row1['completed'],
                'invlineid' => $row1['invlineid']
            );

            $custdata = $customer->getCustDetails(+$row1['data'], +$row1['cust_id']);

            $request_item['customerCode'] = $custdata['customerCode'];
            $request_item['company_name'] = $custdata['company_name'];
            $request_item['address'] = $custdata['address'];
            $request_item['tel'] = $custdata['tel'];
            $request_item['contact_person'] = $custdata['contact_person'];


            array_push($data_arr['requests'], $request_item);
        }
    }

    http_response_code(200);

    echo json_encode($data_arr);
?>
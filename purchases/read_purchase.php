<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/purchases.php';
    include_once '../objects/products.php';
    include_once '../objects/invoice.php';
    include_once '../objects/customer.php';

    $database = new Database();
    $db = $database->getConnection();

    $reqs = new PurchaseRequest($db);
    $reqprods = new PurchaseRequestProds($db);

    $prchs = new PurchaseInit($db);
    $prchsprods = new PurchaseInitProds($db);

    $customer = new Customer($db);
    $product = new Products($db);

    $invlines = new InvoiceLines($db);

    
    $reqprods->type = isset($_GET['type']) ? +$_GET['type'] + 1 : 2;
    $reqprods->completed = isset($_GET['status']) ? +$_GET['status'] - 1 : 0;
    
    if ($reqprods->completed == 0) {
        $data_arr = array();
        $data_arr['requests'] = array();

        $stmt1 = $reqprods->read();
    
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
    } elseif ($reqprods->completed == 1) {
        $data_arr = array();
        $data_arr['awaiting'] = array();

        $stmt1 = $reqprods->readAllProd();

        if ($stmt1->rowCount() > 0) {
            while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                extract ($row);
                $prod_item = array(
                    'p_id' => $p_id,
                    'des1' => $des1,
                    'des2' => $des2,
                    'des3' => $des3,
                    'counter' => $counter,
                    'total' => $total
                );

                $prod_item['entries'] = array();


                $entries = $reqs->readEntries($p_id, 1);

                if ($entries->rowCount() > 0) {
                    while ($row2 = $entries->fetch(PDO::FETCH_ASSOC)) {
                        $entry_item = array(
                            'req_id' => $row2['req_id'],
                            'cust_id' => $row2['cust_id'],
                            'workflow_id' => $row2['workflow_id'],
                            'data' => $row2['data'],
                            'type' => $row2['type'],
                            'completed' => $row2['completed'],
                            'qty' => $row2['qty'],
                            'invlineid' => $row2['invlineid']
                        );

                        $custdata = $customer->getCustDetails(+$row2['data'], +$row2['cust_id']);

                        $entry_item['customerCode'] = $custdata['customerCode'];
                        $entry_item['company_name'] = $custdata['company_name'];
                        $entry_item['address'] = $custdata['address'];
                        $entry_item['tel'] = $custdata['tel'];
                        $entry_item['contact_person'] = $custdata['contact_person'];

                        array_push($prod_item['entries'], $entry_item);
                    }
                }

                array_push($data_arr['awaiting'], $prod_item);
            }   
        }

        http_response_code(200);
    
        echo json_encode($data_arr);
    } elseif ($reqprods->completed == 2 || $reqprods->completed == 3 || $reqprods->completed == 4) {
        $data_arr = array();
        $data_arr['requests'] = array();

        $stmt1 = $reqprods->read2();
    
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
                    'completed' => $row1['completed']
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
    }

?>
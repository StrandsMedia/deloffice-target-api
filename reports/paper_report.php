<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/pastel.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $srvdatabase = new DelServerDatabase();
    $srvdb = $srvdatabase->getConnection();

    $stk = new PostST($srvdb);

    $workflow = new Workflow($db);
    $details = new WorkflowDetails($db);

    $stmt = $workflow->getData();

    $response_arr = array();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract ($row);

            $wf_item = array(
                'invoiceNo' => $invoiceNo
            );

            $wf_item['products'] = $details->readProducts($invoiceNo);

            if (isset($wf_item['products']) && sizeof($wf_item['products']) > 0) {

                $stk->Reference = $invoiceNo;
                $stmt3 = $stk->getProducts2();

                $wf_item['svrproducts'] = array();

                $num3 = $stmt3->rowCount();

                if ($num3 > 0) {
                    while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                        $prod = array(
                            'code' => $row3['cSimpleCode'],
                            'brand' => $row3['Description_3'],
                            'qty' => +$row3['Quantity']
                        );
                        array_push($wf_item['svrproducts'], $prod);
                    }
                } 


                array_push($response_arr, $wf_item);
            }
        }
    }

    echo json_encode($response_arr);
?>
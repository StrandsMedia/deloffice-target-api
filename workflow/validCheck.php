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

    $inv = new InvNum($srvdb);
    $stk = new PostST($srvdb);

    $workflow = new Workflow($db);

    $invoice = isset($_GET['inv']) ? $_GET['inv'] : die();

    $somarr = array();

    $workflow->invoiceNo = $invoice;
    $stmt = $workflow->findInvNum(1);
    $num = $stmt->rowCount();
    
    $stmt_2 = $workflow->findInvNum(2);
    $num_2 = $stmt_2->rowCount();

    $stmt_3 = $workflow->findInvNum(3);
    $num_3 = $stmt_3->rowCount();

    if ($num > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $item = array(
                'workflow_id' => $workflow_id,
                'invoiceNo' => $invoiceNo,
                'company_name' => $company_name,
                'customerCode' => $customerCode,
                'step' => $step
            );
    
            $inv->InvNumber = $invoiceNo;
    
            $stmt2 = $inv->checkInvoice();
            $num2 = $stmt2->rowCount();

            if ($num2 > 0) {
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $item['InvNumber'] = $row2['InvNumber'];

                    if ($item['InvNumber'] === '') {
                        $item['InvNumber'] = $row2['OrderNum'];
                    }

                    $item['Account'] = $row2['Account'];
                    $item['Name'] = $row2['Name'];
                }
            }

            $stk->Reference = $invoiceNo;
    
            $item['products'] = array();
    
            $stmt3 = $stk->getProducts();
            $num3 = $stmt3->rowCount();

            if ($num3 > 0) {
                while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                    $prod_item = array(
                        'Description_1' => $row3['Description_1'],
                        'Description_2' => $row3['Description_2'],
                        'Description_3' => $row3['Description_3'],
                        'Quantity' => $row3['Quantity']
                    );
                    array_push($item['products'], $prod_item);
                }
            } 
    
    
            array_push($somarr, $item);
        }
        http_response_code(200);
        echo json_encode($somarr);
    } else if ($num_2 > 0) {
        while ($row = $stmt_2->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $item = array(
                'workflow_id' => $workflow_id,
                'invoiceNo' => $invoiceNo,
                'company_name' => $company_name,
                'customerCode' => $customerCode,
                'step' => $step
            );
    
            $inv->InvNumber = $invoiceNo;
    
            $stmt2 = $inv->checkInvoice();
            $num2 = $stmt2->rowCount();

            if ($num2 > 0) {
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $item['InvNumber'] = $row2['InvNumber'];

                    if ($item['InvNumber'] === '') {
                        $item['InvNumber'] = $row2['OrderNum'];
                    }

                    $item['Account'] = $row2['Account'];
                    $item['Name'] = $row2['Name'];
                }
            }

            $stk->Reference = $invoiceNo;
    
            $item['products'] = array();
    
            $stmt3 = $stk->getProducts();
            $num3 = $stmt3->rowCount();

            if ($num3 > 0) {
                while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                    $prod_item = array(
                        'Description_1' => $row3['Description_1'],
                        'Description_2' => $row3['Description_2'],
                        'Description_3' => $row3['Description_3'],
                        'Quantity' => $row3['Quantity']
                    );
                    array_push($item['products'], $prod_item);
                }
            } 
    
    
            array_push($somarr, $item);
        }
        http_response_code(200);
        echo json_encode($somarr);
    } else if ($num_3 > 0) {
        while ($row = $stmt_3->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $item = array(
                'workflow_id' => $workflow_id,
                'invoiceNo' => $invoiceNo,
                'company_name' => $company_name,
                'customerCode' => $customerCode,
                'step' => $step
            );
    
            $inv->InvNumber = $invoiceNo;
    
            $stmt2 = $inv->checkInvoice();
            $num2 = $stmt2->rowCount();

            if ($num2 > 0) {
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $item['InvNumber'] = $row2['InvNumber'];

                    if ($item['InvNumber'] === '') {
                        $item['InvNumber'] = $row2['OrderNum'];
                    }

                    $item['Account'] = $row2['Account'];
                    $item['Name'] = $row2['Name'];
                }
            }

            $stk->Reference = $invoiceNo;
    
            $item['products'] = array();
    
            $stmt3 = $stk->getProducts();
            $num3 = $stmt3->rowCount();

            if ($num3 > 0) {
                while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                    $prod_item = array(
                        'Description_1' => $row3['Description_1'],
                        'Description_2' => $row3['Description_2'],
                        'Description_3' => $row3['Description_3'],
                        'Quantity' => $row3['Quantity']
                    );
                    array_push($item['products'], $prod_item);
                }
            } 
    
    
            array_push($somarr, $item);
        }
        http_response_code(200);
        echo json_encode($somarr);
    } else {
        http_response_code(200);
        echo json_encode(array(
            'message' => 'Invoice Not Found'
        ));
    }

?>
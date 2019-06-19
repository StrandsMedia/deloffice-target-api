<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/workflow.php';
    include_once '../objects/invoice.php';
    include_once '../objects/printing.php';
    include_once '../objects/tender.php';

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);
    $details = new WorkflowDetails($db);
    $lines = new InvoiceLines($db);
    $printing = new Printing($db);
    $tender = new Tender($db);

    $workflow->cust_id = isset($_GET['s']) ? $_GET['s'] : die();
    $printing->custid = isset($_GET['s']) ? $_GET['s'] : die();
    $tender->cust_id = isset($_GET['s']) ? $_GET['s'] : die();

    $stmt_wf = $workflow->readByCust();
    $stmt_pr = $printing->readByCust();
    $stmt_td = $tender->readByCust();

    $num = $stmt_wf->rowCount();
    $num2 = $stmt_pr->rowCount();
    $num3 = $stmt_td->rowCount();

    $workflow_arr = array();
    $workflow_arr['workflow'] = array();
    $workflow_arr['printing'] = array();
    $workflow_arr['tenders'] = array();

    if ($num > 0) {
        while ($row = $stmt_wf->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
    
            $workflow_item = array(
                'workflow_id' => $workflow_id,
                'time' => $time,
                'cust_id' => $cust_id,
                'company_name' => $company_name,
                'step' => $step
            );
    
            $workflow_item['product'] = $details->getProducts($workflow_id) !== null ? $details->getProducts($workflow_id) : $lines->getProducts($workflow_id);
    
            array_push($workflow_arr['workflow'], $workflow_item);
        }
    }

    if ($num2 > 0) {
        while ($row2 = $stmt_pr->fetch(PDO::FETCH_ASSOC)) {
            extract($row2);
    
            $printing_item = array(
                'job_id' => $job_id,
                'product' => $product,
                'paperspecs' => $paperspecs,
                'qtyorder' => $qtyorder
            );
    
            array_push($workflow_arr['printing'], $printing_item);
        }
    }

    if ($num3 > 0) {
        while ($row3 = $stmt_td->fetch(PDO::FETCH_ASSOC)) {
            extract($row3);
    
            $tender_item = array(
                'tid' => $tid,
                'product' => $product,
                'estimated_quantity' => $estimated_quantity,
                'schedule' => $schedule
            );
    
            array_push($workflow_arr['tenders'], $tender_item);
        }
    }

    echo json_encode($workflow_arr);

?>
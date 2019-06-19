<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $delivery = new WorkflowDelivery($db);

    $data = json_decode(file_get_contents('php://input'));

    if (isset($data)) {
        $delivery->date1 = isset($data->date1) ? $data->date1 : null;
        $delivery->date2 = isset($data->date2) ? $data->date2 : null;
        
        $delivery->company_name = isset($data->company_name) ? $data->company_name : null;
    
        $delivery->invoice_no = isset($data->invoice_no) ? $data->invoice_no : null;
    
        $stmt = $delivery->readArchive();
        $num = $stmt->rowCount();
    
        if ($num > 0) {
            $delivery_arr = array();
            $delivery_arr['records'] = array();
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
    
                $delivery_item = array(
                    'workflow_id' => $workflow_id,
                    'time' => $time,
                    'company_name' => $company_name,
                    'delivery_status' => $delivery_status,
                    'cust_id' => $cust_id,
                    'purchase' => $purchase,
                    'invoice_no' => $invoice_no,
                    'credit_no' => $credit_no,
                    'vehicle' => $vehicle,
                    'jobID' => $jobID
                );
    
                array_push($delivery_arr['records'], $delivery_item);
            }
    
            echo json_encode($delivery_arr);
        } else {
            echo json_encode(
                array('message' => 'No records found.')
            );
        }
    }

?>
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
    
        $stmt = $delivery->readArchive(1);
        $stmt2 = $delivery->readArchive(2);
        $stmt3 = $delivery->readArchive(3);
        $num = $stmt->rowCount() + $stmt2->rowCount() + $stmt3->rowCount();
    
        if ($num > 0) {
            $delivery_arr = array();
            $delivery_arr['records'] = array();

            $temp_array = array();
    
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
    
                array_push($temp_array, $delivery_item);
            }
            while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                extract($row2);
    
                $delivery_item2 = array(
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
    
                array_push($temp_array, $delivery_item2);
            }
            while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                extract($row3);
    
                $delivery_item3 = array(
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
    
                array_push($temp_array, $delivery_item3);
            }

            $delivery->array_sort_by_column($temp_array, 'time', SORT_DESC);
            $delivery_arr['records'] = $temp_array;
    
            http_response_code(200);
            echo json_encode($delivery_arr);
        } else {
            echo json_encode(
                array('message' => 'No records found.')
            );
        }
    }

?>
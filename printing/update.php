<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/printing.php';

    $database = new Database();
    $db = $database->getConnection();

    $printing = new Printing($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $printing->job_id = $data->job_id;
    
        $printing->custid = $data->custid;
        $printing->product = $data->product;
        $printing->printwork = $data->printwork;
        $printing->startdate = $data->startdate;
        $printing->enddate = $data->enddate;
        $printing->status = $data->status;
        $printing->jobdesc = $data->jobdesc;
        $printing->paperspecs = $data->paperspecs;
        $printing->filename = $data->filename;
        $printing->pc = $data->pc;
        $printing->printer = $data->printer;
        $printing->printsetting = $data->printsetting;
        $printing->qtyorder = $data->qtyorder;
        $printing->qtyconsumed = $data->qtyconsumed;
        $printing->qtycompleted = $data->qtycompleted;
        $printing->qtyrejected = $data->qtyrejected;
        $printing->remarks = $data->remarks;
        $printing->printedby = $data->printedby;
        $printing->supervisedby = $data->supervisedby;
        $printing->deliverydate = $data->deliverydate;
        $printing->dimensions = $data->dimensions;
        $printing->ppunit = $data->ppunit;
    
        if (isset($data)) {
            if ($printing->update()) {
                http_response_code(200);
        
                echo json_encode(array(
                    'message' => 'Printing Entry was updated.'
                ));
            } else {
                http_response_code(503);
        
                echo json_encode(array(
                    'message' => 'Unable to update printing entry.'
                ));
            }
        }
    }

?>
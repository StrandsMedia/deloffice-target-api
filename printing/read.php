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

    $printing->status = isset($data->status) ? $data->status : null;
    
    $printing->company_name = isset($data->company_name) ? $data->company_name : null;

    $printing->product = isset($data->product) ? $data->product : null;

    $stmt = $printing->read();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $printing_arr = array();
        $printing_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $printing_item = array(
                'job_id' => $job_id,
                'custid' => $custid,
                'product' => $product,
                'printwork' => $printwork,
                'startdate' => $startdate,
                'enddate' => $enddate,
                'status' => $status,
                'jobdesc' => $jobdesc,
                'paperspecs' => $paperspecs,
                'filename' => $filename,
                'pc' => $pc,
                'printer' => $printer,
                'printsetting' => $printsetting,
                'qtyorder' => $qtyorder,
                'qtyconsumed' => $qtyconsumed,
                'qtycompleted' => $qtycompleted,
                'qtyrejected' => $qtyrejected,
                'remarks' => $remarks,
                'printedby' => $printedby,
                'supervisedby' => $supervisedby,
                'deliverydate' => $deliverydate,
                'dimensions' => $dimensions,
                'ppunit' => $ppunit,
                'company_name' => $company_name
            );

            array_push($printing_arr['records'], $printing_item);
        }

        echo json_encode($printing_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
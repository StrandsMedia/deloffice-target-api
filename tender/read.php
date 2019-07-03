<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/tender.php';
    include_once '../objects/customer.php';

    $database = new Database();
    $db = $database->getConnection();

    $tender = new Tender($db);
    $attach = new TenderAttachment($db);

    $data = json_decode(file_get_contents('php://input'));

    $customer = new Customer($db);

    $tender->status = isset($data->status) ? $data->status : null;
    
    // $tender->company_name = isset($data->company_name) ? $data->company_name : null;

    $stmt = $tender->read();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $tender_arr = array();
        $tender_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $tender_item = array(
                'tid' => $tid,
                'cust_id' => $cust_id,
                'product' => $product,
                'estimated_quantity' => $estimated_quantity,
                'schedule' => $schedule,
                'receive_date' => $receive_date,
                'closing_date' => $closing_date,
                'actual_quantity' => $actual_quantity,
                'delivery' => $delivery,
                'product_quoted' => $product_quoted,
                'price_quoted' => $price_quoted,
                'attachment' => $attachment,
                'result' => $result,
                'comments' => $comments,
                'status' => $status,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                // 'company_name' => $company_name,
                'data' => $data
            );

            $custdata = $customer->getCustDetails($data, $cust_id);

            $tender_item['company_name'] = $custdata['company_name'];

            if (isset($tid)) {
                $attach->getPath($tid);
                $tender_item['path'] = $attach->path;
            } else {
                
            }

            array_push($tender_arr['records'], $tender_item);
        }

        if (isset($data->company_name)) {
            array_filter($tender_arr['records'], function ($item) {
                return (strpos($item['company_name'], $data->company_name) !== false);
            });
        }

        echo json_encode($tender_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
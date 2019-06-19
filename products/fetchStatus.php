<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/customer.php';
    include_once '../objects/products.php';

    $database = new Database();
    $db = $database->getConnection();

    $statcust = new StatusCust($db);
    $prodfam = new ProductFamily($db);

    $statcust->cust_id = isset($_GET['id']) ? $_GET['id'] : die();

    $stmt = $prodfam->get();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $status_arr = array();
        $status_arr['records'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stmt2 = $statcust->getStatusByCust();
            extract($row);

            $status_item = array(
                'pf_id' => +$pf_id,
                'pf_cat_id' => +$pf_cat_id,
                'pf_subcat_id' => $pf_subcat_id,
                'pf_name' => $pf_name,
                'status' => 0
            );

            if ($stmt2->rowCount() > 0) {
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    if ($row2['pf_id'] === $pf_id) {
                        $status_item['status'] = +$row2['statusNum'];
                    }
                }
            }

            array_push($status_arr['records'], $status_item);
        }
        http_response_code(200);
        echo json_encode($status_arr);
    } else {
        http_response_code(404);
        echo json_encode(
            array(
                "status" => "error",
                "message" => "No data found."
            )
        );
    }
?>
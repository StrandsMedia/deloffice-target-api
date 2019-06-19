<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/products.php';

    $database = new Database();
    $db = $database->getConnection();

    $target = new Target($db);

    $target->cust_id = isset($_GET['id']) ? $_GET['id'] : die();

    $stmt = $target->getTgtByCust();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $target_arr = array();
        $target_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $target_item = array(
                'tar_id' => $tar_id,
                'p_id' => $p_id,
                'customprice' => $customprice,
                'validity_date' => $validity_date,
                'createdAt' => $createdAt,
                'tar_notes' => $tar_notes,
                'pricecat_id' => $pricecat_id,
                'des1' => $des1,
                'des2' => $des2,
                'des3' => $des3,
                'pf_cat_id' => $pf_cat_id,
                'category2' => $category2,
                'puprice' => $puprice,
                'coprice' => $coprice,
                'spprice' => $spprice,
                'wsprice' => $wsprice,
                'delcityprice' => $delcityprice,
                'delcitypromo' => $delcitypromo,
                'sales_rep' => $sales_rep
            );

            array_push($target_arr['records'], $target_item);
        }

        http_response_code(200);
        echo json_encode($target_arr);
    } else {
        http_response_code(404);
        echo json_encode(
            array(
                "status" => "error",
                "message" => "No data found.",
                "records" => array()
            )
        );
    }

?>
<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/pastel.php';
    include_once '../objects/products.php';

    $database = new Database();
    $db = $database->getConnection();

    $srvdatabase = new ServerDatabase();
    $srvdb = $srvdatabase->getConnection();

    if (isset($srvdb)) {
        $stkitm = new StkItem($srvdb);
    }

    $product = new Products($db);

    $product->p_id = isset($_GET['id']) ? $_GET['id'] : die();

    $stmt = $product->getProdById();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $prod_item = array();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        
        $prod_item = array(
            'p_id' => $p_id,
            'des1' => $des1,
            'des2' => $des2,
            'des3' => $des3,
            
            'visible' => $visible,
            'taxcode' => $taxcode,

            'puprice' => $puprice,
            'wsprice' => $wsprice,
            'coprice' => $coprice,
            'avgcost' => $avgcost,
            'delcityprice' => $delcityprice,
            'delcitypromo' => $delcitypromo,
            'spprice' => $spprice,

            'deslong1' => $deslong1,
            'deslong2' => $deslong2,
            'deslong3' => $deslong3,
            'deslong4' => $deslong4,
            'deslong5' => $deslong5,
            'deslong6' => $deslong6,
            'deslong7' => $deslong7,

            'pf_cat_id' => $pf_cat_id,
            'category2' => $category2,
            'category3' => $category3,
            'category4' => $category4
        );

        if (isset($srvdb)) {
            $stkitm->cSimpleCode = $p_id;
            $stmt2 = $stkitm->getPrice();

            if ($stmt2->rowCount() > 0) {
                $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

                $prod_item['avgcost'] = number_format((float)$row2['AveUCst'], 2, '.', '');
            }
        }

        http_response_code(200);
        echo json_encode($prod_item);
    } else {
        http_response_code(404);
        echo json_encode(array(
            'message' => 'No product found.'
        ));
    }
?>
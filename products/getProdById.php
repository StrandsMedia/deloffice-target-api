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
            'taxcode' => $taxcode
        );

        $prod_item['prices'] = array(
            'puprice' => $puprice,
            'wsprice' => $wsprice,
            'coprice' => $coprice,
            'avgcost' => $avgcost,
            'delcityprice' => $delcityprice,
            'delcitypromo' => $delcitypromo,
            'spprice' => $spprice
        );

        if (isset($srvdb)) {
            $stkitm->cSimpleCode = $p_id;
            $stmt2 = $stkitm->getPrice();

            if ($stmt2->rowCount() > 0) {
                $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

                $prod_item['prices']['avgcost'] = number_format((float)$row2['AveUCst'], 2, '.', '');
                $prod_item['stock'] = $row2['Qty_On_Hand'];
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
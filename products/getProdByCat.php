<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
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

    $product->pf_cat_id = isset($_GET['id']) ? +$_GET['id'] : die();
    $mode = isset($_GET['m']) ? +$_GET['m'] : die();

    $stmt = $product->getProdByCat($mode);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $prod_arr = array();
        $prod_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $prod_item = array(
                'p_id' => $p_id,
                'des1' => $des1,
                'des2' => $des2,
                'des3' => $des3,
                'puprice' => $puprice,
                'wsprice' => $wsprice,
                'coprice' => $coprice,
                'avgcost' => $avgcost,
                'delcityprice' => $delcityprice,
                'delcitypromo' => $delcitypromo,
                'visible' => $visible,
                'taxcode' => $taxcode
            );

            if (isset($srvdb)) {
                $stkitm->cSimpleCode = $p_id;
                $stmt2 = $stkitm->getPrice();
    
                if ($stmt2->rowCount() > 0) {
                    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    
                    $prod_item['stock'] = $row2['Qty_On_Hand'];
                }
            }

            array_push($prod_arr['records'], $prod_item);
        }

        http_response_code(200);
        echo json_encode($prod_arr);
    } else {
        // http_response_code(404);
        echo json_encode(array(
            'records' => array(),
            'message' => 'No products found. Please try with another query.'
        ));
    }
?>
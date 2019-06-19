<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

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

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $stmt = $product->getProdByName($data->mode, $data->searchprod);
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
        } else {
            http_response_code(503);
            echo json_encode(array(
                'records' => array(),
                'message' => 'Error. No data found.'
            ));
        }
    }
    

    
?>
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

    $product = new Products($db);

    $searchstring = isset($_GET['s']) ? $_GET['s'] : die();

    $stmt = $product->search($searchstring);
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

                'pf_cat_id' => $pf_cat_id,
                'category2' => $category2,
                'category3' => $category3,
                'category4' => $category4
            );

            $prod_item['prod_fam'] = $product->getProdFamily($row);

            array_push($prod_arr['records'], $prod_item);
        }

        http_response_code(200);
        echo json_encode($prod_arr);
    } else {
        http_response_code(404);
        echo json_encode(array(
            'message' => 'No product found.'
        ));
    }
?>
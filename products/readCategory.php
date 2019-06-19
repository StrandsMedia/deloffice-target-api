<?php
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/products.php';

    $database = new Database();
    $db = $database->getConnection();

    $cat1 = new Category1($db);
    $cat2 = new Category2($db);
    $cat3 = new Category3($db);
    $cat4 = new Category4($db);

    $stmt1 = $cat1->get();
    $stmt2 = $cat2->irr_get();
    $stmt3 = $cat3->irr_get();
    $stmt4 = $cat4->irr_get();

    $num = $stmt1->rowCount() + $stmt2->rowCount() + $stmt3->rowCount() + $stmt4->rowCount();
    
    if ($num > 0) {
        $cat_arr = array();
        $cat_arr['category'] = array();
        $cat_arr['category2'] = array();
        $cat_arr['category3'] = array();
        $cat_arr['category4'] = array();

        while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
            extract($row1);
            $cat_item = array(
                'id' => $row1['id'],
                'position' => $row1['position'],
                'description' => $row1['description']
            );

            array_push($cat_arr['category'], $cat_item);
        }
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            extract($row2);
            $cat_item = array(
                'id' => $row2['id'],
                'description' => $row2['description'],
                'upcat' => $row2['upcat'],
                'last' => $row2['last']
            );

            array_push($cat_arr['category2'], $cat_item);
        }
        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            extract($row3);
            $cat_item = array(
                'id' => $row3['id'],
                'description' => $row3['description'],
                'upcat' => $row3['upcat'],
                'last' => $row3['last']
            );

            array_push($cat_arr['category3'], $cat_item);
        }
        while ($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
            extract($row4);
            $cat_item = array(
                'id' => $row4['id'],
                'description' => $row4['description'],
                'upcat' => $row4['upcat']
            );

            array_push($cat_arr['category4'], $cat_item);
        }

        http_response_code(200);
        echo json_encode($cat_arr);
    } else {
        echo json_encode(array(
            'message' => 'No records found'
        ));
    }
?>
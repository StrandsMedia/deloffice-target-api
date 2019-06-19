<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');

    include_once '../config/db.php';
    include_once '../objects/category.php';

    $database = new Database();
    $db = $database->getConnection();

    $category = new Category($db);
    $sector = new Sector($db);
    $subsector = new Subsector($db);
    
    $stmt1 = $category->read();
    $stmt2 = $sector->read();
    $stmt3 = $subsector->read();
    $num1 = $stmt1->rowCount();
    $num2 = $stmt2->rowCount();
    $num3 = $stmt3->rowCount();

    if ($num1 > 0) {
        $category_arr = array();
        $category_arr['category'] = array();

        while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
            extract($row1);

            $category_item = array(
                'cat_id' => $cat_id,
                'category_name' => $category_name,
                'abre' => $abre,
                'status' => $status,
            );

            array_push($category_arr['category'], $category_item);
        }
        $category_arr['sector'] = array();

        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            extract($row2);

            $category_item = array(
                'cat_id' => $cat_id,
                'category_name' => $category_name,
                'abre' => $abre,
                'status' => $status,
            );

            array_push($category_arr['sector'], $category_item);
        }
        $category_arr['subsector'] = array();

        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            extract($row3);

            $category_item = array(
                'cat_id' => $cat_id,
                'category_name' => $category_name,
                'abre' => $abre,
                'status' => $status,
                'upcat' => $upcat
            );

            array_push($category_arr['subsector'], $category_item);
        }

        echo json_encode($category_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
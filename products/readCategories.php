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

    $num = $stmt1->rowCount();

    if ($num > 0) {
        $cat_arr = array();
        $cat_arr['records'] = array();

        while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
            $cat_item = array(
                'id' => $row['id'],
                'position' => $row['position'],
                'description' => $row['description'],
                'cat' => 1
            );

            $cat2->upcat = $row['id'];
            $stmt2 = $cat2->get();

            if ($stmt2->rowCount() > 0) {
                $cat_item['subcat'] = array();

                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $subcat_item = array(
                        'id' => $row2['id'],
                        'description' => $row2['description'],
                        'upcat' => $row2['upcat'],
                        'last' => $row2['last'],
                        'cat' => 2
                    );

                    if ($row2['last'] === 'N') {
                        $cat3->upcat = $row2['id'];
                        $stmt3 = $cat3->get();

                        if ($stmt3->rowCount() > 0) {
                            $subcat_item['subcat'] = array();

                            while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                $subsubcat_item = array(
                                    'id' => $row3['id'],
                                    'description' => $row3['description'],
                                    'upcat' => $row3['upcat'],
                                    'last' => $row3['last'],
                                    'cat' => 3
                                );

                                if ($row3['last'] === 'N') {
                                    $cat4->upcat = $row3['id'];
                                    $stmt4 = $cat4->get();

                                    if ($stmt4->rowCount() > 0) {
                                        $subsubcat_item['subcat'] = array();

                                        while ($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
                                            $subsubsubcat_item = array(
                                                'id' => $row4['id'],
                                                'description' => $row4['description'],
                                                'upcat' => $row4['upcat'],
                                                'cat' => 4
                                            );

                                            array_push($subsubcat_item['subcat'], $subsubsubcat_item);
                                        }
                                    }
                                }

                                array_push($subcat_item['subcat'], $subsubcat_item);
                            }
                        }
                    }

                    array_push($cat_item['subcat'], $subcat_item);
                }
            }

            array_push($cat_arr['records'], $cat_item);
        }
        http_response_code(200);
        echo json_encode($cat_arr);
    } else {
        echo json_encode(array(
            'message' => 'No records found',
            'records' => array()
        ));
    }

?>
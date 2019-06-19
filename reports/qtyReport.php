<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/pastel.php';
    include_once '../objects/products.php';

    $months = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul ', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul ', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec' );

    $database = new Database();
    $db = $database->getConnection();

    $srvdatabase = new DelServerDatabase();
    $srvdb = $srvdatabase->getConnection();

    $product = new Products($db);

    $prod = isset($_GET['p']) ? $_GET['p'] : die();

    $range = $product->getProductCodes($prod);

    $year = isset($_GET['y']) ? $_GET['y'] : date('Y');
    $month = isset($_GET['m']) ? $_GET['m'] : date('m');

    if (isset($srvdb)) {
        $stk = new PostST($srvdb);

        $stmt = $stk->getQtyReport($range, $year, $month);
        // echo $stmt;
        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);

            $month = $month + 12;
            
            echo json_encode(
                array(
                    0 => array(
                        'month' => $months[+$month - 6],
                        'qty' => round($Qty6)
                    ),
                    1 => array(
                        'month' => $months[+$month - 5],
                        'qty' => round($Qty5)
                    ),
                    2 => array(
                        'month' => $months[+$month - 4],
                        'qty' => round($Qty4)
                    ),
                    3 => array(
                        'month' => $months[+$month - 3],
                        'qty' => round($Qty3)
                    ),
                    4 => array(
                        'month' => $months[+$month - 2],
                        'qty' => round($Qty2)
                    ),
                    5 => array(
                        'month' => $months[+$month - 1],
                        'qty' => round($Qty1)
                    )
                )
            );
        }
    }

?>
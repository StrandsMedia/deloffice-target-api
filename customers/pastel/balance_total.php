<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../../config/db.php';
    include_once '../../objects/pastel.php';

    $terms = ['Current', '30 Days', '60 Days', '90 Days', '120 Days', '150 Days', '180 Days'];
    
    $acc = isset($_GET['id']) ? $_GET['id'] : die();

    if (isset($_GET['d'])) {
        switch (+$_GET['d']) {
            case 1:
                $database = new DelServerDatabase();
                $db = $database->getConnection();
                break;
            case 2:
                $database = new RnsServerDatabase();
                $db = $database->getConnection();
                break;
            case 3:
                $database = new PnpServerDatabase();
                $db = $database->getConnection();
                break;
        }
    } else {
        $database = new DelServerDatabase();
        $db = $database->getConnection();
    }


    if (isset($db)) {
        $client = new Client($db);
        $postAR = new PostAR($db);
    
        $postAR->AccountLink = $client->getDCLink($acc);
        $accterm = $client->getTerms($acc);
    
        $client_arr = array();
        $client_arr['records'] = array();
        $client_arr['terms'] = +$accterm;
        $client_arr['DCLink'] = $postAR->AccountLink;
    
        $finaltotal = 0;

        foreach($terms as $index => $term) {
            $total = 0;
    
            $stmt = $postAR->getAgeAnalysis($index);
            $num = $stmt->rowCount();
        
            if ($num > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);

                    $finaltotal += +$Outstanding;
                }
            }
        }
    
        echo json_encode(array(
            'total' => round($finaltotal, 2)
        ));
    }

?>
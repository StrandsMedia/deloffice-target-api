<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../../config/db.php';
    include_once '../../objects/pastel.php';

    if (isset($_GET['d'])) {
        switch ($_GET['d']) {
            case '1':
                $database = new DelServerDatabase();
                $db = $database->getConnection();
                break;
            case '2':
                $database = new RnsServerDatabase();
                $db = $database->getConnection();
                break;
            case '3':
                $database = new PnpServerDatabase();
                $db = $database->getConnection();
                break;
        }
    } else {
        $database = new DelServerDatabase();
        $db = $database->getConnection();
    }

    $acc = isset($_GET['id']) ? $_GET['id'] : die();

    $date = array();

    $date['start'] = isset($_GET['d1']) ? $_GET['d1'] : null;
    $date['end'] = isset($_GET['d2']) ? $_GET['d2'] : null;

    $terms = ['Current', '30 Days', '60 Days', '90 Days', '120 Days', '150 Days', '180 Days'];

    if (isset($db)) {
        $client = new Client($db);
        $postAR = new PostAR($db);
    
        $postAR->AccountLink = $client->getDCLink($acc);
    
        $stmt = $postAR->getAllocs(3, $date);
        $num = $stmt->rowCount();
    
        if ($num > 0) {
            $alloc_arr = array();
            $alloc_arr['balance'] = array();
            $alloc_arr['records'] = array();
    
            foreach($terms as $index => $term) {
                $total = 0;
                $stmt2 = $postAR->getAgeAnalysis($index);
        
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    extract($row2);
        
                    $total += +$Outstanding;
                }
        
                $client_item = array(
                    'id' => $index,
                    'Name' => $term,
                    'Outstanding' => $total,
                );
        
                array_push($alloc_arr['balance'], $client_item);
            }
    
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
    
                $allocs = $postAR->allocBD($cAllocs);
    
                $alloc_item = array(
                    'AutoIdx' => $AutoIdx,
                    'Code' => $Code,
                    'TxDate' => $TxDate,
                    'Reference' => $Reference,
                    'cReference2' => $cReference2,
                    'Description' => $Description,
                    'Debit' => $Debit,
                    'Credit' => $Credit,
                    'Outstanding' => $Outstanding,
                    'cAllocs' => $allocs,
                    'allocated' => null
                );
    
                if ($allocs !== '') {
                    $alloc_item['allocR'] = array();
        
                    foreach($allocs as $idx => $value) {
                        $alloc_item['allocated'] += +$value['amt'];
        
                        $stmt3 = $postAR->getPairedAllocs($value['idx']);
                        $num3 = $stmt3->rowCount();
        
                        if ($num3 > 0) {
                            while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                $paired_alloc_item = array(
                                    'AutoIdx' => $row3['AutoIdx'],
                                    'Code' => $row3['Code'],
                                    'TxDate' => $row3['TxDate'],
                                    'Reference' => $row3['Reference'],
                                    'cReference2' => $row3['cReference2'],
                                    'Description' => $row3['Description'],
                                    'Debit' => $row3['Debit'],
                                    'Credit' => $value['amt'],
                                    'Outstanding' => $row3['Outstanding'],
                                );
        
                                array_push($alloc_item['allocR'], $paired_alloc_item);
                            }
                        }
                    }
                }
    
                array_push($alloc_arr['records'], $alloc_item);
            }
    
            http_response_code(200);
            echo json_encode($alloc_arr);
        } else {
    
            echo json_encode(array(
                "message" => "No records found for this customer."
            ));
        }
    }

?>
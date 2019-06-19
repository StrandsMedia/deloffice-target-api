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

    $client = new Client($db);
    $postAR = new PostAR($db);

    $postAR->AccountLink = $client->getDCLink($acc);

    $stmt = $postAR->getAllocs(1, $date);
    $stmt_rev = $postAR->getAllocs(2, $date);
    $num = $stmt->rowCount() + $stmt_rev->rowCount();

    if ($num > 0) {
        $alloc_arr = array();
        $alloc_arr['positive'] = array();
        $alloc_arr['negative'] = array();

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
                'cAllocs' => $allocs
            );

            $alloc_item['allocR'] = array();

            foreach($allocs as $idx => $value) {
                $stmt2 = $postAR->getPairedAllocs($value['idx']);
                $num2 = $stmt2->rowCount();

                if ($num2 > 0) {
                    while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                        $paired_alloc_item = array(
                            'AutoIdx' => $row2['AutoIdx'],
                            'Code' => $row2['Code'],
                            'TxDate' => $row2['TxDate'],
                            'Reference' => $row2['Reference'],
                            'cReference2' => $row2['cReference2'],
                            'Description' => $row2['Description'],
                            'Debit' => $row2['Debit'],
                            'Credit' => $value['amt'],
                            'Outstanding' => $row2['Outstanding'],
                        );

                        array_push($alloc_item['allocR'], $paired_alloc_item);
                    }
                }
            }

            if ($alloc_item['Code'] !== 'INV') {
                $alloc_item['idx'] = 1;
            } else {
                $alloc_item['idx'] = 0;
            }

            array_push($alloc_arr['positive'], $alloc_item);
        }
        while ($row_rev = $stmt_rev->fetch(PDO::FETCH_ASSOC)) {
            extract($row_rev);

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
                'cAllocs' => $allocs
            );

            $alloc_item['allocR'] = array();

            foreach($allocs as $idx => $value) {
                $stmt2 = $postAR->getPairedAllocs($value['idx']);
                $num2 = $stmt2->rowCount();

                if ($num2 > 0) {
                    while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                        $paired_alloc_item = array(
                            'AutoIdx' => $row2['AutoIdx'],
                            'Code' => $row2['Code'],
                            'TxDate' => $row2['TxDate'],
                            'Reference' => $row2['Reference'],
                            'cReference2' => $row2['cReference2'],
                            'Description' => $row2['Description'],
                            'Debit' => $row2['Debit'],
                            'Credit' => $value['amt'],
                            'Outstanding' => $row2['Outstanding'],
                        );

                        array_push($alloc_item['allocR'], $paired_alloc_item);
                    }
                }
            }

            if ($alloc_item['Code'] !== 'INV') {
                $alloc_item['idx'] = 1;
            } else {
                $alloc_item['idx'] = 0;
            }

            array_push($alloc_arr['negative'], $alloc_item);
        }

        http_response_code(200);
        echo json_encode($alloc_arr);
    } else {
        http_response_code(404);
        echo json_encode(array(
            "message" => "No records found for this customer."
        ));
    }



    

?>
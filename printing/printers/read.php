<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../../config/db.php';
    include_once '../../objects/printing.php';

    $database = new Database();
    $db = $database->getConnection();

    $printers = new Printer($db);

    $stmt = $printers->readPrinters();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $printer_arr = array();
        $printer_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $printer_item = array(
                'printerId' => $printerId,
                'printerName' => $printerName,
                'active' => $active,
                'createdAt' =>  $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($printer_arr['records'], $printer_item);
        }

        http_response_code(200);
        echo json_encode($printer_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
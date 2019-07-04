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

    $ink_report = new InkReport($db);

    $stmt = $ink_report->readEntries();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $inkReport_arr = array();
        $inkReport_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $inkReport_item = array(
                'reportId' => $reportId,
                'printerId' => $printerId,
                //'printerName' => $printerName
                'inkChangedType' => $inkChangedType,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($inkReport_arr['records'], $inkReport_item);
        }

        http_response_code(200);
        echo json_encode($inkReport_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
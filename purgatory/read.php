<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/purgatory.php';

    $database = new Database();
    $db = $database->getConnection();

    $purgatory = new Purgatory($db);

    $stmt = $purgatory->read();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $purgatory_arr = array();
        $purgatory_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $purgatory_item = array(
                'entryId' => $entryId,
                'invoice_id' => $invoice_id,
                'invlineid' => $invlineid,
                'p_id' => $p_id,
                'debit' => $debit,
                'credit' => $credit,
                'outstd' => $outstd,
                'entryType' => $entryType,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($purgatory_arr['records'], $purgatory_item);
        }

        echo json_encode($purgatory_arr);
    } else {
        echo json_encode(
            array('message' => 'No purgatories found.')
        );
    }
?>
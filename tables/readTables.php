<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/tables.php';

    $database = new Database();
    $db = $database->getConnection();

    $tables = new PrepTables($db);

    $stmt = $tables->read();
    $num = $stmt->rowCount();
    
    $table_arr = array();
    $table_arr['records'] = array();

    if ($num > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $table_item = array(
                'tableId' => $tableId,
                'tableName' => $tableName,
                'status' => $status,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($table_arr['records'], $table_item);
        }

        echo json_encode($table_arr);
    } else {
        echo json_encode(
            array(
                'message' => 'No records found.',
                'records' => array()
            )
        );   
    }
?>
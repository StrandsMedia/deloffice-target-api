<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/debtors.php';

    $database = new Database();
    $db = $database->getConnection();

    $reminder = new DebtReminder($db);

    $id = isset($_GET['c']) ? +$_GET['c'] : die();
    $data = isset($_GET['d']) ? +$_GET['d'] : die();

    $stmt = $reminder->findEntry($id, $data);
    $num = $stmt->rowCount();

    if ($num > 0) {
        echo json_encode(array(
            'result' => true
        ));
    } else {
        echo json_encode(array(
            'result' => false
        ));
    }
?>
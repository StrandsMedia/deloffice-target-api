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

    $control = new DebtorsControl($db);

    $id = isset($_GET['c']) ? +$_GET['c'] : die();
    $data = isset($_GET['d']) ? +$_GET['d'] : die();

    $stmt = $control->findEntry($id, $data);
    $num = $stmt->rowCount();

    if ($num == 1) {
        echo json_encode(array(
            'result' => true
        ));
    } else {
        echo json_encode(array(
            'result' => false
        ));
    }
?>
<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/pastel.php';
    include_once '../objects/products.php';

    $database = new Database();
    $db = $database->getConnection();

    $product = new Products($db);

    $product->p_id = isset($_GET['id']) ? $_GET['id'] : die();

    $num = $product->isProduct();

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
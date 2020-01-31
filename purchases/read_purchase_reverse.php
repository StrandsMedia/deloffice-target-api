<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/purchases.php';
    include_once '../objects/products.php';
    include_once '../objects/customer.php';

    $database = new Database();
    $db = $database->getConnection();

    $reqs = new PurchaseRequest($db);
    $reqprods = new PurchaseRequestProds($db);

    $customer = new Customer($db);
    $product = new Products($db);

?>
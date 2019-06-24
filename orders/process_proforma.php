<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/invoice.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);
    $invoice = new Invoice($db);
    $invlines = new InvoiceLines($db);

    $data = json_decode(file_get_contents('php://input'));
?>
<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../../config/db.php';
    include_once '../../objects/pastel.php';

    $acc = isset($_GET['id']) ? $_GET['id'] : die();

    if (isset($_GET['d'])) {
        switch (+$_GET['d']) {
            case 1:
                $database = new DelServerDatabase();
                $db = $database->getConnection();
                break;
            case 2:
                $database = new RnsServerDatabase();
                $db = $database->getConnection();
                break;
            case 3:
                $database = new PnpServerDatabase();
                $db = $database->getConnection();
                break;
        }
    } else {
        $database = new DelServerDatabase();
        $db = $database->getConnection();
    }

    if (isset($db)) {
        $client = new Client($db);

        $email = $client->getEmail($acc);

        echo json_encode(array(
            'email' => $email
        ));
    }
?>
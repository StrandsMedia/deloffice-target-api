<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../../config/db.php';
    include_once '../../objects/pastel.php';

    $srvdatabase = new CommonServerDatabase();
    $srvdb = $srvdatabase->getConnection();

    $sessions = new _tSessions($srvdb);

    $sessions->idSessions = isset($_GET['id']) ? $_GET['id'] : die();

    if ($sessions->kill()) {
        echo json_encode(array('message' => 'Session killed'));
    } else {
        echo json_encode(array('message' => 'Failed to kill session'));
    }
?>
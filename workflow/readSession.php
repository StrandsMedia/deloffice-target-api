<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/workflow.php';
    include_once '../objects/user.php';

    $database = new Database();
    $db = $database->getConnection();

    $session = new WorkflowSession($db);
    $delivery = new WorkflowDelivery($db);
    $uservar = new User($db);

    $session->range1 = isset($_GET['r1']) ? $_GET['r1'] : die();
    $session->range2 = isset($_GET['r2']) ? $_GET['r2'] : die();

    if ($_GET['r3'] == 0) {
        $operator = '=';
    } else if ($_GET['r3'] == 1) {
        $operator = '!=';
    }

    $stmt = $session->readSession($operator);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $session_arr = array();
        $session_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $session_item = array(
                'sessionID' => $sessionID,
                'sessionDate' => $sessionDate,
                'vehicle' => html_entity_decode($vehicle),
                'driver' => html_entity_decode($driver),
                'status' => $status,
                'user' => $user
            );

            if (isset($user)) {
                $uservar->getUser($user);
                $session_item['sales_rep'] = $uservar->sales_rep;
            } else {
                $session_item['sales_rep'] = null;
            }

            $invCount = $delivery->invoiceCount($session_item['sessionID']);
            $invAmt = $invCount->rowCount();
            
            $session_item['invAmt'] = $invAmt;

            array_push($session_arr['records'], $session_item);
        }

        echo json_encode($session_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }

?>
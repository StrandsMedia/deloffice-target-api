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

    $stmt = $sessions->get();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $session_arr = array();
        $session_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $session_item = array(
                'idSessions' => $idSessions,
                'SessionID' => $SessionID,
                'SQLServer' => $SQLServer,
                'DatabaseName' => $DatabaseName,
                'UserName' => $UserName,
                'AgentName' => $AgentName,
                'ConnectTime' => $ConnectTime,
                'RefreshTime' => $RefreshTime
            );

            array_push($session_arr['records'], $session_item);
        }

        http_response_code(200);
        echo json_encode($session_arr);
    } else {
        http_response_code(404);
        echo json_encode(array(
            'records' => array(),
            'message' => 'No user in session right now.'
        ));
    }
?>
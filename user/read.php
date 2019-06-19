<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/user.php';

    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);

    $stmt = $user->getUsers();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $user_arr = array();
        $user_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $user_item = array(
                'sales_id' => $sales_id,
                'sales_rep' => $sales_rep,
                'dept' => $dept,
                'rep_initial' => $rep_initial,
                'visible' => $visible,
                'status' => $status,
                'password' => $password
            );

            array_push($user_arr['records'], $user_item);
        }

        echo json_encode($user_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }

?>
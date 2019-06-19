<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/user.php';

    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);

    $data = json_decode(file_get_contents('php://input'));

    $user->sales_rep = $data->username;
    $user->password = $data->password;
    
    $stmt = $user->login();
    $num = $stmt->rowCount();
    
    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(
            array(
                'status' => 'loggedin',
                'user_id' => $row['sales_id'],
                'username' => $row['sales_rep'],
                'usertype' => $row['dept'],
                'token' => uniqid($row['dept'], true)
            )
        );
    } else {
        echo json_encode(
            array(
                'status' => 'error',
                'message' => 'Invalid user credentials.'
            )
        );
    }
?>
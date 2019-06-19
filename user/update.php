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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $user->sales_rep = $data->sales_rep;
            $user->password = $data->password;
            $user->dept = $data->dept;
            $user->rep_initial = $data->rep_initial;
            $user->visible = $data->visible;
            $user->status = $data->status;
            $user->sales_id = $data->sales_id;
            if ($user->updateUser()) {
                http_response_code(200);
        
                echo json_encode(array(
                    'message' => "User {$data->sales_rep} was successfully updated."
                ));
            } else {
                http_response_code(503);
        
                echo json_encode(array(
                    'message' => 'Unable to update user.'
                ));
            }
        } else {
            http_response_code(503);
        
            echo json_encode(array(
                'message' => 'Unable to update user.'
            ));
        }
    }
?>
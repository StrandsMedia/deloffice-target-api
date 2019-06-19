<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/permission.php';

    $database = new Database();
    $db = $database->getConnection();

    $permission = new UserPermission($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $permission->moduleId = $data->moduleId;
            $permission->create = json_encode($data->mod);
            if ($permission->change($data->op)) {
                http_response_code(200);
                echo json_encode(array(
                    'status' => 'success',
                    'message' => 'Change successful'
                ));
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'An error occured. Please try again later'
            ));
        }
    }
?>
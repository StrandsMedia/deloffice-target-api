<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/permission.php';

    $database = new Database();
    $db = $database->getConnection();

    $permission = new UserPermission($db);

    $permission->moduleId = isset($_GET['id']) ? +$_GET['id'] : die();

    $stmt = $permission->readByModule();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        extract($row);

        $permission_arr = array(
            'moduleId' => $moduleId,
            'create' => json_decode($create),
            'read' => json_decode($read),
            'update' => json_decode($update),
            'delete' => json_decode($delete),
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt
        );

        http_response_code(200);
        echo json_encode($permission_arr);
    } else {
        // http_response_code(404);
        echo json_encode(array(
            'message' => 'No records found.'
        ));
    }

?>
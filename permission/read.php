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

    $stmt = $permission->read();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $module_arr = array();
        $module_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $module_item = array(
                'moduleId' => $moduleId,
                'moduleName' => $moduleName,
                'create' => json_decode($create),
                'read' => json_decode($read),
                'update' => json_decode($update),
                'delete' => json_decode($delete),
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($module_arr['records'], $module_item);
        }

        http_response_code(200);
        echo json_encode($module_arr);
    } else {
        http_response_code(404);
        echo json_encode(array(
            'message' => 'No records found.',
            'records' => array()
        ));
    }
?>
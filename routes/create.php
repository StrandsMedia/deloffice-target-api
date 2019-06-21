<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../config/db.php';
    include_once '../objects/routes.php';

    $database = new Database();
    $db = $database->getConnection();

    $route = new Route($db);

    $data = json_decode(file_get_contents("php://input"));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $route->routeRef = $data->routeRef;
            $route->routeName = $data->routeName;

            if ($route->create()) {
                http_response_code(201);
    
                echo json_encode(array(
                    'status' => 'success',
                    'message' => 'Route was successfully created'
                ));
            } else {
                http_response_code(503);
    
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Unable to create route'
                ));
            }
        } else {
            http_response_code(400);
    
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Unable to create route. No data was found.'
            ));
        }
    }
?>
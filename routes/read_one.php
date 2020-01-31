<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/routes.php';

    $database = new Database();
    $db = $database->getConnection();

    $route = new Route($db);
    $routeloc = new RouteLocation($db);
    $routeId = isset($_GET['id']) ? $_GET['id'] : die();

    $stmt = $route->read($routeId);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        extract($row);

        $route_arr = array(
            'routeId' => $routeId,
            'routeRef' => $routeRef,
            'routeName' => $routeName,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt
        );
        $routeloc->routeId = $routeId;

        $route_arr['locations'] = $routeloc->readByRoute();
         

        http_response_code(200);
        echo json_encode($route_arr);
    } else {
        echo json_encode(
            array('message' => 'No route found.')
        );
    }


?>
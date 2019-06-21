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
    $routeId = null;

    $stmt = $route->read($routeId);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $route_arr = array();
        $route_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $route_item = array(
                'routeId' => $routeId,
                'routeRef' => $routeRef,
                'routeName' => $routeName,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($route_arr['records'], $route_item);
        }

        echo json_encode($route_arr);
    } else {
        echo json_encode(
            array('message' => 'No routes found.')
        );
    }
?>
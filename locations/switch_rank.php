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

    $routeloc = new RouteLocation($db);
    
    $data = json_decode(file_get_contents("php://input"));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            switch ($data->case) {
                case 1:
                    $routeloc->routelocId = $data->routelocId1;
                    $routeloc->rank = +$data->rank1 + 1;
                    if ($routeloc->changeRank()) {
                        $routeloc->routelocId = $data->routelocId2;
                        $routeloc->rank = +$data->rank2 - 1;
                        if ($routeloc->changeRank()) {
                            http_response_code(201);
    
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => 'Update successful'
                            ));
                        }
                    }
                    break;
                case 2:
                    $routeloc->routelocId = $data->routelocId1;
                    $routeloc->rank = +$data->rank1 - 1;
                    if ($routeloc->changeRank()) {
                        $routeloc->routelocId = $data->routelocId2;
                        $routeloc->rank = +$data->rank2 + 1;
                        if ($routeloc->changeRank()) {
                            http_response_code(201);
    
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => 'Update successful'
                            ));
                        }
                    }
            }
        } else {
            http_response_code(400);
    
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Unable to create location. No data was found.'
            ));
        }
    }
?>
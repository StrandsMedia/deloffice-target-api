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

    $location = new Location($db);
    $locationId = null;

    $stmt = $location->read($locationId);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $location_arr = array();
        $location_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $location_item = array(
                'locationId' => $locationId,
                'locationRef' => $locationRef,
                'locationName' => $locationName,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($location_arr['records'], $location_item);
        }

        echo json_encode($location_arr);
    } else {
        echo json_encode(
            array('message' => 'No locations found.')
        );
    }
?>
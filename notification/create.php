<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/notification.php';

    $database = new Database();
    $db = $database->getConnection();

    $reminder = new UserReminders($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $reminder->reminder_name = $data->reminder_name;
            $reminder->reminder_time = $data->reminder_time;
            $reminder->user = $data->user;

            if ($reminder->create()) {
                http_response_code(201);
                echo json_encode(array(
                    'status' => 'success',
                    'message' => 'Reminder successfully created.'
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Failed to create reminder.'
                ));
            }
        } else {
            http_response_code(503);
            echo json_encode(
                array(
                    'status' => 'error',
                    'message' => 'Service unavailable. Please try again.'
                )
            );
        }
    }
?>
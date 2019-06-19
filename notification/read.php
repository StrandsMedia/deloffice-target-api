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
            $reminder->status = $data->status;
            $reminder->user = $data->user;
            $stmt = $reminder->getReminder();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $reminder_arr = array();
                $reminder_arr['records'] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $reminder_item = array(
                        'reminder_id' => $reminder_id,
                        'reminder_name' => $reminder_name,
                        'reminder_time' => $reminder_time,
                        'status' => $status,
                        'user' => $user,
                        'createdAt' => $createdAt,
                        'updatedAt' => $updatedAt
                    );

                    array_push($reminder_arr['records'], $reminder_item);
                }

                http_response_code(200);
                echo json_encode($reminder_arr);
            } else {
               
                echo json_encode(
                    array(
                        'message' => 'No records found'
                    )
                );
            }
        } else {
            http_response_code(503);
            echo json_encode(
                array(
                    'message' => 'Service unavailable. Please try again.'
                )
            );
        }
    }
?>
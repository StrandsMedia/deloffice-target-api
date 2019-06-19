<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/debtors.php';
    include_once '../objects/notification.php';

    $database = new Database();
    $db = $database->getConnection();

    $reminder = new DebtReminder($db);

    $userreminder = new UserReminders($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $reminder->cust_id = $data->cust_id;
            $reminder->amt = $data->amt;
            $reminder->status = $data->status;
            $reminder->data = $data->data;
            $reminder->active = 0;

            $userreminder->user = $data->user;
            $userreminder->reminder_name = 'Send next reminder to ' . $data->company_name;
            $userreminder->reminder_time = date('Y-m-d H:i:s');

            if ($reminder->create(+$data->step)) {
                // if ($userreminder->create()) {
                    http_response_code(201);
                    echo json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Entry was successfully created.'
                        )
                    );
                // }
            } else {
                http_response_code(404);
                echo json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'Failed to create entry. An error occured.'
                    )
                );
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Data was not found or is incomplete. Please try again later.'
            ));
        }
    }
?>
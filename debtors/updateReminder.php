<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/debtors.php';
    include_once '../objects/comments.php';
    include_once '../objects/notification.php';

    $database = new Database();
    $db = $database->getConnection();

    $reminder = new DebtReminder($db);
    $remcomm = new DebtReminderComment($db);
    $comment = new DebtorsComment($db);

    $userreminder = new UserReminders($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $reminder->status = +$data->status;
            $reminder->dbt_rem_id = +$data->dbt_rem_id;

            $name = $reminder->getReminderName(+$data->status);

            $remcomm->dbt_rem_id = +$data->dbt_rem_id;
            $remcomm->user = $data->user_id;
            $remcomm->dbt_comment = "{$name} sent on: " . date("Y-m-d");

            $date = date('Y-m-d H:i:s');

            $userreminder->user = $data->user_id;
            $userreminder->reminder_name = 'Send next reminder to ' . $data->company_name;
            $userreminder->reminder_time = date('Y-m-d H:i:s', strtotime($date. ' + 7 days'));

            if ($reminder->update(+$data->step)) {
                if ($data->company === 'DEL') {
                    if ($remcomm->insert()) {
                        $comment->cust_id = $data->cust_id;
                        $comment->comment = 'From DR: ' . $remcomm->dbt_comment;
                        $comment->date = date('Y-m-d H:i:s');
                        $comment->user = $data->user_id;
                        if ($comment->insertComment()) {
                            if (+$data->status) {
                                if ($userreminder->create()) {
                                    http_response_code(200);
                                    echo json_encode(
                                        array(
                                            'status' => 'success',
                                            'message' => 'Mail was successfully sent.'
                                        )
                                    );
                                }
                            } else {
                                http_response_code(200);
                                echo json_encode(
                                    array(
                                        'status' => 'success',
                                        'message' => 'Mail was successfully sent.'
                                    )
                                );
                            }
                        } else {
                            http_response_code(503);
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => 'An error occured on debtors comment insert.'
                            ));
                        }
                    } else {
                        http_response_code(503);
                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'An error occured on reminder comment insert.'
                        ));
                    }
                } else {
                    if ($remcomm->insert()) {
                        if ($userreminder->create()) {
                            http_response_code(200);
                            echo json_encode(
                                array(
                                    'status' => 'success',
                                    'message' => 'Mail was successfully sent.'
                                )
                            );
                        }
                    }
                }
            } else {
                // http_response_code(503);
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'An error occured on updating reminder.'
                ));
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
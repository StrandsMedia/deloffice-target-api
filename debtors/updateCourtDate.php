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

    $rem = new UserReminders($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $reminder->courtDate = $data->newDate;
            $reminder->dbt_rem_id = $data->dbt_rem_id;

            $remcomm->dbt_rem_id = $data->dbt_rem_id;
            $remcomm->user = $data->user;
            $remcomm->dbt_comment = 'Updated court date: ' . date('Y-m-d', strtotime($data->newDate));

            $rem->reminder_name = 'Court date';
            $rem->reminder_time = date('Y-m-d H:i:s', strtotime($data->newDate . ' 08:30:00'));
            $rem->user = $data->user;

            if ($reminder->updateCourtDate()) {
                if ($remcomm->insert()) {
                    if ($rem->create()) {
                        http_response_code(200);
                        echo json_encode(
                            array(
                                'status' => 'success',
                                'message' => 'Update was successful.'
                            )
                        );
                    }
                }
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
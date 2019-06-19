<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/debtors.php';
    include_once '../objects/comments.php';

    $database = new Database();
    $db = $database->getConnection();

    $reminder = new DebtReminder($db);
    $remcomm = new DebtReminderComment($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $reminder->amt = $data->newAmt;
            $reminder->dbt_rem_id = $data->dbt_rem_id;

            $remcomm->dbt_rem_id = $data->dbt_rem_id;
            $remcomm->user = $data->user;
            $remcomm->dbt_comment = 'Amount due is now of ' . $data->newAmt;

            if ($reminder->updateAmtDue()) {
                if ($remcomm->insert()) {
                    http_response_code(200);
                    echo json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Update was successful.'
                        )
                    );
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
<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/debtors.php';

    $database = new Database();
    $db = $database->getConnection();

    $reminder = new DebtReminder($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $reminder->approved = 1;
            $reminder->dbt_rem_id = $data->dbt_rem_id;
            if ($reminder->authReminder()) {
                http_response_code(200);
                echo json_encode(
                    array(
                        'status' => 'success',
                        'message' => 'Update was successful. Permission granted.'
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
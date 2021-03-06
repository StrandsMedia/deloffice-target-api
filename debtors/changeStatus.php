<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/comments.php';
    include_once '../objects/debtors.php';

    $database = new Database();
    $db = $database->getConnection();

    $control = new DebtorsControl($db);
    $comment = new DebtorsCtrlCmt($db);

    $dbt_comment = new DebtorsComment($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $control->dc_id = $data->dc_id;
            $control->status = $data->status;

            $comment->dc_id = $data->dc_id;
            $comment->user = $data->user;
            $comment->dc_comment = $data->note;
            if (isset($comment->dc_comment) && $comment->dc_comment != '') {
                
                $dbt_comment->cust_id = $data->cust_id;
                $dbt_comment->comment = 'From DC: '. $data->note;
                $dbt_comment->date = date('Y-m-d H:m:s');
                $dbt_comment->user = $data->user;
            }

            if ($control->statusUpdate()) {
                if (isset($comment->dc_comment) && $comment->dc_comment != '') {
                    if ($comment->insertComment()) {
                        if ($dbt_comment->insertComment()) {
                            http_response_code(201);
                            echo json_encode(
                                array(
                                    'status' => 'success',
                                    'message' => 'Successful creation of data.'
                                )
                            );
                        }
                    }
                } else {
                    http_response_code(201);
                    echo json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Successful creation of data.'
                        )
                    );
                }
            } else {
                http_response_code(503);
                echo json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'Failed to update entry.'
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
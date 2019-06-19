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

    $ctrlcmt = new DebtorsCtrlCmt($db);
    $comment = new DebtorsComment($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            if (isset($data->comment)) {
                $ctrlcmt->dc_id = $data->dc_id;
                $ctrlcmt->dc_comment = $data->comment;
                $ctrlcmt->user = $data->user_id;
                if ($ctrlcmt->insertComment()) {
                    $comment->cust_id = $data->cust_id;
                    $comment->comment = 'From DC: ' . $data->comment;
                    $comment->date = $data->date;
                    $comment->user = $data->user_id;
                    
                    if ($comment->insertComment()) {
                        http_response_code(201);
                        echo json_encode(array(
                            'status' => 'success',
                            'message' => 'Comment created successfully.'
                        ));
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
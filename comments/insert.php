<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/customer.php';
    include_once '../objects/comments.php';

    $database = new Database();
    $db = $database->getConnection();

    $customer = new DelCustomer($db);
    
    $data = json_decode(file_get_contents('php://input'));

    if (isset($data->step)) {
        if ($data->step === 1) {
            $comment = new SalesComment($db);
        } else {
            $comment = new DebtorsComment($db);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $customer->cust_id = $data->cust_id;
            $customer->comment = $data->date . " - ({$data->username}) - " . $data->comment;
            if ($customer->updateComment()) {
                $comment->cust_id = $data->cust_id;
                $comment->comment = $data->comment;
                $comment->date = $data->date;
                $comment->user = $data->user_id;
                $comment->data = $data->data;

                if ($data->step == 1) {
                    $comment->interactionOutcome = $data->interactionOutcome;
                    $comment->interactionType = $data->interactionType;
                }
                $comment->data = $data->data;
                $comment->data = $data->data;
                
                if ($comment->insertComment()) {
                    http_response_code(201);
                    echo json_encode(array(
                        'status' => 'success',
                        'message' => 'Comment was created successfully.'
                    ));
                } else {
                    http_response_code(503);
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Failed to create comment.'
                    ));
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
<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/customer.php';
    include_once '../objects/comments.php';
    include_once '../objects/debtors.php';

    $database = new Database();
    $db = $database->getConnection();

    $customer = new DelCustomer($db);
    $collect = new DebtCollect($db);
    $comment = new DebtorsComment($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $count = 0;
            foreach($data as $idx => $val) {
                $collect->collected = $val->collected;
                $collect->debt_id = $val->debt_id;
                $collect->comment = $val->comment;
                if ($collect->updateEntry()) {
                    $comment->cust_id = $val->cust_id;
                    $comment->comment = isset($val->comment) ? 'From CC:' . $val->comment : '';
                    $comment->date = $val->date;
                    $comment->user = $val->user;
                    if ($comment->insertComment()) {
                        $customer->comment = $val->date . ` - ({$val->username}) - ` . $val->comment;
                        $customer->cust_id = $val->cust_id;
                        if ($val->data === 1) {
                            if ($customer->updateComment()) {
                                $count++;
                            }
                        }
                    }
                }
            }

            if ($count > 0) {
                http_response_code(201);
                echo json_encode(
                    array(
                        'status' => 'success',
                        'message' => 'Successful entry of data.'
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
<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/user.php';
    include_once '../objects/workflow.php';
    include_once '../objects/comments.php';

    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);
    $history = new WorkflowHistory($db);
    $comment = new SalesComment($db);

    $data = json_decode(file_get_contents('php://input'));

    $history->date1 = isset($data->date1) ? $data->date1 : null;
    $history->date2 = isset($data->date2) ? $data->date2 : null;

    $comment->date0 = isset($data->date1) ? $data->date1 : null;
    $comment->date1 = isset($data->date2) ? $data->date2 : null;

    $stmt = $user->getActiveUsers();
    $num = $stmt->rowCount();

    $inquirytotal = 0;
    $quotetotal = 0;
    $ordertotal = 0;
    $purchasetotal = 0;
    $invoicingtotal = 0;
    $invoicedtotal = 0;
    $commenttotal = 0;

    if ($num > 0) {
        $user_arr = array();
        $user_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $user_item = array(
                'sales_id' => $sales_id,
                'sales_rep' => $sales_rep
            );

            if (isset($history->date1) && isset($history->date2)) {
                $inquiry = $history->readCount(1, $sales_id);
                $quote = $history->readCount(2, $sales_id);
                $order = $history->readCount(3, $sales_id);
                $purchase = $history->readCount(4, $sales_id);
                $invoicing = $history->readCount(5, $sales_id);
                $invoiced = $history->readCount(6, $sales_id);

                $user_item['inquiry'] = $inquiry->rowCount();
                $user_item['quote'] = $quote->rowCount();
                $user_item['order'] = $order->rowCount();
                $user_item['purchase'] = $purchase->rowCount();
                $user_item['invoicing'] = $invoicing->rowCount();
                $user_item['invoiced'] = $invoiced->rowCount();

                $inquirytotal += $inquiry->rowCount();
                $quotetotal += $quote->rowCount();
                $ordertotal += $order->rowCount();
                $purchasetotal += $purchase->rowCount();
                $invoicingtotal += $invoicing->rowCount();
                $invoicedtotal += $invoiced->rowCount();
            }

            if (isset($comment->date0) && isset($comment->date1)) {
                $commentCount = $comment->readCount($sales_id);

                $user_item['comment'] = $commentCount->rowCount();

                $commenttotal += $commentCount->rowCount();
            }

            array_push($user_arr['records'], $user_item);
        }

        array_push($user_arr['records'], array(
            'sales_id' => '99',
            'sales_rep' => 'TOTAL',
            'inquiry' => $inquirytotal,
            'quote' => $quotetotal,
            'order' => $ordertotal,
            'purchase' => $purchasetotal,
            'invoicing' => $invoicingtotal,
            'invoiced' => $invoicedtotal,
            'comment' => $commenttotal
        ));

        echo json_encode($user_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
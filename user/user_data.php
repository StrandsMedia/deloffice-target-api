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
                $data = $history->readCount($sales_id);

                $user_item['inquiry'] = $data['inquiry'];
                $user_item['quote'] = $data['quote'];
                $user_item['order'] = $data['order'];
                $user_item['purchase'] = $data['purchase'];
                $user_item['invoicing'] = $data['invoicing'];
                $user_item['invoiced'] = $data['invoiced'];

                $inquirytotal += +$data['inquiry'];
                $quotetotal += +$data['quote'];
                $ordertotal += +$data['order'];
                $purchasetotal += +$data['purchase'];
                $invoicingtotal += +$data['invoicing'];
                $invoicedtotal += +$data['invoiced'];
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
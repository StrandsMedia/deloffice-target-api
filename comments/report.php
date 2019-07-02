<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/comments.php';
    include_once '../objects/customer.php';

    $database = new Database();
    $db = $database->getConnection();

    $s_comment = new SalesComment($db);
    $d_comment = new DebtorsComment($db);

    $customer = new Customer($db);

    $s_comment->date0 = isset($_GET['d1']) ? $_GET['d1'] : die();
    $s_comment->date1 = isset($_GET['d2']) ? $_GET['d2'] : die();
    $s_comment->user = isset($_GET['u']) ? $_GET['u'] : die();

    $d_comment->date0 = isset($_GET['d1']) ? $_GET['d1'] : die();
    $d_comment->date1 = isset($_GET['d2']) ? $_GET['d2'] : die();
    $d_comment->user = isset($_GET['u']) ? $_GET['u'] : die();
    
    $stmt = $s_comment->report();
    $stmt2 = $d_comment->report();
    $num = $stmt->rowCount() + $stmt2->rowCount();

    if ($num > 0) {
        $comment_arr = array();
        $comment_arr['records'] = array();

        $sales_comms = array();
        $debt_comms = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $comment_item = array(
                'cd_id' => $cd_id,
                'cust_id' => $cust_id,
                'comment' => $comment,
                'date' => $date,
                'user' => $user,
                'taskBy' => $taskBy,
                'date2' => $date2,
                'sales_rep' => $sales_rep,
                'dept' => $dept,
                'data' => $data,
                // 'company_name' => $company_name,
                'type' => 'sales'
            );

            $custdata = $customer->getCustDetails($data, $cust_id);

            $comment_item['company_name'] = $custdata['company_name'];

            array_push($sales_comms, $comment_item);
        }

        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {

            $comment_item = array(
                'cd_id' => $row2['cd_id'],
                'cust_id' => $row2['cust_id'],
                'comment' => $row2['comment'],
                'date' => $row2['date'],
                'user' => $row2['user'],
                'taskBy' => $row2['taskBy'],
                'date2' => $row2['date2'],
                'sales_rep' => $row2['sales_rep'],
                'dept' => $row2['dept'],
                'data' => $data,
                // 'company_name' => $row2['company_name'],
                'type' => 'debtors'
            );

            $custdata = $customer->getCustDetails($data, $cust_id);

            $comment_item['company_name'] = $custdata['company_name'];

            array_push($debt_comms, $comment_item);
        }
        $comment_arr['records'] = array_merge($sales_comms, $debt_comms);

        usort($comment_arr['records'], function($a, $b) {
            strcmp($a->date2, $b->date2);
        });

        $comment_arr['status'] = 'okay';

        echo json_encode($comment_arr);
    } else {
        echo json_encode(
            array(
                'status' => 'error',
                'message' => 'No records found. Please try again with a different query.'
            )
        );
    }
?>
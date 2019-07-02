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

    $comment = isset($_GET['s']) ? new SalesComment($db) : new DebtorsComment($db);

    $customer = new Customer($db);
    
    $optional = array();
    $optional['user'] = isset($_GET['u']) ? $_GET['u'] : null;
    $optional['cust'] = isset($_GET['c']) ? $_GET['c'] : null;
    $optional['data'] = isset($_GET['d']) ? $_GET['d'] : 1;
    
    $stmt = $comment->read($optional);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $comment_arr = array();
        $comment_arr['records'] = array();

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
                'data' => $data
            );

            $custdata = $customer->getCustDetails($data, $cust_id);

            $comment_item['company_name'] = $custdata['company_name'];

            array_push($comment_arr['records'], $comment_item);
        }

        echo json_encode($comment_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
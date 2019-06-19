<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/debtors.php';

    $database = new Database();
    $db = $database->getConnection();

    $remcomments = new DebtReminderComment($db);

    $remcomments->dbt_rem_id = isset($_GET['id']) ? $_GET['id'] : die();

    $stmt = $remcomments->read();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $reminder_arr = array();
        $reminder_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $comment_item = array(
                'dbt_comm_id' => $dbt_comm_id,
                'dbt_rem_id' => $dbt_rem_id,
                'dbt_comment' => $dbt_comment,
                'sales_id' => $sales_id,
                'sales_rep' => $sales_rep,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($reminder_arr['records'], $comment_item);
        }

        http_response_code(200);
        echo json_encode($reminder_arr);
    } else {
        http_response_code(404);
        echo json_encode(array(
            'message' => 'No entries found.',
            'records' => array()
        ));
    }

?>
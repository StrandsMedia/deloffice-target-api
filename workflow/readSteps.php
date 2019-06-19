<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once '../config/db.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $step = new WorkflowStep($db);

    $stmt = $step->read();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $steps_arr = array();
        $steps_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $step_item = array(
                'step_id' => $step_id,
                'id' => $id,
                'step' => $step,
                'status' => $status
            );

            array_push($steps_arr['records'], $step_item);
        }

        http_response_code(200);
        echo json_encode($steps_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
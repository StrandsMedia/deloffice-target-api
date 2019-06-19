<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once '../config/db.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $paper = new WorkflowPaper($db);

    $stmt = $paper->get();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $paper_arr = array();
        $paper_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $paper_item = array(
                'paperID' => $paperID,
                'paperBrand' => $paperBrand
            );

            array_push($paper_arr['records'], $paper_item);
        }

        http_response_code(200);
        echo json_encode($paper_arr);
    } else {
        echo json_encode(
            array('message' => 'No records found.')
        );
    }
?>
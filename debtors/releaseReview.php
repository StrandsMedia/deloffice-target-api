<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/debtors.php';

    $database = new Database();
    $db = $database->getConnection();

    $debt_review = new DebtorsReview($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $count = 0;
            foreach($data->dr_id as $item => $index) {
                $debt_review->dr_id = $index;
                $debt_review->reviewPeriod = $data->reviewPeriod;
                if ($debt_review->release()) {
                    $count++;
                }
            }

            if ($count == sizeof($data->dr_id)) {
                http_response_code(200);
                echo json_encode(
                    array(
                        'status' => 'success',
                        'message' => 'Insert was successful.'
                    )
                );
            } else {
                http_response_code(503);
                echo json_encode(
                    array(
                        'status' => 'success',
                        'message' => 'Failed to insert.'
                    )
                );
            }
        }
    }
?>
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
            // $debt_review->reviewPeriod = $data->reviewPeriod;
            $debt_review->user = $data->user;

            if (is_array($data->dc_id)) {
                $count = 0;
                foreach($data->dc_id as $item => $index) {
                    $debt_review->dc_id = $index;
                    if ($debt_review->isInReview() === null) {
                        if ($debt_review->insert()) {
                            $count++;
                        }
                    } else {
                        $debt_review->dr_id = $debt_review->isInReview();
                        if ($debt_review->reactivate()) {
                            $count++;
                        }
                    }
                }

                if ($count == sizeof($data->dc_id)) {
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

        } else {
            http_response_code(503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Data was not found or is incomplete. Please try again later.'
            ));
        }
    }

?>
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
    $control = new DebtorsControl($db);
    $control_cmt = new DebtorsCtrlCmt($db);

    $remn = new DebtReminder($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $debt_review->active = $data->active;
            $debt_review->user = $data->user;

            $stmt1 = $debt_review->read(1);
            $stmt2 = $debt_review->read(2);
            $stmt3 = $debt_review->read(3);

            $num = $stmt1->rowCount() + $stmt2->rowCount() + $stmt3->rowCount();

            if ($num > 0) {
                $review_arr = array();
                $review_arr['records'] = array();

                $temp_array = array();

                while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $review_item = array(
                        'dr_id' => $dr_id,
                        'cust_id' => $cust_id,
                        'company_name' => $company_name,
                        'company' => 'DEL',
                        'dc_id' => $dc_id,
                        'active' => $active,
                        'data' => $data,
                        'user' => $user,
                        'status' => $control->getStatus($status),
                        'reviewAt' => $reviewAt,
                        'reviewPeriod' => $reviewPeriod,
                        'comment' => $control_cmt->getLastComment($dc_id),
                        'reminder_procedure' => $remn->readAvailable(1, $cust_id),
                    );

                    array_push($temp_array, $review_item);
                }
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    extract($row2);
                    $review_item2 = array(
                        'dr_id' => $dr_id,
                        'cust_id' => $cust_id,
                        'company_name' => $company_name,
                        'company' => 'RNS',
                        'dc_id' => $dc_id,
                        'active' => $active,
                        'data' => $data,
                        'user' => $user,
                        'status' => $control->getStatus($status),
                        'reviewAt' => $reviewAt,
                        'reviewPeriod' => $reviewPeriod,
                        'comment' => $control_cmt->getLastComment($dc_id),
                        'reminder_procedure' => $remn->readAvailable(1, $cust_id),
                    );

                    array_push($temp_array, $review_item2);
                }
                while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                    extract($row3);
                    $review_item3 = array(
                        'dr_id' => $dr_id,
                        'cust_id' => $cust_id,
                        'company_name' => $company_name,
                        'company' => 'PNP',
                        'dc_id' => $dc_id,
                        'active' => $active,
                        'data' => $data,
                        'user' => $user,
                        'status' => $control->getStatus($status),
                        'reviewAt' => $reviewAt,
                        'reviewPeriod' => $reviewPeriod,
                        'comment' => $control_cmt->getLastComment($dc_id),
                        'reminder_procedure' => $remn->readAvailable(1, $cust_id),
                    );

                    array_push($temp_array, $review_item3);
                }

                $debt_review->array_sort_by_column($temp_array, 'dr_id', SORT_DESC);
                $review_arr['records'] = $temp_array;

                http_response_code(200);
                echo json_encode($review_arr);
            } else {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'No records found.',
                    'records' => array()
                ));
            }
            
        } else {
            http_response_code(503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Data was not found or is incomplete. Please try again later.',
                'records' => array()
            ));
        }
    }
?>
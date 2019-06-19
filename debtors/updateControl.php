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

    $control = new DebtorsControl($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $control->dispute = $data->dispute;
            $control->inv = $data->inv;
            $control->dn = $data->dn;
            $control->crn = $data->crn;
            $control->oth = $data->oth;
            $control->adjs = $data->adjs;
            $control->issue = $data->issue;
            $control->cheque = $data->cheque;
            $control->payment = $data->payment;
            $control->status = $data->status;
            $control->dc_id = $data->dc_id;

            if ($control->update()) {
                http_response_code(200);
                echo json_encode(
                    array(
                        'status' => 'success',
                        'message' => 'Successful update of data.'
                    )
                );
            } else {
                http_response_code(404);
                echo json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'Failed to update entry.'
                    )
                );
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
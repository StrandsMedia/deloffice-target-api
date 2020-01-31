<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/invoice.php';
    include_once '../objects/proforma.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $invoice = new Invoice($db);
    $lines = new InvoiceLines($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $checkCount = 0;
            if ($data->status == 1) {
                foreach($data->entries as $item) {
                    if ($item->checked != 1) {
                        if ($item->required == $item->onhand) {
                            $lines->invlineid = $item->invlineid;
                            if ($lines->markChecked()) {
                                $checkCount++;
                            } else {
                                http_response_code(503);
                                echo json_encode(array(
                                    'status' => 'error',
                                    'message' => "Failed to save. Please try again later."
                                ));
                            }
                        } else {
                            $lines->purchase = $item->purchase;
                            $lines->checked = 0;
                            $lines->transfer = $item->transfer;
                            $lines->amend = $item->missing;
                            $lines->invlineid = $item->invlineid;
                            if ($lines->updateList()) {
                                $invoice->workflow_id = $data->workflow_id;
                                if ($item->missing > 0) {
                                    if ($invoice->changeAmendStatus(1)) {
                                        $checkCount++;
                                    }
                                } else {
                                    $checkCount++;
                                }
                            } else {
                                http_response_code(503);
                                echo json_encode(array(
                                    'status' => 'error',
                                    'message' => "Failed to save. Please try again later."
                                ));
                            }
                        }
                    }
                }
            } else if ($data->status == 2) {
                foreach($data->entries as $item) {
                    if ($item->verified != 1) {
                        $lines->invlineid = $item->invlineid;
                        if ($lines->markVerified()) {
                            $checkCount++;
                        } else {
                            http_response_code(503);
                            echo json_encode(array(
                                'status' => 'error',
                                'message' => "Failed to save. Please try again later."
                            ));
                        }
                    }
                }
            }
            

            http_response_code(200);
            echo json_encode(array(
                'status' => 'success',
                'message' => "{$checkCount} entries have been updated."
            ));

        } else {
            http_response_code(503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Data was not found or is incomplete. Please try again later.'
            ));
        }
    }
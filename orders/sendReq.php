<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/invoice.php';
    include_once '../objects/proforma.php';
    include_once '../objects/purchases.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $invoice = new Invoice($db);
    $lines = new InvoiceLines($db);

    $proformahist = new ProformaHistory($db);

    $request = new PurchaseRequest($db);
    $request_prod = new PurchaseRequestProds($db);

    $init = new PurchaseInit($db);
    $init_prod = new PurchaseInitProds($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $lines->invlineid = $data->invlineid;
            $lines->amendstatus = $data->status;

            if ($lines->markStatus(+$data->case)) {
                $request->cust_id = $data->cust_id;
                $request->data = $data->data;
                $request->type = +$data->case;
                $request->completed = 0;
                $request->workflow_id = $data->workflow_id;
                $req_id = $request->getExisting();

                if (!isset($req_id)) {
                    if ($request->create()) {
                        $request_prod->req_id = $request->getExisting();
                        $request_prod->p_id = $data->prod->p_id;
                        $request_prod->qty = $data->qty;
                        if ($request_prod->create()) {
                            http_response_code(200);
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => "All entries have been updated."
                            ));
                        }
                    }
                } else {
                    $request_prod->req_id = $req_id;
                    $request_prod->p_id = $data->prod->p_id;
                    $request_prod->qty = $data->qty;
                    if ($request_prod->create()) {
                        http_response_code(200);
                        echo json_encode(array(
                            'status' => 'success',
                            'message' => "All entries have been updated."
                        ));
                    }
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
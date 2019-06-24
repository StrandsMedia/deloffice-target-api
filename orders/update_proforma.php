<?php
error_reporting(E_ALL);
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/invoice.php';
    include_once '../objects/workflow.php';

    $database = new Database();
    $db = $database->getConnection();

    $workflow = new Workflow($db);
    $invoice = new Invoice($db);
    $invlines = new InvoiceLines($db);

    $data = json_decode(file_get_contents('php://input'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $invoice->company_name = $data->company_name;
            $invoice->Contact_Person = $data->Contact_Person;
            $invoice->Telephone = $data->Telephone;
            $invoice->Physical1 = $data->Physical1;
            $invoice->Physical2 = $data->Physical2;
            $invoice->Physical3 = $data->Physical3;
            $invoice->Physical4 = $data->Physical4;
            $invoice->Registration = $data->Registration;
            $invoice->Tax_Number = $data->Tax_Number;
            $invoice->customerCode = $data->customerCode;
            $invoice->iARPriceListNameID = isset($data->iARPriceListNameID) ? $data->iARPriceListNameID : null;
            $invoice->TotalExcl = $data->TotalExcl;
            $invoice->TotalTax = $data->TotalTax;
            $invoice->TotalIncl = $data->TotalIncl;
            $invoice->InvDate = $data->InvDate;
            $invoice->InvStatus = $data->InvStatus;
            $invoice->DCLink = $data->DCLink;
            $invoice->user = $data->user;
            $invoice->edited = isset($data->edited) ? $data->edited : 0;
            $invoice->workflow_id = $data->workflow_id;
            $invoice->invNumber = $data->invNumber;
            $invoice->poNumber = $data->poNumber;
            $invoice->notes = $data->notes;
            $invoice->invRef = $data->invRef;
            $invoice->invoice_id = $data->invoice_id;
            
            if ($invoice->updateInvoice()) {
                $count = 0;

                foreach($data->entries as $entry) {
                    $invlines->invoice_id = isset($data->invoice_id) ? $data->invoice_id : null;
                    $invlines->line_id = isset($entry->line_id) ? $entry->line_id : null;
                    $invlines->AveUCst = $entry->AveUCst;
                    $invlines->Description_1 = $entry->Description_1;
                    $invlines->Description_2 = $entry->Description_2;
                    $invlines->Description_3 = $entry->Description_3;
                    $invlines->Qty_On_Hand = $entry->Qty_On_Hand;
                    $invlines->StockLink = $entry->StockLink;
                    $invlines->TaxRate = $entry->TaxRate;
                    $invlines->idTaxRate = $entry->idTaxRate;
                    $invlines->fExclPrice = $entry->fExclPrice;
                    $invlines->p_id = $entry->p_id;
                    $invlines->qty = $entry->qty;
                    $invlines->pricecat = $entry->pricecat;
                    $invlines->fExclPrice2 = $entry->fExclPrice2;

                    $invlines->invlineid = isset($entry->invlineid) ? $entry->invlineid : null;
                    
                    switch ($entry->status) {
                        case 'new':
                            if ($invlines->createInvLine()) {
                                $count++;
                            }
                            break;
                        case 'existing':
                            if ($invlines->updateInvLine()) {
                                $count++;
                            }
                            break;
                        case 'delete':
                            if ($invlines->deleteInvLine()) {
                                $count++;
                            }
                            break;
                    }
                }

                if (sizeof($data->entries) == $count) {
                    http_response_code(200);
                    echo json_encode(array(
                        'status' => 'success',
                        'message' => 'Proforma invoice updated successfully.'
                    ));
                } else {
                    http_response_code(200);
                    echo json_encode(array(
                        'status' => 'success',
                        'message' => 'Failed to update invoice.'
                    ));
                }
            }
        } else {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Data was not found or is incomplete. Please try again later.'
            ));
        }
    }

?>
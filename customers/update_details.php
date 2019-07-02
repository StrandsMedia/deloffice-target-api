<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    include_once '../config/db.php';
    include_once '../objects/customer.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $data = json_decode(file_get_contents("php://input"));

    switch (+$data->data) {
        case 1:
            $customer = new DelCustomer($db);
            break;
        case 2: 
            $customer = new RnsCustomer($db);
            break;
        case 3: 
            $customer = new PnpCustomer($db);
            break;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $customer->cust_id = $data->cust_id;
            $customer->contact_person = $data->contact_person;
            $customer->tel = $data->tel;
            $customer->fax = $data->fax;
            $customer->mob = $data->mob;
            $customer->email = $data->email;
    
            if ($customer->updateDetails($data->id)) {
                http_response_code(200);
                echo json_encode(array(
                    "message" => "Customer details were updated."
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "message" => "Unable to update customer details."
                ));
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                "message" => "Unable to update customer details."
            ));
        }
    }


?>
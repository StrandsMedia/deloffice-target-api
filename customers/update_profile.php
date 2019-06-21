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
    
    $customer = new DelCustomer($db);
    
    $data = json_decode(file_get_contents("php://input"));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
    
            $customer->cust_id = $data->cust_id;
            $customer->company_name = $data->company_name;
            $customer->customerCode = $data->customerCode;
            $customer->address = $data->address;
            $customer->address2 = $data->address2;
            $customer->address3 = $data->address3;

            $customer->location = $data->location;
            $customer->location2 = $data->location2;
            $customer->location3 = $data->location3;
            
            $customer->category = $data->category;
            $customer->sector = $data->sector;
            $customer->subsector = $data->subsector;
            
    
            if ($customer->updateProfile()) {
                http_response_code(200);
                echo json_encode(array(
                    "message" => "Customer profile was updated."
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "message" => "Unable to update customer profile."
                ));
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                "message" => "Unable to update customer profile."
            ));
        }
    }


?>
<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/customer.php';

    $database = new Database();
    $db = $database->getConnection();

    $data = json_decode(file_get_contents('php://input'));

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

    $customer->customerCode = $data->customerCode;
    $customer->company_name = $data->company_name;
    $customer->address = $data->address;

    $customer->location = $data->location;

    $customer->category = $data->category;
    $customer->sector = $data->sector;
    $customer->subsector = $data->subsector;
    $customer->tel = $data->tel;
    $customer->fax = $data->fax;
    $customer->mob = $data->mob;
    $customer->email = $data->email;

    if (isset($data)) {
        if ($customer->create()) {
            echo json_encode(array(
                'message' => 'Customer was successfully created.'
            ));
        } else {
            echo json_encode(array(
                'message' => 'Unable to create product.'
            ));
        }
    }
?>
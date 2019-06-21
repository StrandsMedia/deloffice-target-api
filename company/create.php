<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../config/db.php';
    include_once '../objects/company.php';

    $database = new Database();
    $db = $database->getConnection();

    $company = new Company($db);

    $data = json_decode(file_get_contents("php://input"));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $company->companyName = $data->companyName;
            $company->companyReference = $data->companyReference;

            if ($company->create()) {
                http_response_code(201);
    
                echo json_encode(array(
                    'status' => 'success',
                    'message' => 'Company was successfully created'
                ));
            } else {
                http_response_code(503);
    
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Unable to create company'
                ));
            }
        } else {
            http_response_code(400);
    
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Unable to create company. No data was found.'
            ));
        }
    }
?>
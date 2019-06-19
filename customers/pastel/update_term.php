<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../../config/db.php';
    include_once '../../objects/pastel.php';

    $srvdatabase = new ServerDatabase();
    $srvdb = $srvdatabase->getConnection();

    $client = new Client($srvdb);

    $data = json_decode(file_get_contents("php://input"));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $client->DCLink = $data->DCLink;
            $client->AccountTerms = $data->term;

            if ($client->updateTerm()) {
                http_response_code(200);
                echo json_encode(array(
                    "message" => "Account terms were updated."
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "message" => "Unable to update account terms."
                ));
            }
        } else {
            http_response_code(503);
            echo json_encode(array(
                "message" => "Unable to update account terms."
            ));
        }
    }
?>
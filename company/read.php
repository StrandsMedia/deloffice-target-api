<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    include_once '../config/db.php';
    include_once '../objects/company.php';

    $database = new Database();
    $db = $database->getConnection();

    $company = new Company($db);
    $companyId = null;

    $stmt = $company->read($companyId);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $company_arr = array();
        $company_arr['records'] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $company_item = array(
                'companyId' => $companyId,
                'companyName' => $companyName,
                'companyReference' => $companyReference,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt
            );

            array_push($company_arr['records'], $company_item);
        }

        echo json_encode($company_arr);
    } else {
        echo json_encode(
            array('message' => 'No companies found.')
        );
    }
?>
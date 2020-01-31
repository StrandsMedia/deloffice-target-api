<?php
    ini_set('upload_max_filesize', '100M');
    ini_set('post_max_size', '100M');

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';

    $database = new Database();
    $db = $database->getConnection();

        $localdir = "../../poscans/";

        if (isset($_FILES['file'])) {
            if (!is_array($_FILES['file']['name'])) {
                $fileName = $_FILES['file']['name'];

                $localFile = $localdir.$fileName;

                //checking if file exsists
                if (file_exists($localFile)) unlink($localFile);

                if (move_uploaded_file($_FILES['file']['tmp_name'], $localFile)) {
                    http_response_code(200);
                    echo json_encode(array(
                        'message' => 'File was uploaded successfully'
                    ));
                } else {
                    http_response_code(503);
                    echo json_encode(array(
                        'message' => 'Unable to upload file at the moment.'
                    ));
                    
                }
            }
        } else {
            echo json_encode(array(
                'message' => 'File to upload not found.'
            ));
        }

    
?>
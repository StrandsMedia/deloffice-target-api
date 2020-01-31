<?php
    ini_set('upload_max_filesize', '100M');
    ini_set('post_max_size', '100M');

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/ftp.php';
    include_once '../config/db.php';
    include_once '../objects/products.php';

    $database = new Database();
    $db = $database->getConnection();

    $product = new Products($db);

    $ftp = new DelOfficeFTP('ftp.deloffice.mu', 'deloffice', 'WanLtd2018*');
    if ($ftp) {
        $localdir = "../../imgdir/";
        $remotedir = "/public_html/home/images/img_large/";

        if (isset($_FILES['file'])) {
            if (!is_array($_FILES['file']['name'])) {
                $fileName = $_FILES['file']['name'];

                $remoteFile = $remotedir.$fileName;
                $localFile = $localdir.$fileName;
                $dir = '/public_html/home/images/img_large';

                //checking if file exsists
                if (file_exists($localFile)) unlink($localFile);

                if (move_uploaded_file($_FILES['file']['tmp_name'], $localFile)) {
                    $product->p_id = explode('.', $fileName)[0];
                    if ($product->watsProduct() !== 'P') {
                        if ($ftp->ftpCopyFile($remoteFile, $localFile, $dir)) {
                            http_response_code(200);
                            echo json_encode(array(
                                'message' => $product->watsProduct() . 'File was uploaded successfully'
                            ));
                        } else {
                            http_response_code(503);
                            echo json_encode(array(
                                'message' => 'Unable to upload file at the moment.'
                            ));
                        }
                    } else {
                        http_response_code(200);
                        echo json_encode(array(
                            'message' => 'File was uploaded successfully'
                        ));
                    }
                } else {
                    $product->p_id = explode('.', $fileName)[0];
                    if ($product->watsProduct() !== 'P') {
                        if ($ftp->ftpCopyFile($remoteFile, $localFile, $dir)) {
                            http_response_code(200);
                            echo json_encode(array(
                                'message' => $product->watsProduct() . 'File was uploaded successfully'
                            ));
                        } else {
                            http_response_code(503);
                            echo json_encode(array(
                                'message' => 'Unable to upload file at the moment.'
                            ));
                        }
                    } else {
                        http_response_code(200);
                        echo json_encode(array(
                            'message' => 'File was uploaded successfully'
                        ));
                    }
                }
            }
        } else {
            
        }
        $ftp->ftpClose();
    } else {
        http_response_code(503);
        echo json_encode(
            array(
                'message' => 'Failed to connect to FTP Server'
            )
        );
    }

    
?>
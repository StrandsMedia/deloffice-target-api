<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: access');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');

    $searchstring = isset($_GET['s']) ? $_GET['s'] . '.jpg' : die();

    $dir = '../../imgdir';
    $files = array_diff(scandir($dir, 1), array('..', '.'));

    $result = array();

    foreach ($files as $key => $value) {
        if ($value === $searchstring) {
            $result_item = array(
                'name' => $value
            );
            array_push($result, $result_item);
        }
    }

    if (sizeof($result) > 0) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(array(
            'message' => 'File not found.'
        ));
    }

?>
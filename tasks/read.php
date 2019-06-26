<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../config/db.php';
    include_once '../objects/notification.php';

    $database = new Database();
    $db = $database->getConnection();

    $tasks = new UserTasks($db);

    $data = json_decode(file_get_contents('php://input'));
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($data)) {
            $tasks->user = +$data->user;

            $num = +$data->num;
        
            $stmt = $tasks->read($num);
            $num = $stmt->rowCount();
        
            if ($num > 0) {
                $tasks_arr = array();
                $tasks_arr['records'] = array();
        
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
        
                    $task_item = array(
                        'task_id' => $task_id,
                        'taskname' => $taskname,
                        'username' => $username,
                        'userassigned' => $userassigned,
                        'status' => $status,
                        'user' => $user,
                        'assignedTo' => $assignedTo,
                        'origin' => $origin,
                        'acknowledged' => $acknowledged,
                        'createdAt' => $createdAt,
                        'updatedAt' => $updatedAt
                    );
        
                    array_push($tasks_arr['records'], $task_item);
                }
                http_response_code(200);
                echo json_encode($tasks_arr);
            } else {
                // http_response_code(404);
                echo json_encode(
                    array('message' => 'No records found.')
                );
            }
        }
    }
?>
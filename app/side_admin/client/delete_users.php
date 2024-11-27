<?php
// -------
// -------------- TESTING
// -------

// POST http://localhost/course/6_CRM/app/side_admin/client/delete_users.php
// Content-Type: application/json

// {
//     "user_id": "5"
// }

// -------------------------------------------------------------

// CORS POLICY
include __DIR__ . "/../../../classes/CORS.php";

// DATABASE CONNECTION
require_once __DIR__ . "/../../../config/db_connection.php";
if ($conn === false) {
    echo json_encode(["ERROR" => "Database connection failed"]);
    exit();
}

// ADMIN_MANAGER CLASS
include __DIR__ . "/../../../classes/AdminManager.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["user_id"])){
        $id = $data["user_id"];
        $user_delete = new AdminManager($conn);
        $delete = $user_delete->deleteUsers($id);
        if($delete){
            echo json_encode(["SUCCESS" => "User deleted successfully"]);
        } else {
            echo json_encode(["ERROR" => "This user doesn't exist in the database."]);
        }
    }

} else {
    echo json_encode(["ERROR" => "Method Not Allowed"]);
}

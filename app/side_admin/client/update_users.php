<?php
// -------
// -------------- TESTING
// -------

// POST http://localhost/course/6_CRM/app/side_admin/client/update_users.php
// Content-Type: application/json

// {
//     "user_id": "1",
//     "user_username": "ali",
//     "user_email": "ali@mail.com",
//     "user_address": "egypt",
//     "user_url": "ali.website"
// }

// -------------------------------------------------------------

// DATABASE CONNECTION
require_once __DIR__ . "/../../../config/db_connection.php";
if ($conn === false) {
    echo json_encode(["ERROR" => "Database connection failed"]);
    exit();
}

include __DIR__ . "/../../../classes/CORS.php"; // CORS POLICY
include __DIR__ . "/../../../classes/AdminManager.php"; // ADMIN_MANAGER CLASS
include __DIR__ . "/../../../classes/Authentication.php"; // AUTHENTICATION CLASS

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["user_username"]) && isset($data["user_email"]) && isset($data["user_address"]) && isset($data["user_url"]) && isset($data["user_id"])) {
        $id = $data["user_id"];
        $username = $data["user_username"];
        $email = $data["user_email"];
        $address = $data["user_address"];
        $url = $data["user_url"];

        $check = new Authentication($conn);
        $check_user_exists = $check->checkUserExists($id, $username, $email, $url);
        if (!$check_user_exists) {
            $update = new AdminManager($conn);
            $user_update = $update->updateUsers($id, $username, $email, $address, $url);
            if ($user_update) {
                echo json_encode(["SUCCESS" => "User data is fully updated"]);
            } else {
                echo json_encode(["ERROR" => "An error occurred while updating user data"]);
            }
        } else {
            // User exists in database
            echo json_encode(["ERROR" => $check_user_exists]);
        }
    } else {
        echo json_encode(["ERROR" => "All data are required"]);
    }
} else {
    echo json_encode(["ERROR" => "Method Not Allowed"]);
}

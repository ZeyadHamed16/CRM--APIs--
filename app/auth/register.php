<?php

// -------
// -------------- TESTING
// -------

// POST http://localhost/course/6_CRM/app/auth/register.php
// Content-Type: application/json

// {
//     "user_username": "amr",
//     "user_email": "amr@mail.com",
//     "user_address": "egypt",
//     "user_url": "amr.website",
//     "user_password": "amr123"
// }

// -------------------------------------------------------------

// CORS POLICY
include __DIR__ . "/../../classes/CORS.php";

// DATABASE CONNECTION
require_once __DIR__ . "/../../config/db_connection.php";
if ($conn === false) {
    echo json_encode(["ERROR" => "Database connection failed"]);
    exit();
}

// AUTHENTICATION CLASS
include __DIR__ . "/../../classes/Authentication.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // DECODE DATA FROM [ JSON ] TO [ Associative Array ]
    $data = json_decode(file_get_contents("php://input"), true);

    // REQUIRED DATA
    if (isset($data["user_id"]) && isset($data["user_username"]) && isset($data["user_email"]) && isset($data["user_address"]) && isset($data["user_url"]) && isset($data["user_password"])) {
        $id = $data["user_id"];
        $username = htmlspecialchars($data['user_username']);
        $email = filter_var($data['user_email'], FILTER_VALIDATE_EMAIL);
        $address = $data["user_address"];
        $url = $data["user_url"];
        $password = $data["user_password"];

        $users = new Authentication($conn);
        // CHECK IF THE USER EXISTS OR NOT
        $check_user_exists = $users->checkUserExists($id, $username, $email, $url);
        // [ IF USER EXISTS ] RETURNS:
        // Username already exists __OR__ Email already exists __OR__ URL already exists
        // [ IF USER NOT EXISTS ] RETURNS:
        // FALSE => So that ths user not exists in database and create a new one
        if (!$check_user_exists) {
            // CREATE NEW USER INTO DATABASE
            $create_new_user = $users->createUser($username, $email, $address, $url, $password);
            if ($create_new_user) {
                session_start();
                $_SESSION["username"] = $username;
                echo json_encode(["success" => "Added new user named: $username and created a default project"]);
            } else {
                echo json_encode(['ERROR' => 'Registration failed']);
            }
        } else {
            // User exists in database
            echo json_encode(["ERROR" => $check_user_exists]);
        }
    } else {
        echo json_encode(["ERROR" => "All data are required"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["ERROR" => "Method Not Allowed"]);
}

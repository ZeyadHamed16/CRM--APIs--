<?php

// -------
// -------------- TESTING
// -------

// GET http://localhost/course/6_CRM/app/side_admin/client/show_users.php
// Content-Type: application/json

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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $user_list = new AdminManager($conn);
    $data = $user_list->getAllUsers();
    echo json_encode($data);
} else {
    echo json_encode(["ERROR" => "Invalid request method"]);
}

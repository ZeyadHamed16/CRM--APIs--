<?php

// -------
// -------------- TESTING
// -------

// GET http://localhost/course/6_CRM/app/side_admin/home/home_page.php
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

// CountManager CLASS
include __DIR__ . "/../../../classes/CountManager.php";

session_start();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $count = new CountManager($conn);
    $num_of_users = $count->getUsersCount();
    $num_of_pending_proj = $count->getPendingProjectsCount();
    $num_of_completed_proj = $count->getCompletedProjectsCount();
    echo json_encode([
        "number_of_users" => $num_of_users,
        "pending_projects" => $num_of_pending_proj,
        "completed_projects" => $num_of_completed_proj
    ]);
} else {
    echo json_encode(["ERROR" => "Method Not Allowed"]);
}

<?php

// -------
// -------------- TESTING
// -------

// POST http://localhost/course/6_CRM/app/side_user/tickets/send_ticket.php
// Content-Type: application/json

// {
//     "ticket_user_id": 2,
//     "ticket_heading": "Login Issue",
//     "ticket_description": "The user is experiencing problems logging in. The password reset function is not working."
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
    $data = json_decode(file_get_contents("php://input"), true); // Decode JSON as an associative array
    if (isset($data["ticket_user_id"]) && isset($data["ticket_heading"]) && isset($data["ticket_description"])) {
        $user_id = $data["ticket_user_id"];
        $heading = $data["ticket_heading"];
        $description = $data["ticket_description"];

        $controller = new AdminManager($conn);
        $insert_new_ticket = $controller->createTickets($user_id, $heading, $description);

        if ($insert_new_ticket) {
            echo json_encode(["SUCCESS" => "Ticket sent successfully"]);
        } else {
            echo json_encode(["ERROR" => "User does not exist"]);
        }
    } else {
        echo json_encode(["ERROR" => "All fields are required"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["ERROR" => "Method Not Allowed"]);
}

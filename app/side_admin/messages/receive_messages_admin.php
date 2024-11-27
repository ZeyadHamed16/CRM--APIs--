<?php
// -------
// -------------- TESTING
// -------

// POST http://localhost/course/6_CRM/app/side_admin/messages/receive_messages_admin.php
// Content-Type: application/json

// {
//     "admin_id": "1"
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

// MessageManager CLASS
include __DIR__ . "/../../../classes/MessageManager.php";

session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['admin_id'])) {
        $admin_id = $data['admin_id'];

        // Initialize the MessageManager and connect to the database
        $messageManager = new MessageManager($conn);

        // Fetch the messages for the admin
        $messages = $messageManager->adminReceiveMessages($admin_id);
        echo json_encode($messages);
    } else {
        echo json_encode(['ERROR' => 'Missing required fields']);
    }
} else {
    echo json_encode(["ERROR" => "Method Not Allowed"]);
}
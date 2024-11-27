<?php
// -------
// -------------- TESTING
// -------

// POST http://localhost/course/6_CRM/app/side_user/messages/send_messages_user.php
// Content-Type: application/json

// {
//     "user_id": "1",
//     "admin_id": "1",
//     "message_content": "Hello new user"
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
    if (isset($data['user_id']) && isset($data['admin_id']) && isset($data['message_content'])) {
        $user_id = $data['user_id'];
        $admin_id = $data['admin_id'];
        $message_content = $data['message_content'];

        // Initialize the MessageManager and connect to the database
        $messageManager = new MessageManager($conn);

        // Send the message
        $result = $messageManager->userSendMessage($user_id, $admin_id, $message_content);

        if ($result) {
            echo json_encode(["SUCCESS" => "Message sent successfully"]);
        } else {
            echo json_encode(["ERROR" => "Message sending failed"]);
        }
    } else {
        echo json_encode(['ERROR' => 'Missing required fields']);
    }
} else {
    echo json_encode(["ERROR" => "Method Not Allowed"]);
}

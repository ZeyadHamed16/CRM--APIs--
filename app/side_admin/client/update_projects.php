<?php
// -------
// -------------- TESTING
// -------

// POST http://localhost/course/6_CRM/app/side_admin/client/update_projects.php
// Content-Type: application/json

// {
//     "proj_user_id": 1,
//     "projectUpdates": {
//         "Account Managment": {
//             "status": "In process",
//             "description": "Updated description for Account Management"
//         },
//         "Design": {
//             "status": "Completed",
//             "description": "Design phase complete"
//         },
//         "Programming": {
//             "status": "Pending",
//             "description": "Programming phase to be started"
//         }
//     }
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

    // Check if required fields are present
    if (isset($data["proj_user_id"], $data["projectUpdates"])) {
        $user_id = $data["proj_user_id"];
        $projectUpdates = $data["projectUpdates"];

        // Initialize AdminManager and update project
        $projectManager = new AdminManager($conn);
        $updateStatus = $projectManager->updateProjects($user_id, $projectUpdates);

        if ($updateStatus) {
            echo json_encode(["SUCCESS" => "Projects updated successfully"]);
        } else {
            echo json_encode(["ERROR" => "Failed to update projects"]);
        }
    } else {
        echo json_encode(["ERROR" => "Missing required fields"]);
    }
} else {
    echo json_encode(["ERROR" => "Method Not Allowed"]);
}

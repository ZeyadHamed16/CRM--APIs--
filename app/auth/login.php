<?PHP

// -------
// -------------- TESTING
// -------

// POST http://localhost/course/6_CRM/app/auth/login.php
// Content-Type: application/json

// {
//     "user_email": "amr@mail.com",
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

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["user_email"]) && isset($data["user_password"])) {
        $email = $data["user_email"];
        $password = $data["user_password"];

        $user_auth = new Authentication($conn);

        $result = $user_auth->verifyEmailPassword($email, $password);

        if ($result === true) {
            echo json_encode(['SUCCESS' => "loged In"]);
        } elseif ($result === 'email_exists') {
            echo json_encode(['ERROR' => 'Invalid email address']);
        } elseif ($result === 'password_exists') {
            echo json_encode(['ERROR' => 'Incorrect password']);
        } else {
            echo json_encode(['ERROR' => 'Something wrong while logging in']);
        }
    } else {
        echo json_encode(['ERROR' => 'Username Or email and password are required']);
    }
} else {
    echo json_encode(["ERROR" => "Method Not Allowed"]);
}

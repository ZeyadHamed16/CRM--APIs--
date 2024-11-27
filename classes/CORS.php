<?php
// CORS POLICY SETTINGS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// RESPONSE CONTENT TYPE TO [ JSON ]
header("Content-type: application/json");

// Handle preflight requests (OPTIONS method) for CORS
// Send a 200 OK status and exit to confirm allowed request headers/methods without processing further
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

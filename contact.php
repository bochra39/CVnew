<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!$input || !isset($input['name'], $input['email'], $input['organization'], $input['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$name = htmlspecialchars($input['name']);
$email = filter_var($input['email'], FILTER_VALIDATE_EMAIL);
$organization = htmlspecialchars($input['organization']);
$message = htmlspecialchars($input['message']);

if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email address']);
    exit;
}

// Email configuration
$to = 'netbocra@gmail.com';
$subject = 'Website User: ' . $name;
$headers = [
    'From: ' . $email,
    'Reply-To: ' . $email,
    'Content-Type: text/html; charset=UTF-8'
];

$email_body = "
<h2>New Research Form Submission</h2>
<p><strong>Name:</strong> {$name}</p>
<p><strong>Email:</strong> {$email}</p>
<p><strong>Organization:</strong> {$organization}</p>
<p><strong>Message:</strong></p>
<p>{$message}</p>
";

if (mail($to, $subject, $email_body, implode("\r\n", $headers))) {
    echo json_encode(['success' => true, 'message' => 'Email sent successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to send email']);
}
?>
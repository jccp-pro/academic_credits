<?php
// save_grade.php
// Receives a JSON POST from prerequisites_test.html and appends
// one line to grades.txt located in the same directory.

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed.']);
    exit;
}

// Parse JSON body
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (empty($data['line'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing grade line.']);
    exit;
}

// Sanitise: keep only printable ASCII + common accented chars, strip newlines
$line = preg_replace('/[\r\n]+/', '', $data['line']);
$line = mb_substr($line, 0, 300);   // hard cap

// Path to grades.txt — same directory as this script
$file = __DIR__ . '/grades.txt';

// Append the line (create file if it does not exist)
$result = file_put_contents($file, $line . PHP_EOL, FILE_APPEND | LOCK_EX);

if ($result === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Could not write to grades.txt. Check server file permissions.']);
    exit;
}

echo json_encode(['ok' => true]);

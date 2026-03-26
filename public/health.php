<?php
// Simple health check - bypasses Laravel entirely
http_response_code(200);
echo json_encode([
    'status' => 'ok',
    'timestamp' => date('c'),
    'php' => phpversion(),
]);

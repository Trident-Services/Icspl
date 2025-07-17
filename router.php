<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

switch ($uri) {
    case '/login':
        require __DIR__ . '/backend/login.php';
        break;
    case '/register':
        require __DIR__ . '/backend/create_admin.php';
        break;
    case '/forgot-password':
        require __DIR__ . '/backend/forgot_password.php';
        break;
    default:
        $file = __DIR__ . $uri;
        if (file_exists($file)) {
            return false; // Let PHP handle static files
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
}

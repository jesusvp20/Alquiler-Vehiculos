<?php
function sendResponse($code, $data = []) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

function sendError($code, $message) {
    sendResponse($code, ['error' => $message]);
}
?>
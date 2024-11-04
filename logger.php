<?php
function logMessage($message, $level = 'INFO') {
    $logFile = 'app.log';
    $timestamp = date('Y-m-d H:i:s');
    $formattedMessage = "[$timestamp] [$level] $message\n";
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}

function logError($message) {
    logMessage($message, 'ERROR');
}
?>

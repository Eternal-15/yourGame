<?php
    // Logs error occured and stores it in a file
    function logError($message) {
        file_put_contents(__DIR__ . "/../user/files/error_log.txt", date("Y-m-d H:i:s") . " - $message\n", FILE_APPEND);
    }
?>

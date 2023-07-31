<?php
// server.php

require_once 'WebSocketServerSingleton.php';

// Define the log file path
$logFilePath = 'server.log';

// Function to log messages to the specified log file
function writeToLog($message) {
    global $logFilePath;
    error_log($message . PHP_EOL, 3, $logFilePath);
}

// Initialize the WebSocket server
$server = new \Ratchet\App('localhost', 8080);
$myWebSocketServer = MyWebSocketServer::getInstance(); // Use the singleton instance
$server->route('/websocket', $myWebSocketServer);

// Write a log message to indicate that the WebSocket server is starting
writeToLog('WebSocket server started.');

// Run the WebSocket server
$server->run();

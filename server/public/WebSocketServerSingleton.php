<?php
// WebSocketServerSingleton.php

require_once '../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class MyWebSocketServer implements MessageComponentInterface {
    private static $instance = null;
    protected $clients;

    private function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public static function getInstance(): MyWebSocketServer {
        if (self::$instance === null) {
            self::$instance = new MyWebSocketServer();
        }
        return self::$instance;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->logMessage("WebSocket connection opened: {$conn->resourceId}");
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $this->logMessage("Received message from client {$from->resourceId}: $msg");
        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        $this->logMessage("WebSocket connection closed: {$conn->resourceId}");
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $this->logMessage("An error has occurred: {$e->getMessage()}");
        $conn->close();
    }

    public function broadcastPaymentStatusUpdate($status) {
        $data = [
            'type' => 'payment_status_update',
            'status' => $status,
        ];

        $encoded_data = json_encode($data);

        foreach ($this->clients as $client) {
            $client->send($encoded_data);
        }
    }

    // Add a new method to broadcast all events to connected clients
    public function broadcastEvent($type, $data) {
        $eventData = [
            'type' => $type,
            'data' => $data,
        ];

        $encoded_data = json_encode($eventData);

        foreach ($this->clients as $client) {
            $client->send($encoded_data);
        }
    }

    private function logMessage($message) {
        $logFilePath = 'websocket_server.log';
        $formattedMessage = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
        error_log($formattedMessage, 3, $logFilePath);
    }
}

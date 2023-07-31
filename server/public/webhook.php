<?php

// webhook.php

require '../vendor/autoload.php';
require_once './shared.php';
require_once 'WebSocketServerSingleton.php'; // Include the singleton class

// This is your Stripe CLI webhook secret for testing your endpoint locally.
$endpoint_secret = $_ENV['STRIPE_WEBHOOK_SECRET'];

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
  $event = \Stripe\Webhook::constructEvent(
    $payload, $sig_header, $endpoint_secret
  );
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  http_response_code(400);
  exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
  // Invalid signature
  http_response_code(400);
  exit();
}

// Use the singleton instance of MyWebSocketServer
$myWebSocketServer = MyWebSocketServer::getInstance();

$eventData = [
    'type' => $event->type,
    'data' => $event->data->object,
];

$myWebSocketServer->broadcastEvent('event', $eventData);

// Handle the event
switch ($event->type) {
    case 'payment_intent.succeeded':
      $paymentIntent = $event->data->object;
      $logMessage = "Hello!!!";
      error_log($logMessage);
  
      // Now, let's broadcast the log message to all connected WebSocket clients
      $myWebSocketServer->broadcastEvent('log_message', $logMessage);
      $myWebSocketServer->broadcastEvent('payment_intent.succeeded', $paymentIntent);
      break;
  
    case 'payment_intent.amount_capturable_updated':
      $paymentIntent = $event->data->object;
      $logMessage = "updated!!";
      error_log($logMessage);
  
      // Now, let's broadcast the log message to all connected WebSocket clients
      $myWebSocketServer->broadcastEvent('log_message', $logMessage);
      $myWebSocketServer->broadcastEvent('payment_intent.amount_capturable_updated', $paymentIntent);
      break;
  
    // ... handle other event types
  
    case 'charge.succeeded':
      $logMessage = "charge succeeded happened!!";
      error_log($logMessage);
  
      // Now, let's broadcast the log message to all connected WebSocket clients
      $myWebSocketServer->broadcastEvent('log_message', $logMessage);
      $myWebSocketServer->broadcastEvent('charge.succeeded', $event->data->object);
      break;
  
    case 'terminal.reader.action_succeeded':
      $logMessage = "customer paid happened!!";
      error_log($logMessage);
  
      // Now, let's broadcast the log message to all connected WebSocket clients
      $myWebSocketServer->broadcastEvent('log_message', $logMessage);
      $myWebSocketServer->broadcastEvent('terminal.reader.action_succeeded', $event->data->object);
      break;
  
    default:
      $logMessage = 'Received unknown event type ' . $event->type;
      error_log($logMessage);
  
      // Now, let's broadcast the log message to all connected WebSocket clients
      $myWebSocketServer->broadcastEvent('log_message', $logMessage);
  }
  

http_response_code(200);

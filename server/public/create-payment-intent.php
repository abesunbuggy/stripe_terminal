<?php

require_once 'shared.php';

try {
  // Create the PaymentIntent
  $paymentIntent = $stripe->paymentIntents->create([
    'amount' => $_POST['amount'],
    'currency' => 'usd',
    'capture_method' => 'manual',
    'payment_method_types' => ['card_present'],
    'metadata' => [
      'name' => $_POST['name'],
      'email' => $_POST['email'],
      'address' => $_POST['address'],
      'reservation_number' => $_POST['resno'],
      'selected_reader' => $_POST['selected_reader']
    ],

  ]);

} catch (\Stripe\Exception\ApiErrorException $e) {
  http_response_code(400);
  error_log($e->getError()->message);
?>
  <h1>Error</h1>
  <p>Failed to create a PaymentIntent</p>
  <p>Please check the server logs for more information</p>
<?php
  exit;
} catch (Exception $e) {
  error_log($e);
  http_response_code(500);
  exit;
}

try {
  // Hand off to the reader
  $reader = $stripe->terminal->readers->processPaymentIntent($_POST['reader'], [
    'payment_intent' => $paymentIntent->id,
  ]);
} catch (\Stripe\Exception\ApiErrorException $e) {
  http_response_code(400);
  error_log($e->getError()->message);
?>
  <h1>Error</h1>
  <p>Failed to hand off to the reader.</p>
  <p>Please check the server logs for more information</p>
<?php
  exit;
} catch (Exception $e) {
  error_log($e);
  http_response_code(500);
  exit;
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Stripe Terminal Sample</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="css/normalize.css" />
    <link rel="stylesheet" href="css/global.css" />
  </head>
  <body>
    <header>
    </header>
    <div class="sr-root">
      <div class="sr-main">
        <section class="container">

          <div class="button-row">



            <form action="/canceled.php" method="POST">
              <input type="hidden" name="reader" value="<?= $reader->id; ?>" />
              <button type="submit">Cancel</button>
            </form>

          </div>
          </form>
        </section>
        <div id="messages" role="alert" style="display: none;"></div>
      </div>
    </div>
  </body>
</html>



<?php
require_once 'shared.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
  // Create the PaymentIntent
  $paymentIntent = $stripe->paymentIntents->create([
    'amount' => $_POST['amount'],
    'currency' => 'usd',
    'capture_method' => 'manual',
    'payment_method_types' => ['card_present'],
    'metadata' => [
      'name' => $_POST['name'],
      'email' => $_POST['email'],
      'address' => $_POST['address'],
      'reservation_number' => $_POST['resno'],
      'selected_reader' => $_POST['selected_reader']
    ],

  ]);


} catch (\Stripe\Exception\ApiErrorException $e) {
  http_response_code(400);
  error_log($e->getError()->message);
  ?>
  <h1>Error</h1>
  <p>Failed to create a PaymentIntent</p>
  <p>Please check the server logs for more information</p>
  <?php
  exit;
} catch (Exception $e) {
  error_log($e);
  http_response_code(500);
  exit;
}

try {
  // Hand off to the reader
  $reader = $stripe->terminal->readers->processPaymentIntent($_POST['reader'], [
    'payment_intent' => $paymentIntent->id,
  ]);
} catch (\Stripe\Exception\ApiErrorException $e) {
  http_response_code(400);
  error_log($e->getError()->message);
  ?>
  <h1>Error</h1>
  <p>Failed to hand off to the reader.</p>
  <p>Please check the server logs for more information</p>
  <?php
  exit;
} catch (Exception $e) {
  error_log($e);
  http_response_code(500);
  exit;
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Stripe Terminal Sample</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="css/normalize.css" />
    <link rel="stylesheet" href="css/global.css" />
    <script src="websocket.js"></script> 
  </head>
  <body>
    <header>
    </header>
    <div class="sr-root">
      <div class="sr-main">
        <section class="container">
       
          <div class="sr-form-row">
            <label>Selected Reader: </label>
            <select name="reader" class="sr-select" disabled>
              <option value="<?= $reader->id; ?>"><?= $reader->label; ?> (<?= $reader->id ?>)</option>
            </select>
          </div>
          <div class="sr-form-row">
            <label for="amount">Amount:</label>
            <input type="text" id="amount" class="sr-input" disabled value="<?= $paymentIntent->amount; ?>" />
            <label for="amount">Customer Name:</label>
            <input type="text" id="name" class="sr-input" disabled value="<?= $paymentIntent->metadata->name; ?>" />
            <label for="amount">Res Number:</label>
            <input type="text" id="name" class="sr-input" disabled value="<?= $paymentIntent->metadata->reservation_number; ?>" />
          </div>
          </form>
        </section>
        <div id="events"></div>      </div>
    </div>

  </body>
</html>
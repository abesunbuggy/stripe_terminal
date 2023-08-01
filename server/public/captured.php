<?php

require_once 'shared.php';

try {
  $paymentIntent = $stripe->paymentIntents->capture($_POST['payment_intent']);
} catch (\Stripe\Exception\ApiErrorException $e) {
  http_response_code(400);
  error_log($e->getError()->message);
?>
  <h1>Error</h1>
  <p>Failed to capture the payment.</p>
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
    <style>
      .countdown {
        font-size: 48px;
        text-align: center;
        margin-top: 50px;
      }
    </style>
    <script>
      // Countdown timer
      var countdown = 10;
      var countdownTimer = setInterval(function() {
        countdown--;
        document.getElementById("countdown").innerText = countdown;
        if (countdown <= 0) {
          clearInterval(countdownTimer);
          window.location.href = './index.php';
        }
      }, 1000);
    </script>
  </head>
  <body>
    <div class="sr-root">
      <div class="sr-main">
        <header class="sr-header">
          <div class="sr-header__logo"></div>
        </header>
        <div class="sr-payment-summary completed-view">
          <h1>Operation Successful</h1>
          <h2>Wait a moment...</h2>
          <div class="countdown" id="countdown">10</div>
        </div>
        <!-- <pre>< json_encode($paymentIntent, JSON_PRETTY_PRINT); ?></pre> -->
      </div>
    </div>
  </body>
</html>
<?php

require_once 'shared.php';

try {
  $readers = $stripe->terminal->readers->all();
} catch (\Stripe\Exception\ApiErrorException $e) {
  http_response_code(400);
  error_log($e->getError()->message);
?>
  <h1>Error</h1>
  <p>Failed to list Readers</p>
  <p>Please check the server logs for more information</p>
<?php
  exit;
} catch (Exception $e) {
  error_log($e);
  http_response_code(500);
  exit;
}
include('display_payments.php');
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

    <div class="sr-root">
        <div class="sr-main">
            <section class="container">
                <h1>Sunbuggy Deposit App</h1>
                <form action="/create-payment-intent.php" method="POST">
                    <div class="sr-form-row">
                        <label>Select Reader: </label>
                        <select name="reader" class="sr-select">
                            <?php foreach($readers as $reader) { ?>
                                <option value="<?= $reader->id; ?>"><?= $reader->label; ?> (<?= $reader->id ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="sr-form-row">
                        <label>Name: </label>
                        <input type="text" name="name" class="sr-input">
                    </div>
                    <div class="sr-form-row">
                        <label>Email: </label>
                        <input type="email" name="email" class="sr-input">
                    </div>
                    <div class="sr-form-row">
                        <label>Address: </label>
                        <input type="text" name="address" class="sr-input">
                    </div>
                    <div class="sr-form-row">
                        <label>Reservation Number: </label>
                        <input type="text" name="resno" class="sr-input">
                    </div>
                    <div class="sr-form-row">
                        <label for="amount">Amount To Authorize:</label>
                        <input type="text" name="amount" class="sr-input">
                    </div>
                    <input type="hidden" name="selected_reader" value="" id="selected-reader-input">
                    <div class="button-row">
                        <button id="confirm">Confirm</button>
                    </div>
                </form>
            </section>
            <div id="messages" role="alert" style="display: none;"></div>
        </div>
    </div>
    <div>
    </div>

</body>
</html>
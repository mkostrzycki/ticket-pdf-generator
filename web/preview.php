<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    var_dump($_POST);

    if (isset($_POST['origin']) && !empty($_POST['origin'])
        && isset($_POST['destination']) && !empty($_POST['destination'])
    ) {

        // airports are set and not empty
        $originAirportCode = $_POST['origin'];
        $destinationAirportCode = $_POST['destination'];

        if ($originAirportCode !== $destinationAirportCode) {

            if (isset($_POST['departure']) && !empty($_POST['departure'])
                && isset($_POST['flightTime']) && is_numeric($_POST['flightTime'])
                && isset($_POST['ticketPrice']) && is_numeric($_POST['ticketPrice'])
            ) {

                // departure date, flight time and ticket price are set and valid
                $departureDateAndTime = $_POST['departure'];
                $flightTime = $_POST['flightTime'];
                $ticketPrice = $_POST['ticketPrice'];

                if ($ticketPrice > 0) {

                    $ticketHtml = '';

                    $ticketHtml .= $departureDateAndTime . '<br>';
                    $ticketHtml .= $flightTime . '<br>';
                    $ticketHtml .= $ticketPrice;

                } else { // $ticketPrice < 0
                    $errorMessage = 'Ticket price is less than zero.';
                }

            } else { // $_POST['departure'] and/or $_POST['flightTime'] and/or $_POST['ticketPrice'] are not set or invalid
                $errorMessage = 'Departure date, flight time and ticket price are not set or incorrect.';
            }

        } else { // $originAirportCode === $destinationAirportCode
            $errorMessage = 'You have chosen the same origin and destination airports.';
        }

    } else { // $_POST['origin'] or/and $_POST['destination'] are not set or empty
        $errorMessage = 'No information about airports.';
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ticket Generator - Preview</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <p>Preview:</p>
    <div class="row">
        <!-- message / errorMessage -->
        <?php
        if (isset($errorMessage)) {
            echo '<p>';
            echo $errorMessage;
            echo '</p>';
        }
        ?>
    </div>
    <div class="row">
        <?php
        echo $ticketHtml;
        ?>
    </div>
    <div class="row">
        <p><a href="index.php"><<< Back to form</a></p>
    </div>
</div>
</body>
</html>
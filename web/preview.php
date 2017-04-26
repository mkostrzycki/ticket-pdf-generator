<?php

include_once(__DIR__ . '/../includes/airports.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

                    $originAirportName = null;
                    $destinationAirportName = null;

                    $originAirportTimeZone = null;
                    $destinationAirportTimeZone = null;

                    foreach ($airports as $airport) {

                        if ($originAirportCode == $airport['code']) {
                            $originAirportName = $airport['name'];
                            $originAirportTimeZone = new DateTimeZone($airport['timezone']);

                        } elseif ($destinationAirportCode == $airport['code']) {
                            $destinationAirportName = $airport['name'];
                            $destinationAirportTimeZone = new DateTimeZone($airport['timezone']);
                        }
                    }

                    $originAirportLocalTime = new DateTime($departureDateAndTime, $originAirportTimeZone);

                    // calculate time of arrival in destination time zone
                    $destinationAirportLocalTime = new DateTime($departureDateAndTime, $originAirportTimeZone);
                    $destinationAirportLocalTime->setTimezone($destinationAirportTimeZone); // change timezone to destination
                    $destinationAirportLocalTime->modify($flightTime . ' hours'); // add flight time

                    // get date and time as a string
                    $originAirportLocalTime = $originAirportLocalTime->format('d-m-Y H:i:s');
                    $destinationAirportLocalTime = $destinationAirportLocalTime->format('d-m-Y H:i:s');

                    $ticketHtml = '';

                    $ticketHtml .= 'Origin Airport: ' . $originAirportName . ' [' . $originAirportCode . ']<br>';
                    $ticketHtml .= 'Departure time: ' . $originAirportLocalTime . ' (' . timezone_name_get($originAirportTimeZone) . ' local time)<br>';
                    $ticketHtml .= 'Destination Airport: ' . $destinationAirportName . ' [' . $destinationAirportCode . ']<br>';
                    $ticketHtml .= 'Arrival time: ' . $destinationAirportLocalTime . ' (' . timezone_name_get($destinationAirportTimeZone) . ' local time)<br>';
                    $ticketHtml .= 'Flight time: ' . $flightTime . '<br>';
                    $ticketHtml .= 'Ticket price: ' . $ticketPrice;

                } else { // $ticketPrice < 0
                    $errorMessage = 'Ticket price is less than zero.';
                }

            } else { // $_POST['departure'] and/or $_POST['flightTime'] and/or $_POST['ticketPrice'] are not set or are invalid
                $errorMessage = 'Departure date, flight time and ticket price are not set or incorrect.';
            }

        } else { // $originAirportCode === $destinationAirportCode
            $errorMessage = 'You have chosen the same origin and destination airports.';
        }

    } else { // $_POST['origin'] or/and $_POST['destination'] are not set or are empty
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
        <h3>Preview:</h3>
        <?php
        if (isset($ticketHtml)) {
            echo $ticketHtml;
        }
        ?>
    </div>
    <div class="row">
        <p><a href="index.php"><<< Back to form</a></p>
    </div>
</div>
</body>
</html>
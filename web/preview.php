<?php

use Faker\Factory;
use NumberToWords\NumberToWords;

require_once(__DIR__ . '/../vendor/autoload.php');
include_once(__DIR__ . '/../includes/airports.php');

session_start();

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

                    /**
                     * Get data from airports table
                     */

                    $originAirportName = null;
                    $destinationAirportName = null;

                    $originAirportTimeZone = null;
                    $destinationAirportTimeZone = null;

                    if (isset($airports)) {

                        foreach ($airports as $airport) {

                            if ($originAirportCode == $airport['code']) {
                                $originAirportName = $airport['name'];
                                $originAirportTimeZone = new DateTimeZone($airport['timezone']);

                            } elseif ($destinationAirportCode == $airport['code']) {
                                $destinationAirportName = $airport['name'];
                                $destinationAirportTimeZone = new DateTimeZone($airport['timezone']);
                            }
                        }
                    }

                    /**
                     * Calculate dates and times
                     */

                    $originAirportLocalTime = new DateTime($departureDateAndTime, $originAirportTimeZone);

                    // calculate time of arrival in destination time zone
                    $destinationAirportLocalTime = new DateTime($departureDateAndTime, $originAirportTimeZone);
                    $destinationAirportLocalTime->setTimezone($destinationAirportTimeZone); // change timezone to destination
                    $destinationAirportLocalTime->modify($flightTime . ' hours'); // add flight time

                    // get date and time as a string
                    $originAirportLocalTime = $originAirportLocalTime->format('d-m-Y H:i:s');
                    $destinationAirportLocalTime = $destinationAirportLocalTime->format('d-m-Y H:i:s');

                    /**
                     * Generate name using fzaninotto/faker
                     * (https://github.com/fzaninotto/Faker)
                     */

                    $faker = Factory::create();
                    $passengerName = $faker->name;

                    /**
                     * Convert price to words using kwn/number-to-words
                     * (https://github.com/kwn/number-to-words)
                     */

                    $numberToWords = new NumberToWords();
                    $currencyTransformer = $numberToWords->getCurrencyTransformer('en');

                    $ticketPriceInWords = $currencyTransformer->toWords($ticketPrice * 100, 'PLN'); // amount in grosze

                    /**
                     * Generate html
                     */

                    $ticketHtml = '';

                    $ticketHtml .= '<div class="ticket">';

                    $ticketHtml .= '<div class="logotype">'
                        . '<p class="plane">&nbsp;&nbsp;&nbsp;&nbsp;__|__          NeverFalling</p><p class="plane">---o-(_)-o--- Airlines</p></div>';

                    $ticketHtml .= '<div class="header width-100">Time Of Departure</div>';
                    $ticketHtml .= '<div class="clear"></div>';
                    $ticketHtml .= '<div class="text width-100">'
                        . $originAirportLocalTime
                        . '<span class="small"> ('
                        . timezone_name_get($originAirportTimeZone)
                        . ' local time)</span></div>';
                    $ticketHtml .= '<div class="clear"></div>';

                    $ticketHtml .= '<div class="header inner width-50">From | Origin</div><div class="header width-50">To | Destination</div>';
                    $ticketHtml .= '<div class="clear"></div>';
                    $ticketHtml .= '<div class="text inner width-50">'
                        . $originAirportName
                        . '<span class="small"> ['
                        . $originAirportCode
                        . ']</span></div>';

                    $ticketHtml .= '<div class="text width-50">'
                        . $destinationAirportName
                        . '<span class="small"> ['
                        . $destinationAirportCode
                        . ']</span></div>';
                    $ticketHtml .= '<div class="clear"></div>';

                    $ticketHtml .= '<div class="header width-100">Time Of Arrival</div>';
                    $ticketHtml .= '<div class="clear"></div>';
                    $ticketHtml .= '<div class="text width-100">'
                        . $destinationAirportLocalTime
                        . '<span class="small"> ('
                        . timezone_name_get($destinationAirportTimeZone)
                        . ' local time)</span></div>';
                    $ticketHtml .= '<div class="clear"></div>';

                    $ticketHtml .= '<div class="header inner width-50">Ticket price</div><div class="header width-50">Flight time</div>';
                    $ticketHtml .= '<div class="clear"></div>';
                    $ticketHtml .= '<div class="text inner width-50">'
                        . number_format((float)$ticketPrice, 2, ',', ' ')
                        . ' zł</div>';

                    $ticketHtml .= '<div class="text width-50">'
                        . $flightTime
                        . ' hour(s)</div>';
                    $ticketHtml .= '<div class="clear"></div>';

                    $ticketHtml .= '<div class="header width-100">Price in words</div>';
                    $ticketHtml .= '<div class="clear"></div>';
                    $ticketHtml .= '<div class="text width-100">'
                        . $ticketPriceInWords
                        . '</div>';
                    $ticketHtml .= '<div class="clear"></div>';

                    $ticketHtml .= '<div class="header width-100">Passenger name</div>';
                    $ticketHtml .= '<div class="clear"></div>';
                    $ticketHtml .= '<div class="text width-100">'
                        . $passengerName
                        . '</div>';
                    $ticketHtml .= '<div class="clear"></div>';

                    $ticketHtml .= '</div>';

                    $_SESSION['ticketHtml'] = $ticketHtml;

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
    <link rel="stylesheet" href="css/ticketStyle.css">
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
        <div class="btn-group">
            <a href="index.php" class="btn btn-info" role="button"><<< Back to form</a>
            <a href="pdf.php" class="btn btn-primary" role="button">Download PDF</a>
        </div>
    </div>
</div>
</body>
</html>
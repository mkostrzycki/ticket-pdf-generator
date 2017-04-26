<?php

session_start();

require_once(__DIR__ . '/../vendor/autoload.php');

/**
 * Generate PDF using mpdf/mpdf
 * (https://github.com/mpdf/mpdf)
 */

if (isset($_SESSION['ticketHtml'])) {

    $ticketHtml = $_SESSION['ticketHtml'];

    $mPdf = new mPDF('utf-8', 'A4-L');

    $stylesheet = file_get_contents('css/ticketStyle.css');

    $mPdf->WriteHTML($stylesheet, 1);
    $mPdf->WriteHTML($ticketHtml, 2);

    $mPdf->Output('flight_ticket.pdf', D);

} else {

}

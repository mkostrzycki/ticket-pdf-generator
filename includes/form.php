<?php
/**
 * @param string $name
 * @param string $labelText
 * @param array $optionsArray
 * @return string
 */
function getAirportSelectElement($name, $labelText, $optionsArray)
{
    $dropDownListCode = '';

    $dropDownListCode .= '<label for="' . $name . '">' . $labelText . '</label><br>'; // @ToDo Zamiast <br> dodać style w CSS
    $dropDownListCode .= '<select name="' . $name . '">';

    // options
    foreach ($optionsArray as $option) {
        $dropDownListCode .= '<option value="' . $option['code'] . '">' .
            $option['name'] . ' / ' . $option['code'] .
            '</option>';
    }

    $dropDownListCode .= '</select><br>'; // @ToDo Zamiast <br> dodać style w CSS

    return $dropDownListCode;
}

?>

<form action="../web/preview.php" method="post">
    <legend>Generate a ticket</legend>
    <div class="form-group">
        <?php
        echo getAirportSelectElement('origin', 'Origin Airport:', $airports);
        echo '<br>'; // @ToDo Zamiast <br> dodać style w CSS
        echo getAirportSelectElement('destination', 'Destination Airport:', $airports);
        ?>
    </div>
    <div class="form-group">
        <label>Departure:
            <input type="datetime-local" class="form-control" name="departure" placeholder="dd-mm-rrr h:m:s">
        </label>
        <label>Flight time:
            <input type="number" min="0" step="1" class="form-control" name="flightTime">
        </label>
        <label>Ticket price:
            <input type="number" min="0" step="0.01" class="form-control" name="ticketPrice">
        </label>
    </div>
    <button type="submit" class="btn btn-primary">Generate</button>
</form>

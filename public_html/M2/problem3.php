<?php
$a1 = [-1, -2, -3, -4, -5, -6, -7, -8, -9, -10];
$a2 = [-1, 1, -2, 2, 3, -3, -4, 5];
$a3 = [-0.01, -0.0001, -.15];
$a4 = ["-1", "2", "-3", "4", "-5", "5", "-6", "6", "-7", "7"];

function bePositive($arr) {
    echo "<br>Processing Array:<br><pre>" . var_export($arr, true) . "</pre>";
    echo "<br>Positive output:<br>";
    $output = [];
    //start edits
    //note: use the $arr variable, don't directly touch $a1-$a4
    //TODO Take each value of the $arr, convert it to positive, and set it to the same index in the $output array but with the original data type (i.e., if the source was a string the output slot value should be a string)

    foreach ($arr as $value) {
        $positiveValue = abs($value);

        if (is_string($value)) {
            $positiveValue = (string)$positiveValue;
        }

        $output[] = $positiveValue;
    }

    //end edits
    
    //displays the output along with their types
    $mappedOutput = array_map(function($o) {
        $type = strtoupper(substr(gettype($o), 0, 1));
        return "$o ($type)";
    }, $output);
    echo implode(', <br>', $mappedOutput);
}

echo "Problem 3: Be Positive<br>";
?>
<table>
    <thead>
        <th>A1</th>
        <th>A2</th>
        <th>A3</th>
        <th>A4</th>
    </thead>
    <tbody>
        <tr>
            <td>
                <?php bePositive($a1); ?>
            </td>
            <td>
                <?php bePositive($a2); ?>
            </td>
            <td>
                <?php bePositive($a3); ?>
            </td>
            <td>
                <?php bePositive($a4); ?>
            </td>
        </tr>
    </tbody>
</table>
<style>
    table {
        border-spacing: 2em 3em;
        border-collapse: separate;
    }

    td {
        border-right: solid 1px black;
        border-left: solid 1px black;
    }
</style>

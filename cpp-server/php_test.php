<!DOCTYPE html>
<html>
<head>
    <title>Matrix Viewer</title>
    <style>
        table { border-collapse: collapse; margin-top: 20px; }
        td { border: 1px solid black; padding: 5px; text-align: center; width: 30px; }
    </style>
</head>
<body>
<h1>Matrix from Text File</h1>

<?php
// Read matrix.txt
$lines = file("matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Get dimensions
list($rows, $cols) = explode(" ", array_shift($lines));

// Build matrix
$matrix = [];
for ($i = 0; $i < $rows; $i++) {
    $row = preg_split('/\s+/', $lines[$i]);
    $matrix[] = array_map(function($val){ return $val === "" ? 0 : intval($val); }, $row);
}

// Display matrix as HTML table
echo "<table>";
foreach ($matrix as $row) {
    echo "<tr>";
    foreach ($row as $cell) {
        echo "<td>" . ($cell === 0 ? "" : $cell) . "</td>";
    }
    echo "</tr>";
}
echo "</table>";


echo "<script>const matrix = " . json_encode($matrix) . ";</script>";
?>

</body>
</html>

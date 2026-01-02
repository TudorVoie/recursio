<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Matrix Viewer</title>
    <link rel="stylesheet" href="css_design.css">
</head>
<body>
<div id="container" class="container67">
</div>
<?php
$lines = file("matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
list($rows, $cols) = explode(" ", array_shift($lines));

$matrix = [];
for ($i = 0; $i < $rows; $i++) {
    $row = preg_split('/\s+/', $lines[$i]);
    $matrix[] = array_map('intval', $row);
}
?>

<script>
    window.APP = {
        matrix: <?= json_encode($matrix) ?>,
        rows: <?= $rows ?>,
        cols: <?= $cols ?>
    };
</script>

<script src="js_generation.js"></script>

</body>
</html>

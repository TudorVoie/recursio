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
<svg id="svg-lines"></svg>
<?php
$lines = file("matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
list($rows, $cols) = explode(" ", array_shift($lines));

$matrix = [];
for ($i = 0; $i < $rows; $i++) {
    $row = preg_split('/\s+/', $lines[$i]);
    $matrix[] = array_map('intval', $row);
}

$lines2 = file("rute_matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
list($rows2, $cols2) = explode(" ", array_shift($lines2));

$matrix2 = [];
for ($i = 0; $i < $rows2; $i++) {
    $row2 = preg_split('/\s+/', $lines2[$i]);
    $matrix2[] = array_map('intval', $row2);
}

$lines3 = file("drawing.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$progresare_cnt = intval(array_shift($lines3));

$values = preg_split('/\s+/', implode(" ", $lines3));

$progresare = array_map('intval', $values);


?>

<script>
    window.APP = {
        matrix: <?= json_encode($matrix) ?>,
        rows: <?= $rows ?>,
        cols: <?= $cols ?>
    };

    window.APP2 = {
        matrix2: <?= json_encode($matrix2) ?>,
        rows2: <?= $rows2 ?>,
        cols2: <?= $cols2 ?>
    };
    window.APP3 = {
        count: <?= $progresare_cnt ?>,
        data: <?= json_encode($progresare) ?>
    };
</script>


<script src="js_generation.js"></script>
<script src="Animation.js"></script>

</body>
</html>

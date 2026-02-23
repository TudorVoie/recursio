<!DOCTYPE html>
<html lang="en">

<?php
session_start();

$output = '';
$dir = __DIR__ . '/sessions/' . session_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $call = $_POST['call'] ?? '';

    $_SESSION['code'] = $code;
    $_SESSION['call'] = $call;

    // stimulează animația să ruleze după submit
    $_SESSION['just_submitted'] = true;

    if ($code !== '' && $call !== '') {
        if (!is_dir($dir)) mkdir($dir, 0700, true);

        file_put_contents("$dir/user.cpp", $code);

        $cmd = __DIR__ . "/int.sh " . escapeshellarg($call) . " " . escapeshellarg($dir);
        $output = shell_exec($cmd . ' 2>&1');
        file_put_contents("$dir/temp_test_text.txt", $output);

        if (file_exists("$dir/temp_test_text.txt")) {
            $lines = file("$dir/temp_test_text.txt");
            file_put_contents("$dir/text_test.txt", implode("", array_slice($lines, 2)));
        }

        $cmd = __DIR__ . "/a.out " . escapeshellarg($dir);
        shell_exec($cmd . ' 2>&1');

        $cmd = __DIR__ . "/int2.sh " . escapeshellarg($dir);
        $output = shell_exec($cmd . ' 2>&1');

        header("Location: php_test.php?sid=" . session_id());
        exit;
    }
}

// citim matricea și progresarea
$matrix = $matrix2 = $progresare = [];
$rows = $cols = $rows2 = $cols2 = $progresare_cnt = 0;

if (file_exists("$dir/matrix.txt")) {
    $lines = file("$dir/matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    list($rows, $cols) = explode(" ", array_shift($lines));
    for ($i = 0; $i < $rows; $i++) {
        $row = preg_split('/\s+/', $lines[$i]);
        $matrix[] = array_map('intval', $row);
    }
}

if (file_exists("$dir/rute_matrix.txt")) {
    $lines2 = file("$dir/rute_matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    list($rows2, $cols2) = explode(" ", array_shift($lines2));
    for ($i = 0; $i < $rows2; $i++) {
        $row2 = preg_split('/\s+/', $lines2[$i]);
        $matrix2[] = array_map('intval', $row2);
    }
}

if (file_exists("$dir/drawing.txt")) {
    $lines3 = file("$dir/drawing.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $progresare_cnt = intval(array_shift($lines3));
    $values = preg_split('/\s+/', implode(" ", $lines3));
    $progresare = array_map('intval', $values);
}

// ------------------------------
// flat array of numbers from apelari.txt
$vars = [];
$input_file = "$dir/apelari.txt";
if (file_exists($input_file)) {
    $lines = file($input_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (preg_match_all('/-?\d+/', $line, $matches)) {
            $numbers = array_map('intval', $matches[0]);
            $vars = array_merge($vars, $numbers);
        }
    }
}

// ------------------------------
// reversed numbers from returnari.txt
$numbers = [];
$input_file = "$dir/returnari.txt";
if (file_exists($input_file)) {
    $lines = file($input_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_reverse($lines);
    foreach ($lines as $line) {
        if (preg_match_all('/-?\d+/', $line, $matches)) {
            $numbers[] = array_map('intval', $matches[0]);
        }
    }
}

$run_animation = !empty($_SESSION['just_submitted']);
if ($run_animation) unset($_SESSION['just_submitted']);
?>

<head>
    <meta charset="UTF-8">
    <title>Matrix Viewer</title>
    <link rel="stylesheet" href="css_design.css">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>

<body>
<form method="post" id="myForm">
    <div id="dragBox">
        <div id="dragBoxHeader">↔</div>
        <div class="text_div">
            <textarea name="code" class="code_input_field" placeholder="Introdu codul"><?= htmlspecialchars($_SESSION['code'] ?? '') ?></textarea>
            <textarea name="call" class="value_input_field" rows="1" placeholder="Introdu valorile"><?= htmlspecialchars($_SESSION['call'] ?? '') ?></textarea>
        </div>
        <button type="submit" id="executeButton">Execută</button>
    </div>
</form>

<div id="container" class="container67"></div>
<svg id="svg-lines"></svg>

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
window.APP_VARS = <?= json_encode($vars) ?>;
window.REVERSED_NUMBERS = <?= json_encode($numbers) ?>;

// run animation after submit
<?php if ($run_animation): ?>
function runAnimationIfReady() {
    if (typeof Animarea_Liniilor === 'function') {
        Animarea_Liniilor();
    } else {
        setTimeout(runAnimationIfReady, 100);
    }
}
window.addEventListener('DOMContentLoaded', runAnimationIfReady);
<?php endif; ?>
</script>

<script src="js_generation.js"></script>
<script src="Animation.js"></script>
<script src="Code_field.js"></script>

<?php if ($output !== ''): ?>
<pre><?= htmlspecialchars($output) ?></pre>
<?php endif; ?>
<?php exit ?>
</body>
</html>

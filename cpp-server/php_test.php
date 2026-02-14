<!DOCTYPE html>
<html lang="en">

<?php
session_start();

$output = '';
$dir = __DIR__ . '/sessions/' . session_id();

/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $call = $_POST['call'] ?? '';

    if ($code !== '' && $call !== '') {
        $dir = __DIR__ . '/sessions/' . session_id();
        if (!is_dir($dir)) mkdir($dir, 0700, true);

        file_put_contents("$dir/user.cpp", $code);

        /*
        //$cmd = "$dir/int.sh " . escapeshellarg($call);
        $cmd = __DIR__ . "/int.sh " . escapeshellarg($call) . " " . escapeshellarg($dir) . " 2>&1";

        //$output = shell_exec($cmd . ' 2>&1');
        $output = shell_exec($cmd);
        file_put_contents("$dir/temp_test_text.txt", $output);
        $cmd = "tail -n +3 " . escapeshellarg("$dir/temp_test_text.txt") . " > " . escapeshellarg("$dir/test_text.txt");
        shell_exec($cmd);

        
        $cmd = __DIR__ . "/a.out " . escapeshellarg($dir) . "/";
        $output1 = shell_exec($cmd);
        print $output1;
        

        // 1. Rulează int.sh și capturează tot outputul
        $cmd = __DIR__ . "/int.sh " . escapeshellarg($call) . " " . escapeshellarg($dir);
        $output = shell_exec($cmd . ' 2>&1');
        file_put_contents("$dir/temp_test_text.txt", $output);

        // 2. Taie primele 2 linii direct în PHP
        $lines = file("$dir/temp_test_text.txt");
        file_put_contents("$dir/text_test.txt", implode("", array_slice($lines, 2)));

        // 3. Rulează a.out
        $cmd = __DIR__ . "/a.out " . escapeshellarg($dir);
        $output1 = shell_exec($cmd . ' 2>&1');

        
        // 4. Afișează outputul
        echo "<pre>" . htmlspecialchars($output1) . "</pre>";

        header("Location: php_test.php?sid=" . session_id());
        exit;
        
    }
}
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $call = $_POST['call'] ?? '';
    $_SESSION['code'] = $code;
    $_SESSION['call'] = $call;


    if ($code !== '' && $call !== '') {

        if (!is_dir($dir)) mkdir($dir, 0700, true);

        file_put_contents("$dir/user.cpp", $code);

        // 1. run int.sh
        $cmd = __DIR__ . "/int.sh " . escapeshellarg($call) . " " . escapeshellarg($dir);
        $output = shell_exec($cmd . ' 2>&1');
        file_put_contents("$dir/temp_test_text.txt", $output);

        // 2. cut first 2 lines safely
        if (file_exists("$dir/temp_test_text.txt")) {
            $lines = file("$dir/temp_test_text.txt");
            file_put_contents("$dir/text_test.txt", implode("", array_slice($lines, 2)));
        }

        // 3. run a.out
        $cmd = __DIR__ . "/a.out " . escapeshellarg($dir);
        shell_exec($cmd . ' 2>&1');

        // IMPORTANT: redirect BEFORE ANY OUTPUT
        header("Location: php_test.php?sid=" . session_id());
        exit;
    }
}

?>

<head>
    <meta charset="UTF-8">
    <title>Matrix Viewer</title>
    <link rel="stylesheet" href="css_design.css">
</head>

<body>
    <form method="post">
    <div id="dragBox">
        <div id="dragBoxHeader">↔</div>
        <div class="text_div">
            <textarea name="code" class="code_input_field" placeholder="Introdu codul"><?= htmlspecialchars($_SESSION['code'] ?? '') ?></textarea>
            <textarea name="call" class="value_input_field" rows="1" placeholder="Introdu valorile"><?= htmlspecialchars($_SESSION['call'] ?? '') ?></textarea>
        </div>
        <button type="submit" id="executeButton">Execută</button>
    </div>
    </form> 
    <div id="container" class="container67">
    </div>
    <svg id="svg-lines"></svg>
    <?php

    $dir = __DIR__ . '/sessions/' . session_id();

    if (file_exists("$dir/matrix.txt")) {
        $lines = file("$dir/matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        list($rows, $cols) = explode(" ", array_shift($lines));

        $matrix = [];
        for ($i = 0; $i < $rows; $i++) {
            $row = preg_split('/\s+/', $lines[$i]);
            $matrix[] = array_map('intval', $row);
        }
    }

    if (file_exists("$dir/rute_matrix.txt")) {
        $lines2 = file("$dir/rute_matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        list($rows2, $cols2) = explode(" ", array_shift($lines2));

        $matrix2 = [];
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
    <script src="Code_field.js"></script>

<?php if ($output !== ''): ?>
<pre><?= htmlspecialchars($output) ?></pre>
<?php endif; ?>

</body>

</html>
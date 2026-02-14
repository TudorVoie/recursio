<!DOCTYPE html>
<?php
session_start();

$output = '';

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
        */

        // 1. Rulează int.sh și capturează tot outputul
        $cmd = __DIR__ . "/../int.sh " . escapeshellarg($call) . " " . escapeshellarg($dir);
        $output = shell_exec($cmd . ' 2>&1');
        file_put_contents("$dir/temp_test_text.txt", $output);

        // 2. Taie primele 2 linii direct în PHP
        $lines = file("$dir/temp_test_text.txt");
        file_put_contents("$dir/text_test.txt", implode("", array_slice($lines, 2)));

        // 3. Rulează a.out
        $cmd = __DIR__ . "/a.out " . escapeshellarg($dir);
        $output1 = shell_exec($cmd . ' 2>&1');

        /*
        // 4. Afișează outputul
        echo "<pre>" . htmlspecialchars($output1) . "</pre>";

        header("Location: php_test.php?sid=" . session_id());
        exit;
        */
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recursivitate</title>
</head>
<body>
<center>
<form method="post">
    <i>Introdu codul tau aici:</i><br>
    <textarea name="code" cols="100" rows="40"><?= htmlspecialchars($_POST['code'] ?? '') ?></textarea>
    <br><br>

    <i>Introdu numele functiei si parametrii, ex: <b>f(123)</b></i><br>
    <textarea name="call" cols="100" rows="1"><?= htmlspecialchars($_POST['call'] ?? '') ?></textarea>
    <br><br>

    <button type="submit">Da-i!</button>
</form>

<?php if ($output !== ''): ?>
<pre><?= htmlspecialchars($output) ?></pre>
<?php endif; ?>
</center>
</body>
</html>

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

        $cmd = './int.sh ' . escapeshellarg($call);
        $output = shell_exec($cmd . ' 2>&1');
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

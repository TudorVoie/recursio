<?php

session_start();

if(!isset($_REQUEST['tip']) || !isset($_REQUEST['id'])) {
    die("Lipsesc datele necesare.");
}

try {
    $pdo = new PDO("mysql:host=192.168.0.3;dbname=recursivitate;charset=utf8", "tudor", "tudor");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$stmt = $pdo->prepare("SELECT * FROM " . $_REQUEST['tip'] . " WHERE id = :id");

$stmt->execute(['id' => $_REQUEST['id']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) {
    die("Grila/problema nu a fost gasita.");
}
//var_dump($data);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dir = __DIR__ . '/sessions1/' . session_id();

    $code = $_POST['rezolvare'] ?? '';
    $_SESSION['rezolvare'] = $code;

    if($code !== '') {
        if (!is_dir($dir)) mkdir($dir, 0700, true);
        file_put_contents("$dir/user.cpp", $code);
        $cmd = __DIR__ . "/verificareteste.sh " . escapeshellarg($dir) . " date_intrare_iesire/" . escapeshellarg($_REQUEST['id']) . ".json";
        $output = shell_exec($cmd . ' 2>&1');
        echo $output;
    }
}

?>

<html>
    <head>
        <title>Rezolvare</title>
    </head>
    <body>
        <h1 align="center">Rezolvare pentru <?php echo htmlspecialchars($_REQUEST['tip']) . " cu ID " . htmlspecialchars($_REQUEST['id']); ?></h1>
        <p align="center">
            <?php echo $data["enunt"] ?>
        </p>
        <?php
        if($_REQUEST['tip'] == "grila") {
            ?>

            <form method="post">
                <input type="radio" value="1" id="v1" name="varianta">
                <label for="v1"><?php echo $data["v1"] ?></label><br>
                <input type="radio" value="2" id="v2" name="varianta">
                <label for="v2"><?php echo $data["v2"] ?></label><br>
                <input type="radio" value="3" id="v3" name="varianta">
                <label for="v3"><?php echo $data["v3"] ?></label><br>
                <input type="radio" value="4" id="v4" name="varianta">
                <label for="v4"><?php echo $data["v4"] ?></label><br>

                <button type="submit" name="verifica">Verifica raspunsul</button>

                <?php
                if(isset($_POST['verifica'])) {
                    if($_POST['varianta'] == $data['corecta']) {
                        echo "Raspuns corect!";
                    } else {
                        echo "Raspuns gresit!";
                    }
                }
                ?>
            </form>

            <?php
        } else {
            ?>
                <p align="center">
                    <?php echo "Date de intrare: " . $data["date_intrare"] ?>
                </p>
                <p align="center">
                    <?php echo "Date de iesire: " . $data["date_iesire"] ?>
                </p>
                <form method="post" align="center">
                    <textarea name="rezolvare" rows="10" cols="50" placeholder="Scrie rezolvarea ta aici..."><?= htmlspecialchars($_SESSION['rezolvare'] ?? '') ?></textarea><br>
                    <button type="submit" name="verifica">Verifica rezolvarea</button>
                </form>
            <?php
        }
        ?>
        
    </body>
</html>
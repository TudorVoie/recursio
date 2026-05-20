<?php ob_start(); ?><!DOCTYPE html>
<html lang="en">
<?php
// === FIX 2 (AI): suprima notice/warnings ca sa nu corupa JSON-ul === 
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');

if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobile') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'android')) {
   header('Location: mobileerror.php');
   exit;
}


session_start();
$output = '';
$sessionDir = __DIR__ . '/sessions/' . session_id();

// === FIX (file manipulation): create session folder instantly + auto-create empty files ===
if (!is_dir($sessionDir)) {
    mkdir($sessionDir, 0777, true);
}

$files = [
    'a.out',
    'apelari.txt',
    'call.txt',
    'compilare.txt',
    'drawing.txt',
    'gdb.txt',
    'iesire.txt',
    'input.cpp',
    'matrix.txt',
    'returnari.txt',
    'rute_matrix.txt',
    'stiva.gdb',
    'text_test.txt',
    'user.cpp',
    'supVars.cpp'
];

foreach ($files as $file) {
    $path = $sessionDir . '/' . $file;
    if (!file_exists($path)) {
        file_put_contents($path, '');
    }
}

$dir = $sessionDir;
$sharesRoot = __DIR__ . '/shares';
$isSharedView = false;
$config = parse_ini_file(__DIR__ . '/../.env');
$chatgptkey = $config['CHATGPT'];

// === FIX 3 (AI): detectie AJAX mai robusta (X-Requested-With SAU Accept: application/json) === 
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
    || (isset($_POST['chatgpt_request']))
    || (isset($_POST['share_request']));

function send_json($payload)
{
    while (ob_get_level() > 0)
        ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function intrebare_chat($messages, $chatgptkey)
{
    $endpoint = "https://api.openai.com/v1/chat/completions";

    $body = [
        "model" => "gpt-4o-mini",
        "messages" => $messages,
        "temperature" => 0.5
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $chatgptkey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

    $response = curl_exec($ch);

    if ($response === false) {
        return ['data' => null, 'error' => curl_error($ch)];
    }

    curl_close($ch);

    $decoded = json_decode($response, true);

    return [
        'data' => $decoded['choices'][0]['message']['content'] ?? '',
        'error' => $decoded['error']['message'] ?? null
    ];
}

function intrebare($prompt, $chatgptkey, $lang)
{
    // OpenAI endpoint 
    $endpoint = "https://api.openai.com/v1/chat/completions";

    // Headers 
    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer " . $chatgptkey
    ];
    // Body 
    $body = [
        "model" => "gpt-4o-mini",
        "messages" => [
            [
                "role" => "system",
                "content" => "You explain code clearly and concisely. Use {$lang} language. Do not use markdown."
            ],
            [
                "role" => "user",
                "content" => $prompt
            ]
        ],
        "temperature" => 0.4,
        "max_tokens" => 1024
    ];
    // Init CURL 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

    // Execute request 
    $response = curl_exec($ch);
    // CURL error 
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'data' => null,
            'error' => 'cURL Error: ' . $error
        ];
    }

    // HTTP status 
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    // Decode JSON 
    $decoded = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'data' => null,
            'error' => 'Invalid JSON response from API'
        ];
    }

    // API error 
    if ($httpCode !== 200 || isset($decoded['error'])) {
        return [
            'data' => null,
            'error' => $decoded['error']['message'] ?? ('HTTP Error ' . $httpCode)
        ];
    }

    // Success 
    return [
        'data' => $decoded['choices'][0]['message']['content'] ?? 'No response returned',
        'error' => null
    ];
}

function read_session_data($dir)
{
    $m = $m2 = $pr = [];
    $r = $c = $r2 = $c2 = $pc = 0;
    if (file_exists("$dir/matrix.txt")) {
        $L = file("$dir/matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $first = array_shift($L);
        if ($first !== null) {
            $p = preg_split('/\s+/', trim($first));
            $r = (int) ($p[0] ?? 0);
            $c = (int) ($p[1] ?? 0);
            for ($i = 0; $i < $r && isset($L[$i]); $i++)
                $m[] = array_map('intval', preg_split('/\s+/', trim($L[$i])));
        }
    }
    if (file_exists("$dir/rute_matrix.txt")) {
        $L = file("$dir/rute_matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $first = array_shift($L);
        if ($first !== null) {
            $p = preg_split('/\s+/', trim($first));
            $r2 = (int) ($p[0] ?? 0);
            $c2 = (int) ($p[1] ?? 0);
            for ($i = 0; $i < $r2 && isset($L[$i]); $i++)
                $m2[] = array_map('intval', preg_split('/\s+/', trim($L[$i])));
        }
    }
    if (file_exists("$dir/drawing.txt")) {
        $L = file("$dir/drawing.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $pc = intval(array_shift($L));
        if (!empty($L))
            $pr = array_map('intval', preg_split('/\s+/', trim(implode(" ", $L))));
    }
    $v = [];

    if (file_exists("$dir/apelari.txt")) {

        foreach (
            file("$dir/apelari.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
            as $line
        ) {

            $parts = explode(',', $line);
            $parts = array_map('trim', $parts);

            $v[] = $parts;
        }
    }
    $n = [];
    if (file_exists("$dir/returnari.txt")) {
        foreach (file("$dir/returnari.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l)
            if (preg_match_all('/-?\d+|true|false/i', $l, $mt))
                foreach ($mt[0] as $vv) {
                    $vv = strtolower($vv);
                    $n[] = ($vv === 'true') ? true : (($vv === 'false') ? false : (int) $vv);
                }
    }
    return [
        'app' => ['matrix' => $m, 'rows' => $r, 'cols' => $c],
        'app2' => ['matrix2' => $m2, 'rows2' => $r2, 'cols2' => $c2],
        'app3' => ['count' => $pc, 'data' => $pr],
        'apelari' => $v,
        'returnari' => $n,
        'compilare' => file_exists("$dir/compilare.txt") ? file_get_contents("$dir/compilare.txt") : '',
        'iesire' => file_exists("$dir/iesire.txt") ? file_get_contents("$dir/iesire.txt") : ''
    ];
}
$lang = $_SESSION['lang'] ?? 'ro';
if (!is_file(__DIR__ . "/{$lang}.php"))
    $lang = 'ro';
$t = require __DIR__ . "/{$lang}.php";
$safeShareId = '';
$requestedShareId = trim($_GET['share'] ?? '');
if ($requestedShareId !== '') {
    $safeShareId = preg_replace('/[^a-zA-Z0-9_-]/', '', $requestedShareId);
    if ($safeShareId !== '') {
        $candidateShareDir = $sharesRoot . '/' . $safeShareId;
        if (is_dir($candidateShareDir)) {
            $dir = $candidateShareDir;
            $isSharedView = true;
        }
    }
}
$shareQuerySuffix = ($isSharedView && $safeShareId !== '') ? '&share=' . urlencode($safeShareId) : '';
$currentPageUrl = 'index.php' . ($isSharedView && $safeShareId !== '' ? '?share=' . urlencode($safeShareId) : '');

function copy_share_files($fromDir, $toDir)
{
    $filesToCopy = [
        'matrix.txt',
        'rute_matrix.txt',
        'drawing.txt',
        'apelari.txt',
        'returnari.txt',
        'input.cpp',
        'text_test.txt',
        'user.cpp',
        'supVars.cpp',
        'call.txt',
        'iesire.txt'
    ];
    foreach ($filesToCopy as $fileName) {
        $source = $fromDir . '/' . $fileName;
        $destination = $toDir . '/' . $fileName;
        if (is_file($source))
            copy($source, $destination);
    }
}

$shareFeedback = $_SESSION['share_feedback'] ?? null;
if ($shareFeedback !== null)
    unset($_SESSION['share_feedback']);

$chatgptResponse = $_SESSION['chatgpt_response'] ?? null;
if ($chatgptResponse !== null)
    unset($_SESSION['chatgpt_response']);

$chatgptError = $_SESSION['chatgpt_error'] ?? null;
if ($chatgptError !== null)
    unset($_SESSION['chatgpt_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['chat_request'])) {

    $msg = trim($_POST['message'] ?? '');
    if ($msg === '') {
        send_json(['success' => false, 'error' => 'Empty message']);
    }

    if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [
        [
            "role" => "system",
            "content" =>
                "You are a coding assistant integrated inside a recursion visualizer website.
                The user will ask questions about THEIR OWN code.
                Always answer short, clear and relevant to the current code.
                Remember previous conversation context."
        ]
    ];
}

    $_SESSION['chat_history'][] = ["role" => "user", "content" => $msg];

   $messages = $_SESSION['chat_history'];

    $result = intrebare_chat($messages, $chatgptkey);

    if (!$result['error']) {
        $_SESSION['chat_history'][] = [
            "role" => "assistant",
            "content" => $result['data']
        ];
    }

    send_json([
        'success' => empty($result['error']),
        'response' => $result['data'],
        'error' => $result['error'] ?? null
    ]);
}

    // ========== ChatGPT handler ========== 
    if (isset($_POST['chatgpt_request'])) {
        $chatgptCode = trim($_POST['chatgpt_code'] ?? '');
        if ($chatgptCode === '') {
            $candidateCodePath = "$dir/user.cpp";
            if (is_file($candidateCodePath))
                $chatgptCode = file_get_contents($candidateCodePath) ?: '';
        }
        if ($chatgptCode === '') {
            if ($isAjax)
                send_json(['success' => false, 'error' => 'No code available to describe yet.']);
            $_SESSION['chatgpt_error'] = 'No code is available to describe yet.';
            header("Location: " . $currentPageUrl);
            exit;
        }
        $prompt = "Describe this code in detail and point out what it does, how it works, and any important variables or functions. Do not use markdown and write in $lang language. \n\nCode:\n" . $chatgptCode;
        $result = intrebare($prompt, $chatgptkey, $lang);

        if ($isAjax) {
            send_json([
                'success' => empty($result['error']),
                'response' => $result['data'] ?: '',
                'error' => $result['error'] ?? null
            ]);
        }

        if (!empty($result['error'])) {
            $_SESSION['chatgpt_error'] = $result['error'];
            $_SESSION['chatgpt_response'] = $result['data'] ?: 'ChatGPT request failed.';
        } else {
            $_SESSION['chatgpt_response'] = $result['data'] ?: 'No response received.';
        }
        header("Location: " . $currentPageUrl);
        exit;
    }

    // ========== Share handler ========== 
    if (isset($_POST['share_request'])) {
        $sharerName = trim($_POST['sharer_name'] ?? '');
        $shareTitle = trim($_POST['share_title'] ?? '');

        if ($sharerName === '' || $shareTitle === '') {
            if ($isAjax)
                send_json(['success' => false, 'error' => $t['share_err_fields']]);
            $_SESSION['share_feedback'] = ['type' => 'error', 'message' => $t['share_err_fields']];
            header("Location: index.php");
            exit;
        }

        if (!is_dir($sessionDir)) {
            if ($isAjax)
                send_json(['success' => false, 'error' => $t['share_err_no_data']]);
            $_SESSION['share_feedback'] = ['type' => 'error', 'message' => $t['share_err_no_data']];
            header("Location: index.php");
            exit;
        }

        if (!is_dir($sharesRoot))
            mkdir($sharesRoot, 0777, true);

        $shareId = bin2hex(random_bytes(8));
        $shareDir = $sharesRoot . '/' . $shareId;

        if (!mkdir($shareDir, 0777, true) && !is_dir($shareDir)) {
            if ($isAjax)
                send_json(['success' => false, 'error' => $t['share_err_folder']]);
            $_SESSION['share_feedback'] = ['type' => 'error', 'message' => $t['share_err_folder']];
            header("Location: index.php");
            exit;
        }

        copy_share_files($sessionDir, $shareDir);
        $meta = ['id' => $shareId, 'name' => $sharerName, 'title' => $shareTitle, 'created_at' => date('c')];
        file_put_contents($shareDir . '/meta.json', json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $shareUrl = 'https://recursio.ro/?share=' . urlencode($shareId);

        if ($isAjax) {
            send_json([
                'success' => true,
                'message' => $t['shared_ok'],
                'title' => $shareTitle,
                'name' => $sharerName,
                'url' => $shareUrl
            ]);
        }

        $_SESSION['share_feedback'] = [
            'type' => 'success',
            'message' => $t['shared_ok'],
            'title' => $shareTitle,
            'name' => $sharerName,
            'url' => $shareUrl
        ];
        header("Location: index.php");
        exit;
    }

    // ========== Execute handler ========== 
    $_SESSION['algorithm'] = $_POST['algorithm'] ?? 'custom';

    $code = $_POST['code'] ?? '';
    $call = $_POST['call'] ?? '';
    $supVars = $_POST['supVars'] ?? '';

    $_SESSION['code'] = $code;
    $_SESSION['call'] = $call;
    $_SESSION['supVars'] = $supVars;

    if ($code !== '' && $call !== '') {
        $call = trim($call);
        $_SESSION['just_submitted'] = true;
        $workDir = $sessionDir;

        if (!is_dir($workDir))
            mkdir($workDir, 0777, true);

        file_put_contents("$workDir/call.txt", $call);
        file_put_contents("$workDir/user.cpp", $code);
        if ($supVars == '') {
            $par = 0;
        } else {
            $par = 1;
            $supVars = trim($supVars);
            file_put_contents("$workDir/supVars.cpp", $supVars);
        }

        $tip = strtok($code, " \t\n");

        $dockerpath = "../sessions/" . session_id();

        if ($tip == "void") {
            $cmd = __DIR__ . ($par == 0 ? "/void.sh " : "/voidcuparametri.sh ") . escapeshellarg($call) . " " . escapeshellarg($dockerpath);
            $output = shell_exec($cmd . ' 2>&1');
            file_put_contents("$workDir/text_test.txt", $output);
            shell_exec(__DIR__ . "/a.out " . escapeshellarg($workDir) . ' 2>&1');
            $output = shell_exec(__DIR__ . "/void2.sh " . escapeshellarg($workDir) . ' 2>&1');
        } else {
            $cmd = __DIR__ . ($par == 0 ? "/int.sh " : "/intcuparametri.sh ") . escapeshellarg($call) . " " . escapeshellarg($dockerpath);
            $output = shell_exec($cmd . ' 2>&1');
            file_put_contents("$workDir/text_test.txt", $output);
            shell_exec(__DIR__ . "/a.out " . escapeshellarg($workDir) . ' 2>&1');
            $output = shell_exec(__DIR__ . "/int2.sh " . escapeshellarg($workDir) . ' 2>&1');
        }

	if($par == 0) {
            file_put_contents("$workDir/supVars.cpp", "");
        }

        $chatgptCode = $code . "\n\n// Supplementary variables:\n" . $supVars . "\n\n// Function call:\n" . $call;
        $prompt = "Describe this code in detail and point out what it does, how it works, and any important variables or functions. Do not use markdown and write in $lang language. \n\nCode:\n" . $chatgptCode;
        
        
        $result = intrebare($prompt, $chatgptkey, $lang);

        // === FIX BUG "PRIMUL CLICK FACE REFRESH" - partea PHP ===
        // Returnam INTOTDEAUNA JSON, indiferent daca request-ul a venit ca AJAX
        // sau ca submit clasic (fara header X-Requested-With). Asta elimina
        // header("Location:") pe non-AJAX, deci browser-ul NU mai face navigatie
        // (= refresh) chiar daca cumva fix-ul JS de mai jos esueaza.
        $data = read_session_data($workDir);
        send_json(array_merge($data, [
            'success' => empty($result['error']) && empty($data['compilare']),
            'response' => $result['data'] ?? '',
            'error' => $result['error'] ?? null
        ]));
    }
}

// === Render normal: read data for initial page load === 
$matrix = $matrix2 = $progresare = [];
$rows = $cols = $rows2 = $cols2 = $progresare_cnt = 0;
if (file_exists("$dir/matrix.txt")) {
    $lines = file("$dir/matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!empty($lines)) {
        list($rows, $cols) = explode(" ", array_shift($lines));
        for ($i = 0; $i < $rows; $i++) {
            $row = preg_split('/\s+/', $lines[$i]);
            $matrix[] = array_map('intval', $row);
        }
    }
}
if (file_exists("$dir/rute_matrix.txt")) {
    $lines2 = file("$dir/rute_matrix.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!empty($lines2)) {
        list($rows2, $cols2) = explode(" ", array_shift($lines2));
        for ($i = 0; $i < $rows2; $i++) {
            $row2 = preg_split('/\s+/', $lines2[$i]);
            $matrix2[] = array_map('intval', $row2);
        }
    }
}
if (file_exists("$dir/drawing.txt")) {
    $lines3 = file("$dir/drawing.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!empty($lines3)) {
        $progresare_cnt = intval(array_shift($lines3));
        $values = preg_split('/\s+/', implode(" ", $lines3));
        $progresare = array_map('intval', $values);
    }
}

$vars = [];
if (file_exists("$dir/apelari.txt")) {
    foreach (file("$dir/apelari.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $parts = explode(',', $line);
        $parts = array_map('trim', $parts);
        $vars[] = $parts;
    }
}

$numbers = [];
if (file_exists("$dir/returnari.txt")) {
    foreach (file("$dir/returnari.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (preg_match_all('/-?\d+|true|false/i', $line, $matches)) {
            foreach ($matches[0] as $value) {
                $value = strtolower($value);
                if ($value === 'true')
                    $numbers[] = true;
                elseif ($value === 'false')
                    $numbers[] = false;
                else
                    $numbers[] = (int) $value;
            }
        }
    }
}

$run_animation = !empty($_SESSION['just_submitted']) || $isSharedView;
if ($run_animation)
    unset($_SESSION['just_submitted']);

$displayCode = $_SESSION['code'] ?? '';
$displaySupVars = $_SESSION['supVars'] ?? '';
$displayCall = $_SESSION['call'] ?? '';
$shareMetaName = '';
$shareMetaTitle = '';
$shareMetaCreatedAt = '';

if ($isSharedView) {
    $displayCode = is_file("$dir/user.cpp") ? (file_get_contents("$dir/user.cpp") ?: '') : '';
    $displaySupVars = is_file("$dir/supVars.cpp") ? (file_get_contents("$dir/supVars.cpp") ?: '') : '';
    $displayCall = is_file("$dir/call.txt") ? (file_get_contents("$dir/call.txt") ?: '') : '';
    $metaPath = "$dir/meta.json";
    if (is_file($metaPath)) {
        $metaData = json_decode(file_get_contents($metaPath) ?: '', true);
        if (is_array($metaData)) {
            $shareMetaName = trim((string) ($metaData['name'] ?? ''));
            $shareMetaTitle = trim((string) ($metaData['title'] ?? ''));
            $createdAtRaw = trim((string) ($metaData['created_at'] ?? ''));
            if ($createdAtRaw !== '') {
                $timestamp = strtotime($createdAtRaw);
                $shareMetaCreatedAt = $timestamp !== false ? date('d.m.Y H:i:s', $timestamp) : $createdAtRaw;
            }
        }
    }
}
?>

<head>
    <meta charset="UTF-8">
    <title>Recursio</title>
    <link rel="icon" type="image/png" href="Recursio-logo-Spiral.png">
    <link rel="stylesheet" href="css_design.css">
    <link rel="stylesheet" href="design_menu.css">
    <link rel="stylesheet" href="side_bar.css">
    <link rel="stylesheet" href="share_css.css">
</head>

<body>
    <div id="zoomControls"> <button id="zoomOut">-</button> <button id="zoomIn">+</button>
        <div class="zoom_text_content" id="zoomBox">100%</div>
    </div>
    <div id="dragBox">
        <div id="dragBoxHeader"><b>↔</b></div>
        <?php if (!$isSharedView): ?> <select form="myForm" id="myDropdown" class="dropdown_container" name="algorithm">
                <option value="custom" <?= ($_SESSION['algorithm'] ?? 'custom') == 'custom' ? 'selected' : '' ?>>
                    <?= $t['algo_custom'] ?>
                </option>
                <option value="fibonacci" <?= ($_SESSION['algorithm'] ?? '') == 'fibonacci' ? 'selected' : '' ?>>
                    <?= $t['algo_fibonacci'] ?>
                </option>
                <option value="factorial" <?= ($_SESSION['algorithm'] ?? '') == 'factorial' ? 'selected' : '' ?>>
                    <?= $t['algo_factorial'] ?>
                </option>
                <option value="knapsack" <?= ($_SESSION['algorithm'] ?? '') == 'knapsack' ? 'selected' : '' ?>>
                    <?= $t['algo_knapsack'] ?>
                </option>
                <option value="coinChange" <?= ($_SESSION['algorithm'] ?? '') == 'coinChange' ? 'selected' : '' ?>>
                    <?= $t['algo_coin_change'] ?>
                </option>
                <option value="sumDigits" <?= ($_SESSION['algorithm'] ?? '') == 'sumDigits' ? 'selected' : '' ?>>
                    <?= $t['algo_sum_digits'] ?>
                </option>
                <option value="binarySearch" <?= ($_SESSION['algorithm'] ?? '') == 'binarySearch' ? 'selected' : '' ?>>
                    <?= $t['algo_binary_search'] ?>
                </option>
                <option value="power" <?= ($_SESSION['algorithm'] ?? '') == 'power' ? 'selected' : '' ?>>
                    <?= $t['algo_power'] ?>
                </option>
                <option value="palindrome" <?= ($_SESSION['algorithm'] ?? '') == 'palindrome' ? 'selected' : '' ?>>
                    <?= $t['algo_palindrome'] ?>
                </option>
            </select>
        <?php endif; ?>
        <div class="text_div">
            <?php if ($isSharedView): ?>
                <div class="share-meta-panel">
                    <div><strong>
                            <?= $t['meta_author'] ?>:
                        </strong>
                        <?= htmlspecialchars($shareMetaName !== '' ? $shareMetaName : '-') ?>
                    </div>
                    <div><strong>
                            <?= $t['meta_title'] ?>:
                        </strong>
                        <?= htmlspecialchars($shareMetaTitle !== '' ? $shareMetaTitle : '-') ?>
                    </div>
                    <div><strong>
                            <?= $t['meta_created'] ?>:
                        </strong>
                        <?= htmlspecialchars($shareMetaCreatedAt !== '' ? $shareMetaCreatedAt . " UTC" : '-') ?>
                    </div>
                </div>
            <?php endif; ?> <textarea form="myForm" name="code" class="code_input_field" id="textarea1"
                placeholder="<?= $t['placeholder_code'] ?>" <?= $isSharedView ? 'readonly' : '' ?>><?= htmlspecialchars($displayCode) ?></textarea> <textarea form="myForm" name="supVars"
                class="supValue_input_field" id="textarea3" placeholder="<?= $t['placeholder_vars'] ?>" <?= $isSharedView ? 'readonly' : '' ?>><?= htmlspecialchars($displaySupVars) ?></textarea> <textarea form="myForm"
                name="call" class="value_input_field" rows="1" id="textarea2"
                placeholder="<?= $t['placeholder_call'] ?>" <?= $isSharedView ? 'readonly' : '' ?>><?= htmlspecialchars($displayCall) ?></textarea>
        </div>
        <?php if (!$isSharedView): ?> <button form="myForm" type="submit" id="executeButton"><?= $t['execute'] ?></button>
        <?php endif; ?>
        <form method="post" id="myForm" onsubmit="event.preventDefault(); return false;" style="margin:0;padding:0;"></form>
    </div>
    <div id="aiOverlay" class="ai-overlay" onclick="closeAiSidebar()"></div>
    <div id="aiSidebar" class="ai-sidebar">
        <div class="ai-sidebar-item">
            <div class="ai-sidebar-header"> <strong> <?= $t['ai_sidebar_title'] ?></strong>
                <div class="ai-sidebar-subtitle"> <?= $t['ai_sidebar_subtitle'] ?></div>
            </div>
            <form method="post" action="<?= htmlspecialchars($currentPageUrl) ?>" id="chatgptForm" class="ai-form">
                <input type="hidden" name="chatgpt_request" value="1"> <input type="hidden" name="chatgpt_code"
                    id="chatgptCodeHidden" value=""> 
            </form> <label class="ai-response-label"><?= $t['ai_response_label'] ?></label> 
            <textarea readonly class="ai-response" id="chatgptResponseText"
    placeholder="The AI's explanation will appear here..."></textarea>
                
                
            </textarea>
            <div id="aiChatBox"> 
                <input id="aiChatInput" type="text" placeholder="Ask AI..." class="ai-input-field">
                
            </div>
            <div>
                <button id="aiSendBtn" type="button" id="chatgptButton" class="ai-explain-btn"><?= $t['send'] ?></button>
            </div>
            <div class="ai-response-actions"> <button type="button" id="aiCopyBtn" class="ai-action-btn">📋
                    <?= $t['copy'] ?></button> <button type="button" id="aiClearBtn" class="ai-action-btn">🗑 <?= $t['clear'] ?></button> </div>
        </div>
    </div>
    <div id="compilationOutputWrapper" class="compilation_output_wrapper"
        style="<?= (file_exists("$dir/compilare.txt") && filesize("$dir/compilare.txt") !== 0 && !$isSharedView) ? '' : 'display:none;' ?>">
        <button type="button" id="compilationCloseX" class="compilation-close-x" aria-label="Close"
            onclick="document.getElementById('compilationOutputWrapper').classList.add('hidden');">×</button> <textarea
            id="compilationOutput" class="compilation_output"
            readonly><?= (file_exists("$dir/compilare.txt") && filesize("$dir/compilare.txt") !== 0 && !$isSharedView) ? ($t['compile_error'] . "\n\n" . htmlspecialchars(file_get_contents("$dir/compilare.txt"))) : '' ?></textarea>
    </div>
    <div class="navbar_top">
        <div class="navbar_top-left"> <button type="button" class="ai-toggle-nav-btn" id="aiToggleBtn"
                onclick="toggleAiSidebar()"> <?= $t['ai_sidebar_title'] ?> </button> <button type="button" class="ai-toggle-nav-btn"
                onclick="window.location.href='recursivitate.php'"> <?= $t['learn_recursion'] ?> </button></div>
        <div class="navbar_top-center">
            <img class="navbar_logo" src="Recursio-logo-dark-bg-2-2.png">
        </div>
        <div class="navbar_top-right"> <button type="button" class="hide2" id="toggleCompilationOutput"
                data-hide-label="<?= $t['hide_compile'] ?>" data-show-label="<?= $t['show_compile'] ?>"
                style="<?= (file_exists("$dir/compilare.txt") && filesize("$dir/compilare.txt") !== 0) ? '' : 'display:none;' ?>">
                <?= $t['hide_compile'] ?>
            </button> <button type="button" class="hide2" id="toggleProgramOutput"
                data-hide-label="<?= $t['hide_output'] ?>" data-show-label="<?= $t['show_output'] ?>"
                style="<?= (file_exists("$dir/iesire.txt") && filesize("$dir/iesire.txt") !== 0) ? '' : 'display:none;' ?>">
                <?= $t['hide_output'] ?>
            </button>
            <?php if (!$isSharedView): ?> <button class="share" id="share" onclick="openShareModal(); return false;">
                    <?= $t['share_animation'] ?>
                </button>
            <?php endif; ?>
            <?php if ($isSharedView): ?> <button class="share" id="share"
                    onclick="window.location.href='index.php';">
                    <?= $t['go_to_editor'] ?>
                </button>
            <?php endif; ?>
        </div>
        <div class="filter-dropdown"> <button type="button" class="filter-toggle"> <span> <?= $t['filters'] ?></span> </button>
            <div class="filter-dropdown-menu"> <label class="filter-option"> <span>
                        <?= $t['hide_timeline'] ?>
                    </span> <input type="checkbox" id="toggleTimelineVisibility" onclick="toggleTimelineVisibility()">
                    <span class="checkmark"></span> </label> <label class="filter-option"> <span>
                        <?= $t['shrink_timeline'] ?>
                    </span> <input type="checkbox" id="toggleTimeline" onclick="toggleTimeline()"> <span
                        class="checkmark"></span> </label> <label class="filter-option"> <span>
                        <?= $t['hide_code'] ?>
                    </span> <input type="checkbox" id="toggleDragBox"> <span class="checkmark"></span> </label> </div>
        </div>
        <div class="lang-dropdown"> <button type="button" class="lang-toggle" aria-label="Language selector"> <img
                    src="<?= htmlspecialchars($lang) ?>.png" alt="<?= htmlspecialchars($lang) ?> flag"> <span>
                </span> </button>
            <div class="lang-dropdown-menu"> <a class="lang-option"
                    href="set_lang.php?lang=ro<?= $shareQuerySuffix ?>"><img src="ro.png"
                        alt="Romanian flag"><span>RO</span></a> <a class="lang-option"
                    href="set_lang.php?lang=en<?= $shareQuerySuffix ?>"><img src="en.png"
                        alt="English flag"><span>EN</span></a> <a class="lang-option"
                    href="set_lang.php?lang=hu<?= $shareQuerySuffix ?>"><img src="hu.png"
                        alt="Hungarian flag"><span>HU</span></a> </div>
        </div>
    </div> <button id="legendBtn" class="legend-btn">&#x21D2</button>
    <div id="legendOverlay" class="legend-overlay" onclick="closeLegend()"></div>
    <div id="legendSidebar" class="legend-sidebar">
        <div class="legend-item">
            <div class="legend-visuals">
                <div class="legend-icon-box">
                    <div class="circle_demo"><span class="circle_number_demo">1</span></div>
                </div>
                <div class="legend-icon-box"><svg width="34" height="34">
                        <line x1="17" y1="8" x2="17" y2="26" class="legend-line" />
                    </svg></div>
                <div class="legend-icon-box"><svg width="34" height="34">
                        <polygon points="17,6 11,26 23,26" class="legend-arrow" />
                    </svg></div>
                <div class="legend-icon-box"><svg width="34" height="34"><text x="17" y="18" text-anchor="middle"
                            class="legend-arrow-text">5</text></svg></div>
                <div class="legend-icon-box"><svg width="34" height="34"><text x="17" y="18" text-anchor="middle"
                            class="legend-arrow-text2">0</text></svg></div>
            </div>
            <div class="legend-texts">
                <p> <?= $t['legend_param'] ?></p>
                <p> <?= $t['legend_connection'] ?></p>
                <p> <?= $t['legend_direction'] ?></p>
                <p> <?= $t['legend_value'] ?></p>
                <p> <?= $t['legend_final_number'] ?></p>
            </div>
        </div>
        <div class="output_container" id="output_id_container">
            <p id="outputLabel"
                style="<?= (file_exists("$dir/iesire.txt") && filesize("$dir/iesire.txt") !== 0 && !$isSharedView) ? '' : 'display:none;' ?>">
                <?= $t['output'] ?>
            </p> <textarea id="programOutput" class="output_output" readonly
                style="<?= (file_exists("$dir/iesire.txt") && filesize("$dir/iesire.txt") !== 0 && !$isSharedView) ? '' : 'display:none;' ?>"><?= (file_exists("$dir/iesire.txt") && filesize("$dir/iesire.txt") !== 0 && !$isSharedView) ? (htmlspecialchars(ltrim(file_get_contents("$dir/iesire.txt")))) : '' ?></textarea>
        </div>
        <div class="nodes_and_levels">
            <p id="nodes"> <?= $t['nodes'] ?>: 0</p>
            <p id="levels"> <?= $t['levels'] ?>: 0</p>
        </div>
    </div>
    <div id="shareModalOverlay" class="share-modal-overlay" aria-hidden="true">
        <div class="share-modal" role="dialog" aria-modal="true" aria-labelledby="shareModalTitle">
            <h3 id="shareModalTitle">
                <?= $t['share_modal_title'] ?>
            </h3> <label for="shareNameInput"><?= $t['your_name'] ?></label> <input id="shareNameInput" type="text"
                placeholder="<?= $t['name_placeholder'] ?>" maxlength="80" autocomplete="name"> <label
                for="shareTitleInput"><?= $t['anim_title'] ?></label> <input id="shareTitleInput" type="text"
                placeholder="<?= $t['title_placeholder'] ?>" maxlength="120" autocomplete="off">
            <div class="share-modal-actions"> <button id="closeShareModal" type="button">
                    <?= $t['cancel'] ?>
                </button> <button id="confirmShareModal" type="button">
                    <?= $t['continue'] ?>
                </button> </div>
            <div id="shareResultBox" class="share-result-box"> <button type="button" id="shareCloseX"
                    class="share-close-x">×</button>
                <div id="shareStatusMessage"></div>
                 <input id="shareLinkOutput" type="text" readonly> <button type="button"
                    id="copyShareLinkBtn"><?= $t['copy'] ?></button>
            </div>
        </div>
    </div>
    <div class="custom_scroll_wrapper"></div>
    <div id="timelineContainer"> <input type="range" id="timelineBar" min="0" max="100" value="0">
        <div id="timelineControls">
            <div class="centerControls"> <button id="prevStep" title="Previous Step"></button> <button id="playPause"
                    title="Play/Pause" onclick="clik_verificare()">
                    <div id="icon" class="play-icon"></div>
                </button> <button id="nextStep" title="Next Step"></button> </div>
            <div class="speedControl"> <span class="x_icon">x</span> <span id="speedup_text">1</span>
                <div class="increase_decrease"> <button id="plus" onclick="plus_button()">+</button> <button id="minus"
                        onclick="minus_button()">-</button> </div>
            </div>
        </div> <span id="timelineLabel">0 / 0</span>
    </div>
    <div id="pageContent">
        <div id="container" class="container67"> <svg id="svg-lines"></svg> </div>
    </div>
    <script> /* ================= GLOBAL DATA ================= */
        window.APP = { matrix: <?= json_encode($matrix) ?>, rows: <?= $rows ?>, cols: <?= $cols ?> };
        window.APP2 = { matrix2: <?= json_encode($matrix2) ?>, rows2: <?= $rows2 ?>, cols2: <?= $cols2 ?> };
        window.APP3 = { count: <?= $progresare_cnt ?>, data: <?= json_encode($progresare) ?> };
        window.APP_VARS = <?= json_encode($vars) ?>;
        window.REVERSED_NUMBERS = <?= json_encode($numbers) ?>;

        /* ================= AUTO-START (SHARED VIEW) ================= */
        <?php if ($isSharedView): ?>
            window.addEventListener('load', () => {
                if (typeof window.softResetAndRun === 'function') window.softResetAndRun();
            });
        <?php endif; ?>

        /* ================= HELPER: RESET + RUN ================= */
        window.softResetAndRun = function () {
            if (!window.canRunAnimation()) return;
            const tb = document.getElementById('timelineBar');
            if (tb) tb.value = 0;

            const tl = document.getElementById('timelineLabel');
            if (tl) tl.textContent = '0 / 0';

            if (typeof window.generateAnimation === 'function') {
                try { window.generateAnimation(); } catch (e) { console.error('generateAnimation failed:', e); }
            }
            if (typeof window.startAnimation === 'function') {
                try { window.startAnimation(); } catch (e) { console.error('startAnimation failed:', e); }
            }
        };

        /* ================= AI SIDEBAR (GLOBAL) ================= */
        function openAiSidebar() {
            const sb = document.getElementById('aiSidebar');
            const ov = document.getElementById('aiOverlay');
            const btn = document.getElementById('aiToggleBtn');
            if (sb) sb.classList.add('open');
            if (ov) ov.classList.add('open');
            if (btn) btn.classList.remove('pulse');
        }

        function toggleAiSidebar() {
            const sb = document.getElementById('aiSidebar');
            if (sb && sb.classList.contains('open')) {
                closeAiSidebar();
            } else {
                openAiSidebar();
            }
        }

        function closeAiSidebar() {
            const sb = document.getElementById('aiSidebar');
            const ov = document.getElementById('aiOverlay');
            if (sb) sb.classList.remove('open');
            if (ov) ov.classList.remove('open');
        }

        /* ================= CHATGPT SYNC ================= */

        function syncChatgptCode() {
            const codeTextarea = document.getElementById('textarea1');
            const hiddenCode = document.getElementById('chatgptCodeHidden');
            if (codeTextarea && hiddenCode) hiddenCode.value = codeTextarea.value || '';
        }

        /* ================= SHARE MODAL ================= */

        function openShareModal() {
            const overlay = document.getElementById('shareModalOverlay');
            const nameInput = document.getElementById('shareNameInput');
            if (!overlay || !nameInput) return;
            overlay.classList.add('open');
            overlay.setAttribute('aria-hidden', 'false');
            setTimeout(() => nameInput.focus(), 10);
        }

        function closeShareModal() {
            const overlay = document.getElementById('shareModalOverlay');
            if (!overlay) return;
            overlay.classList.remove('open');
            overlay.setAttribute('aria-hidden', 'true');
        }

        /* ============================================================ */
        /* === FIX BUG "PRIMUL CLICK FACE REFRESH" - partea JS ======== */
        /* ============================================================ */
        /* Inregistram handler-ul de submit IMEDIAT, NU in DOMContentLoaded. */
        /* Script-urile externe de mai jos blocheaza DOMContentLoaded sute   */
        /* de ms - daca utilizatorul da click in fereastra aceea, form-ul    */
        /* se submite default (fara X-Requested-With) -> PHP raspunde si     */
        /* browserul navigheaza = arata ca refresh.                          */
        /* Script-ul fiind la finalul body-ului, toate elementele DOM        */
        /* (form, button, textarea) sunt deja disponibile cand cod-ul rulea. */
        (function attachExecuteHandlerEarly() {
            const executeForm = document.getElementById('myForm');
            if (!executeForm) return;

            function hardResetAnimationState() {
                window.queueIndex = 0;
                window.stepRunning = false;
                window.iconita = 0;
                window.contor = 0;
                window.pauseRequested = false;
                window.isPlaying = false;
                const svg = document.getElementById("svg-lines");
                if (svg) svg.innerHTML = "";
                document.querySelectorAll(".circle_div").forEach(el => el.remove());
            }

            executeForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const executeButton = document.getElementById('executeButton');
                const code = document.getElementById('textarea1')?.value || '';
                const call = document.getElementById('textarea2')?.value || '';
                const supVars = document.getElementById('textarea3')?.value || '';
                const dd = document.getElementById('myDropdown');
                const algorithm = dd ? dd.value : 'custom';

                const compilationOutput = document.getElementById('compilationOutput');
                const compilationWrapper = document.getElementById('compilationOutputWrapper');
                const toggleCompilationOutput = document.getElementById('toggleCompilationOutput');
                const programOutput = document.getElementById('programOutput');
                const outputLabel = document.getElementById('outputLabel');
                const toggleProgramOutput = document.getElementById('toggleProgramOutput');
                const chatgptResponseText = document.getElementById('chatgptResponseText');
                const chatgptPanel = document.getElementById('chatgptPanel');

                if (executeButton) {
                    executeButton.disabled = true;
                    executeButton.dataset.oldLabel = executeButton.textContent;
                    executeButton.textContent = '...';
                }
                if (typeof isPlaying !== 'undefined') isPlaying = false;
                if (typeof pauseRequested !== 'undefined') pauseRequested = true;

                const fd = new FormData();
                fd.append('code', code);
                fd.append('call', call);
                fd.append('supVars', supVars);
                fd.append('algorithm', algorithm);

                try {
                    const res = await fetch('index.php', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: fd,
                        credentials: 'same-origin'
                    });

                    const ct = res.headers.get('content-type') || '';
                    if (!ct.includes('application/json')) {
                        console.error('Non-JSON:', (await res.text()).slice(0, 300));
                        return;
                    }

                    const data = await res.json();
                    if (!data.success) {
                        // clear old animation DOM
                        const svg = document.getElementById("svg-lines");
                        if (svg) svg.innerHTML = "";
                        document.querySelectorAll(".circle_div").forEach(el => el.remove());
                        // HARD STOP
                        window.animationBlocked = true;
                        hardResetAnimationState();
                        const tb = document.getElementById('timelineBar');
                        if (tb) tb.value = 0;
                        const tl = document.getElementById('timelineLabel');
                        if (tl) tl.textContent = '0 / 0';
                        // SHOW COMPILATION ERROR
                        if (compilationOutput && compilationWrapper) {
                            compilationOutput.value =
                                '<?= addslashes($t['compile_error']) ?>' + "\n\n" +
                                (data.compilare || 'Unknown compilation error');
                            compilationWrapper.classList.remove('hidden');
                            compilationWrapper.style.display = '';
                            if (toggleCompilationOutput) toggleCompilationOutput.style.display = '';
                        }
                        if (programOutput) {
                            programOutput.value = '';
                            programOutput.style.display = 'none';
                            if (outputLabel) outputLabel.style.display = 'none';
                            if (toggleProgramOutput) toggleProgramOutput.style.display = 'none';
                        }
                        return;
                    }

                    if (chatgptResponseText) {
                        chatgptResponseText.value = data.response || '';
                    }

                    /* ===== UPDATE GLOBALS ===== */
                    window.APP = data.app;
                    window.APP2 = data.app2;
                    window.APP3 = data.app3;
                    window.APP_VARS = data.apelari;
                    window.REVERSED_NUMBERS = data.returnari;

                    if (compilationOutput && compilationWrapper) {
                        if (data.compilare && data.compilare.length > 0) {
                            compilationOutput.value = '<?= addslashes($t['compile_error']) ?>' + "\n\n" + data.compilare;
                            compilationWrapper.classList.remove('hidden');
                            compilationWrapper.style.display = '';
                            if (toggleCompilationOutput) toggleCompilationOutput.style.display = '';
                        } else {
                            compilationOutput.value = '';
                            compilationWrapper.classList.add('hidden');
                            compilationWrapper.style.display = 'none';
                            if (toggleCompilationOutput) toggleCompilationOutput.style.display = 'none';
                        }
                    }

                    if (programOutput) {
                        if (data.iesire && data.iesire.trim().length > 0) {
                            programOutput.value = data.iesire.replace(/^\s+/, '');
                            programOutput.style.display = '';
                            if (outputLabel) outputLabel.style.display = '';
                            if (toggleProgramOutput) toggleProgramOutput.style.display = '';
                        } else {
                            programOutput.value = '';
                            programOutput.style.display = 'none';
                            if (outputLabel) outputLabel.style.display = 'none';
                            if (toggleProgramOutput) toggleProgramOutput.style.display = 'none';
                        }
                    }

                    if (chatgptPanel) chatgptPanel.style.display = '';
                    window.animationBlocked = false;
                    hardResetAnimationState();

                    /* ===== RUN ANIMATION =====
                       generateAnimation / startAnimation vin din js_generation.js / Animation.js
                       (scripturi externe). Pe primul click este posibil sa NU fie inca incarcate
                       cand vine raspunsul fetch-ului. Polluim pana sunt disponibile. */
                    const tryRun = (attempts) => {
                        if (typeof window.generateAnimation === 'function') {
                            window.softResetAndRun();
                        } else if (attempts < 200) {
                            setTimeout(() => tryRun(attempts + 1), 50);
                        } else {
                            console.warn('Animation functions never loaded');
                        }
                    };
                    tryRun(0);

                    /* ===== AI button pulse ===== */
                    const aiBtn = document.getElementById('aiToggleBtn');
                    const aiSb = document.getElementById('aiSidebar');
                    if (aiBtn && aiSb && !aiSb.classList.contains('open')) {
                        aiBtn.classList.add('pulse');
                    }

                } catch (err) {
                    console.error(err);
                } finally {
                    const executeButton = document.getElementById('executeButton');
                    if (executeButton) {
                        executeButton.disabled = false;
                        executeButton.textContent = executeButton.dataset.oldLabel || 'Execute';
                    }
                }
            });
        })();
        /* ============================================================ */
        /* === SFARSITUL FIX-ULUI ===================================== */
        /* ============================================================ */

        /* ================= DOM READY ================= */

        document.addEventListener('DOMContentLoaded', () => {

            /* ===== ELEMENTS ===== */

            const overlay = document.getElementById('shareModalOverlay');
            const closeBtn = document.getElementById('closeShareModal');
            const confirmBtn = document.getElementById('confirmShareModal');
            const nameInput = document.getElementById('shareNameInput');
            const titleInput = document.getElementById('shareTitleInput');
            const resultBox = document.getElementById('shareResultBox');
            const linkBox = document.getElementById('shareLinkOutput');
            const msgBox = document.getElementById('shareStatusMessage');
            const metaBox = document.getElementById('shareMetaInfo');
            const copyBtn = document.getElementById('copyShareLinkBtn');
            const closeX = document.getElementById('shareCloseX');

            const compilationOutput = document.getElementById('compilationOutput');
            const programOutput = document.getElementById('programOutput');
            const toggleCompilationOutput = document.getElementById('toggleCompilationOutput');
            const toggleProgramOutput = document.getElementById('toggleProgramOutput');

            const chatgptForm = document.getElementById('chatgptForm');
            const chatgptButton = document.getElementById('chatgptButton');
            const chatgptPanel = document.getElementById('chatgptPanel');
            const chatgptResponseText = document.getElementById('chatgptResponseText');
            const chatgptCodeHidden = document.getElementById('chatgptCodeHidden');

            const executeForm = document.getElementById('myForm');
            const executeButton = document.getElementById('executeButton');
            const outputLabel = document.getElementById("outputLabel");
            const compilationWrapper = document.getElementById('compilationOutputWrapper');
            const compilationCloseX = document.getElementById('compilationCloseX');
            const legendSidebar = document.querySelector('.legend-sidebar');

            const aiSendBtn = document.getElementById('aiSendBtn');
const aiInput = document.getElementById('aiChatInput');
const aiMessages = document.getElementById('aiMessages');

if (aiSendBtn) {

    aiSendBtn.addEventListener('click', async () => {

        const text = aiInput.value.trim();

        if (!text) return;

        aiInput.value = '';

        // luam codul curent
        const currentCode =
            document.getElementById('textarea1')?.value || '';

        const currentCall =
            document.getElementById('textarea2')?.value || '';

        const currentVars =
            document.getElementById('textarea3')?.value || '';

        const fd = new FormData();

        fd.append('chat_request', '1');

        fd.append(
            'message',
            `
User question:
${text}

Current code:
${currentCode}

Supplementary variables:
${currentVars}

Function call:
${currentCall}
`
        );

        const res = await fetch('index.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: fd
        });

        const data = await res.json();

        // raspunsul apare DOAR in textarea
        if (chatgptResponseText) {

            chatgptResponseText.value =
                data.response || data.error || 'No response';

        }

    });

}

            /* ===== HELPERS ===== */

            const togglePanelVisibility = (el, btn) => {
                if (!el || !btn) return;
                const hidden = el.style.display === 'none';
                el.style.display = hidden ? '' : 'none';
                btn.textContent = hidden ? (btn.dataset.hideLabel || 'Hide') : (btn.dataset.showLabel || 'Show');
            };

            /* ================= MODAL ================= */
            if (closeBtn) closeBtn.addEventListener('click', closeShareModal);

            if (overlay) overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeShareModal();
            });

            document.addEventListener('keydown', (e) => {
                if (!overlay || !overlay.classList.contains('open')) return;
                if (e.key === 'Escape') closeShareModal();
            });

            if (closeX) closeX.addEventListener('click', () => {
                closeShareModal();
                if (resultBox) resultBox.style.display = 'none';
            });

            /* ================= TOGGLE OUTPUT ================= */
            if (toggleCompilationOutput && compilationWrapper) {
                toggleCompilationOutput.addEventListener('click', () =>
                    togglePanelVisibility(compilationWrapper, toggleCompilationOutput)
                );
            }

            if (toggleProgramOutput && programOutput) {
                toggleProgramOutput.addEventListener('click', () => {
                    togglePanelVisibility(programOutput, toggleProgramOutput);
                    if (outputLabel) outputLabel.style.display = programOutput.style.display;
                });
            }

            /* ================= COPY LINK ================= */

            if (copyBtn && linkBox) {
                copyBtn.addEventListener('click', async () => {
                    try {
                        await navigator.clipboard.writeText(linkBox.value);
                        copyBtn.innerText = 'Copied!';
                    } catch {
                        copyBtn.innerText = 'Copy failed';
                    }
                    setTimeout(() => copyBtn.innerText = 'Copy', 1200);
                });
            }

            /* ================= EXECUTE AJAX =================
               Handler was moved OUTSIDE DOMContentLoaded into an early IIFE
               (see "FIX BUG PRIMUL CLICK FACE REFRESH - partea JS" above).
               Keeping the comment as a breadcrumb. */

            /* ================= CHATGPT AJAX ================= */
            if (chatgptForm && chatgptButton) {
                chatgptForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    syncChatgptCode();

                    chatgptButton.disabled = true;
                    const old = chatgptButton.textContent;
                    chatgptButton.textContent = '...';

                    const fd = new FormData();
                    fd.append('chatgpt_request', '1');
                    fd.append('chatgpt_code', chatgptCodeHidden.value || '');

                    try {
                        const res = await fetch('<?= htmlspecialchars($currentPageUrl) ?>', {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            body: fd,
                            credentials: 'same-origin'
                        });

                        const data = await res.json();

                        if (chatgptResponseText) {
                            chatgptResponseText.value =
                                data.error
                                    ? 'Error: ' + data.error + '\n\n' + (data.response || '')
                                    : (data.response || '');
                        }
                    } catch (err) {
                        console.error(err);
                        if (chatgptResponseText)
                            chatgptResponseText.value = 'Network error: ' + err.message;
                    } finally {
                        chatgptButton.disabled = false;
                        chatgptButton.textContent = old;
                    }
                });
            }

            if (chatgptPanel) chatgptPanel.addEventListener('mousedown', () => syncChatgptCode());
            syncChatgptCode();

            /* ================= AI SIDEBAR ACTIONS ================= */
            const aiCopyBtn = document.getElementById('aiCopyBtn');
            const aiClearBtn = document.getElementById('aiClearBtn');

            document.addEventListener('keydown', (e) => {
                const sb = document.getElementById('aiSidebar');
                if (e.key === 'Escape' && sb && sb.classList.contains('open')) {
                    closeAiSidebar();
                }
            });

            if (aiCopyBtn && chatgptResponseText) {
                aiCopyBtn.addEventListener('click', async () => {
                    if (!chatgptResponseText.value.trim()) return;

                    try {
                        await navigator.clipboard.writeText(chatgptResponseText.value);
                        aiCopyBtn.textContent = '✓ Copied!';
                        setTimeout(() => aiCopyBtn.textContent = '📋 Copy', 1400);
                    } catch {
                        aiCopyBtn.textContent = 'Failed';
                    }
                });
            }

            if (aiClearBtn && chatgptResponseText) {
                aiClearBtn.addEventListener('click', () => {
                    chatgptResponseText.value = '';
                });
            }

            /* ================= SHARE AJAX ================= */
            function submitShareForm() {
                const sharerName = nameInput.value.trim();
                const shareTitle = titleInput.value.trim();

                if (sharerName === '') { nameInput.focus(); return; }
                if (shareTitle === '') { titleInput.focus(); return; }

                confirmBtn.disabled = true;
                const old = confirmBtn.textContent;
                confirmBtn.textContent = '...';

                const fd = new FormData();
                fd.append('share_request', '1');
                fd.append('sharer_name', sharerName);
                fd.append('share_title', shareTitle);

                fetch('index.php', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd,
                    credentials: 'same-origin'
                }).then(r => r.json()).then(data => {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = old;

                    if (!data.success) {
                        alert(data.error || 'Share failed');
                        return;
                    }

                    resultBox.style.display = 'block';
                    msgBox.innerHTML = '<strong>' + data.message + '</strong>';
                    //metaBox.innerHTML = 'Title: ' + data.title + '<br>By: ' + data.name;
                    linkBox.value = data.url;

                }).catch(err => {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = old;
                    alert('Network error: ' + err.message);
                });
            }
            /* ================= COMPILATION WARNING CLOSE ================= */


            if (compilationCloseX && compilationWrapper) {

                compilationCloseX.addEventListener('click', () => {

                    compilationWrapper.classList.add('hidden');
                    compilationWrapper.style.display = 'none';

                });
            }

            if (confirmBtn) confirmBtn.addEventListener('click', submitShareForm);
            document.addEventListener('keydown', (e) => {
                if (overlay && overlay.classList.contains('open') &&
                    e.key === 'Enter' && document.activeElement === titleInput) {
                    submitShareForm();
                }
            });

            <?php if ($shareFeedback && $shareFeedback['type'] === 'success'): ?>
                openShareModal();
                resultBox.style.display = 'block';
                msgBox.innerHTML = '<strong><?= addslashes($shareFeedback['message']) ?></strong>';
                metaBox.innerHTML = 'Title: <?= addslashes($shareFeedback['title'] ?? '') ?><br>By: <?= addslashes($shareFeedback['name'] ?? '') ?>';
                linkBox.value = '<?= addslashes($shareFeedback['url'] ?? '') ?>';
            <?php endif; ?>
        });

    </script>
    <script src="js_generation.js"></script>
    <script src="Animation.js"></script>
    <script src="Code_field.js"></script>
    <script src="Dropdown.js"></script>
    <script src="explanation.js"></script>
    <?php if ($output !== ''): ?>
        <pre><?= htmlspecialchars($output) ?></pre>
    <?php endif; ?>
</body>

</html>

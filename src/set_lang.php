<?php
session_start();
$allowed = ['en', 'ro', 'hu'];
$lang = $_GET['lang'] ?? 'ro';
$share = trim($_GET['share'] ?? '');
if (in_array($lang, $allowed)) {
    $_SESSION['lang'] = $lang;
}
$redirect = "index.php";
if ($share !== '') {
    $safeShareId = preg_replace('/[^a-zA-Z0-9_-]/', '', $share);
    if ($safeShareId !== '') {
        $redirect .= '?share=' . urlencode($safeShareId);
    }
}
header("Location: " . $redirect);
exit;
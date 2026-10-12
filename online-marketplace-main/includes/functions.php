<?php
session_start();
define('DATA_DIR', __DIR__ . '/../data/');

function loadData($file) {
    $path = DATA_DIR . $file;
    if (!file_exists($path)) {
        file_put_contents($path, json_encode([]));
    }
    return json_decode(file_get_contents($path), true);
}

function saveData($file, $data) {
    file_put_contents(DATA_DIR . $file, json_encode($data, JSON_PRETTY_PRINT));
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user']['role'] === 'admin';
}

function redirectTo($url) {
    header("Location: $url");
    exit();
}

function getNextId($items) {
    if (empty($items)) return 1;
    return max(array_column($items, 'id')) + 1;
}
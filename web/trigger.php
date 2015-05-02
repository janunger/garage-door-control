<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 400 Bad Request');
    echo 'Bad request';
    exit;
}

$dbParameters = require_once __DIR__ . '/../app/config/db_credentials.php';

$db = new PDO(
    'mysql:' . $dbParameters['database_host'] . ';dbname=' . $dbParameters['database_name'] . ';charset=utf8',
    $dbParameters['database_user'],
    $dbParameters['database_password']
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("USE " . $dbParameters['database_name']);

$db->exec("INSERT INTO command_queue (command, created_at) VALUES ('trigger-door', NOW())");

header('Content-Type: application/json');
echo '{}';
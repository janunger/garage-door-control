<?php

call_user_func(function () {
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

    $command = 'trigger-door';
    if (isset($_POST['sequence']) && '1' === $_POST['sequence']) {
        $command = 'close-after-one-transit';
    }
    $db->exec("INSERT INTO command_queue (command, created_at) VALUES ('$command', NOW())");

    header('Content-Type: application/json');
    echo '{}';
});

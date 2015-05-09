<?php

/**
 * @return PDO
 */
function initDatabase()
{
    $dbParameters = require_once __DIR__ . '/../app/config/db_credentials.php';

    $db = new PDO(
        'mysql:' . $dbParameters['database_host'] . ';dbname=' . $dbParameters['database_name'] . ';charset=utf8',
        $dbParameters['database_user'],
        $dbParameters['database_password']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("USE " . $dbParameters['database_name']);

    return $db;
}

/**
 * @return string
 */
function readCommand()
{
    $json = json_decode(file_get_contents('php://input'), true);

    if (isset($json['sequence']) && 1 === $json['sequence']) {
        return 'close-after-one-transit';
    }
    if (isset($json['cancel_sequence'])) {
        return 'cancel';
    }

    return 'trigger-door';
}

call_user_func(function () {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('HTTP/1.1 400 Bad Request');
        echo 'Bad request';
        exit;
    }

    $command = readCommand();

    $db = initDatabase();
    $db->exec("INSERT INTO command_queue (command, created_at) VALUES ('$command', NOW())");

    header('Content-Type: application/json');
    echo '{}';
});

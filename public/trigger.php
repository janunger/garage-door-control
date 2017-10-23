<?php

/**
 * @return PDO
 */
function initDatabase()
{
    $config = require __DIR__ . '/../etc/config.php';

    $db = new PDO(
        'mysql:host=' . $config['database_host'] . ';dbname=' . $config['database_name'] . ';charset=utf8',
        $config['database_username'],
        $config['database_password']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
    if (isset($json['sequence']) && 2 === $json['sequence']) {
        return 'close-after-two-transits';
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
    $statement = $db->prepare("INSERT INTO command_queue (command) VALUES (?)");
    $statement->execute([$command]);

    header('Content-Type: application/json');
    echo '{}';
});

<?php

/** @var $container \Symfony\Component\DependencyInjection\ContainerBuilder */

call_user_func(function () use ($container) {
    $parameters = require __DIR__ . '/db_credentials.php';

    foreach ($parameters as $name => $value) {
        $container->setParameter($name, $value);
    }
});

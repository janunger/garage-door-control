<?php

declare(strict_types=1);

use JUIT\GDC\ServiceContainer;

$config                    = require __DIR__ . '/../etc/config.php';
$config['app']['base_dir'] = dirname(__DIR__);

return new ServiceContainer($config);

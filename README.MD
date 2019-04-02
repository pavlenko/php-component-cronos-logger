## Requirements

The following versions of PHP are supported.

* PHP 7.1+

## Installation

To install, use composer:

```
php composer.phar require pe/component-cronos-logger
```

## Usage

```php
<?php

namespace PE\Component\Cronos;

use Doctrine\DBAL\Connection;
use PE\Component\Cronos\Core\ServerInterface;
use PE\Component\Cronos\Logger\Handler\HandlerDBAL;
use PE\Component\Cronos\Logger\Handler\HandlerEcho;
use PE\Component\Cronos\Logger\Handler\HandlerPSR;
use PE\Component\Cronos\Logger\LoggerModule;
use Psr\Log\LoggerInterface;

// Initialize server somewhere and use some of options below (one or more)
/* @var $server ServerInterface */
$server = '...';

// A) Attach simple echo logger
$module = new LoggerModule(new HandlerEcho());
$server->attachModule('console-logger', $module);

// B) Attach PSR compatible logger
/* @var $logger LoggerInterface */
$logger = '...';
$module = new LoggerModule(new HandlerPSR($logger));
$server->attachModule('console-logger', $module);

// C) Attach database logger
/* @var $connection Connection */
$connection = '...';

$module = new LoggerModule(new HandlerDBAL($connection, 'log_task'));
$server->attachModule('db-logger', $module);
```

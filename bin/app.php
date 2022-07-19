<?php

use Preset\Out;

try {
    require_once __DIR__ . '/../vendor/autoload.php';

    $commandManager = new Library\CommandManager($argv, false);
    $commandManager->register(new Out($commandManager->getInput(), $commandManager->getOutput()));

    $commandManager->run();
} catch (Throwable $e) {
    echo $e->getFile() ."\r\n";
    echo $e->getLine() ."\r\n";
    echo 'Ошибка: ' . $e->getMessage();
}
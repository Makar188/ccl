<?php

namespace Library;

use Console\Input\InputInterface;
use Console\Output\OutputInterface;

interface CommandInterface
{
    public function __construct(InputInterface $input, OutputInterface $output);

    /**
     * Получение описания команды
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Получение списка допустимых аргументов
     *
     * @return array
     */
    public function getAvailableArguments(): array;

    /**
     * Получение допустимых параметров
     *
     * @return array
     */
    public function getAvailableOptions(): array;

    /**
     * Выполнение команды
     */
    public function execute() : void;
}
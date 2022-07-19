<?php

namespace Console\Output;

interface OutputInterface
{
    /**
     * Вывод текста в поток
     *
     * @param string $message Выводимый текст
     */
    public function write(string $message);
}
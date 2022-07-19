<?php


namespace Console\Input;


interface InputInterface
{
    public function __construct(array $input);

    /**
     * Вернуть массив найденных аргументов
     * @return array
     */
    public function getArguments(): array;

    /**
     * Вернуть массив найденных опций
     * @return array
     */
    public function getOptions(): array;

    /**
     * Была вызвана подсказка
     *
     * @return bool
     */
    public function isHelpCalled(): bool;

    /**
     * Получение названия вызванной команды
     *
     * @return string|null
     */
    public function getCommandName(): ?string;
}
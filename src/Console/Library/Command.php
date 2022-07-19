<?php


namespace Library;

use Console\Input\InputInterface;
use Console\Output\OutputInterface;

abstract class Command implements CommandInterface
{
    /**
     * Допустимые аргументы
     *
     * @var array
     */
    protected array $arguments;

    /**
     * Допустимые параметры
     *
     * @var array
     */
    protected array $options;

    /**
     * Описание команды
     *
     * @var string
     */
    protected string $description;

    /**
     * Подготовленные параметры, аргументы, название вызванной команды
     *
     * @var InputInterface
     */
    protected InputInterface $input;

    /**
     * Поток вывода результатов работы
     *
     * @var OutputInterface
     */
    protected OutputInterface $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableArguments(): array
    {
        if(empty($this->arguments)) {
            return [];
        }

        return $this->arguments;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableOptions(): array
    {
        if(empty($this->options)) {
            return [];
        }

        return $this->options;
    }
}
<?php


namespace Console\Input;


class Input implements InputInterface
{
    /**
     * Имя ключевого аргумента для вывода описания
     */
    public const HELP_ARGUMENT = 'help';

    /**
     * Название выполняемой команды
     *
     * @var string|null
     */
    protected ?string $commandName = null;

    /**
     * Переданный набор параметров
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Переданный набор аргументов
     *
     * @var array
     */
    protected array $arguments = [];

    /**
     * Вызвана помощь
     *
     * @var bool
     */
    protected bool $isHelpCalled = false;

    public function __construct(array $input)
    {
        $this->prepareMainInfo($input);
        $this->parseArguments($input);
        $this->parseOptions($input);
    }

    /**
     * Получение основной информации по команде
     *
     * @param array $input
     */
    protected function prepareMainInfo(array &$input)
    {
        array_shift($input);
        $commandName = array_shift($input);
        if($commandName === self::HELP_ARGUMENT) {
            $this->isHelpCalled = true;
        } else {
            $this->commandName = $commandName;
            if(!empty($input) && $input[0] === self::HELP_ARGUMENT) {
                $this->isHelpCalled = true;
                array_shift($input);
            }
        }
    }

    public function isHelpCalled(): bool
    {
        return $this->isHelpCalled;
    }

    public function getCommandName(): ?string
    {
        return $this->commandName;
    }

    /**
     * Определение массива переданных аргументов
     *
     * @param array $input Аргументы командной строки
     */
    private function parseArguments(array &$input): self
    {
        $arguments = [];
        foreach($input as $index => $param)
        {
            preg_match('/^(?:(?!\[).)+$/', $param, $matches);
            if (empty($matches)) {
                continue;
            } else {
                $arguments[] = str_replace(['{', '}'], '', array_shift($matches));
                unset($input[$index]);
            }
        }
        $this->arguments = array_unique($arguments);

        return $this;
    }

    /**
     * Определение массива переданных параметров
     *
     * @param array $input Аргументы командной строки
     */
    protected function parseOptions(array $input): self
    {
        $this->options = [];
        foreach ($input as $param) {
            preg_match('/\[(.*?)\]/', $param, $matches);
            if (!empty($matches)) {
                list($option, $value) = explode("=", $matches[1]);

                if (isset($options[$option])) {
                    if(is_array($options[$option])) {
                        array_push($options[$option], $value);
                    }  else {
                        $this->options[$option] = [$options[$option], $value];
                    }
                } else {
                    $this->options[$option] = $value;
                }
            }
        }
        return $this;
    }

    /**
     * Возвращает переданный набор аргументов
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Возвращает переданный набор параметров
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
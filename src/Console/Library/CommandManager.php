<?php


namespace Library;

use Console\Output\OutputInterface;
use Exceptions;
use DirectoryIterator;
use Console\Input\Input;
use Console\Input\InputInterface;
use Console\Output\Output;
use Throwable;

class CommandManager
{
    /**
     * Название команды
     *
     * @var string
     */
    protected string $commandName;

    /**
     * Аргументы командной строки (без названия команды)
     *
     * @var array
     */
    protected array $arguments = [];

    /**
     * Зарегистрированные команды
     *
     * @var \Library\Command[]
     */
    private array $commands = [];

    /**
     * Преобразованные входящие параметры
     *
     * @var InputInterface
     */
    private InputInterface $input;

    /**
     * Поток вывода
     *
     * @var \Console\Output\OutputInterface
     */
    private OutputInterface $output;

    /**
     *
     * @param array $input Аргументы командной строки
     * @param bool $initPreset Нужно ли инициализировать предустановленные команды
     * @throws \Exceptions\EnvironmentException
     */
    public function __construct(array $input, bool $initPreset = true, ?InputInterface $inputObject = null, ?OutputInterface $outputObject = null)
    {
        if(php_sapi_name() !== 'cli') {
            throw new Exceptions\EnvironmentException('Выполнение команд доступно только в cli режиме');
        }

        if($initPreset) {
            $this->initPreset();
        }

        if($inputObject === null) {
            $this->initInput($input);
        } else {
            $this->input = $inputObject;
        }

        if($outputObject === null) {
            $this->initOutput();
        } else {
            $this->output = $outputObject;
        }
    }

    public function run()
    {
        if($this->input->getCommandName() === null) {
            $this->runCommandsInfo();
        } else {
            if(!$this->isExists($this->input->getCommandName())) {
                $this->output->write(sprintf('Command %s doesnt exists', $this->input->getCommandName()));
                return;
            }

            if($this->input->isHelpCalled()) {
                $command = $this->getCommandInstance($this->input->getCommandName());
                $this->writeArrayData('Arguments', $command->getAvailableArguments());
                $this->writeArrayData('Options', $command->getAvailableOptions());
            } else {
                $this->executeCommand($this->input->getCommandName());
            }
        }
    }

    /**
     * Получение экземпляра команды
     *
     * @param string $name Название команды
     * @return \Library\CommandInterface
     */
    private function getCommandInstance(string $name): CommandInterface
    {
        return is_object($this->commands[$name])? $this->commands[$name] : new $this->commands[$name]($this->input, $this->output);
    }

    /**
     * Парсинг параметров командной строки
     *
     * @param array $input
     * @return InputInterface
     */
    protected function initInput(array $input): InputInterface
    {
        $this->input = new Input($input);
        return $this->input;
    }

    /**
     * Инициализация предустановленных команд
     *
     * @return $this
     */
    public function initPreset(): self
    {
        $presetPath = realpath(__DIR__ . '/../Preset');
        if($presetPath === false) {
            return $this;
        }

        $iterator = new DirectoryIterator($presetPath);

        foreach($iterator as $presetFile) {
            if($presetFile->getExtension() != 'php') {
                continue;
            }
            $className = $presetFile->getBasename ('.php');
            $classReflection = new \ReflectionClass("Preset\\$className");
            if($classReflection->isSubclassOf(Command::class)) {
                $commandName = strtolower($className);
                if($commandName === Input::HELP_ARGUMENT) {
                    continue;
                }

                if(!$this->isExists($commandName)) {
                    $this->commands[strtolower($className)] = "Preset\\$className";
                }
            }
        }
        return $this;
    }

    /**
     * Инициализация поступившей команды
     *
     * @param string $name
     */
    protected function executeCommand(string $name)
    {
        try {
            $command = $this->getCommandInstance($name);
            $this->writeCommandName($name);
            $command->execute();
        } catch (Throwable $exception) {
            $this->output->write($exception->getMessage());
        }
    }

    /**
     * Зарегистрирована ли команда
     *
     * @param string $name Название команды
     * @return bool
     */
    public function isExists(string $name): bool
    {
        return isset($this->commands[$name]);
    }

    /**
     * Вывод описания  по всем зарегистрированным командам
     */
    protected function runCommandsInfo()
    {
        foreach ($this->commands as $name => $class)
        {
            $this->writeCommandName($name);
            $class = new $class($this->input, $this->output);
            $this->writeCommandDescription($class->getDescription());
            $this->writeArrayData('Arguments', $class->getAvailableArguments());
            $this->writeArrayData('Options', $class->getAvailableOptions());
            $this->output->write('');
        }
    }

    private function writeCommandName(string $name)
    {
        $this->output->write('Called command:: ' . $name);
    }

    private function writeCommandDescription(string $description)
    {
        if(empty($description)) {
            return;
        }

        $this->output->write('Command description: ' . $description);
    }

    private function writeArrayData(string $name, array $options)
    {
        if(empty($options)) {
            return;
        }

        $this->output->write($name . ':');
        foreach ($options as $name => $variants)
        {
            if(!is_array($variants)) {
                $this->output->write("\t - " . $variants);
            } else {
                $this->output->write("\t - " . $name);
                foreach ($variants as $variant)
                {
                    $this->output->write("\t\t - " . $variant);
                }
            }
        }
    }

    /**
     * Инициализация потока вывода
     */
    protected function initOutput(): self
    {
        $this->output = new Output();
        return $this;
    }

    /**
     * Здесь можно регистрировать новые команды
     */
    public function register(CommandInterface $command): self
    {
        $parts = explode('\\', get_class($command));
        $name = strtolower(array_pop($parts));
        if($this->isExists($name)) {
            $this->output->write(sprintf('Command %s already registered', $name));
            return $this;
        }
        $this->commands[$name] = $command;
        return $this;
    }

    /**
     * Получение входящих данных
     *
     * @return \Console\Input\InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * Получение потока вывода
     *
     * @return \Console\Output\OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }
}
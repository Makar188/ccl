<?php

namespace Preset;

use Console\Input\InputInterface;
use Console\Output\OutputInterface;
use Library\Command;

class Out extends Command
{
    protected string $description = "Выводит введенную информацию";

    protected array $options = [
        'order', 'sort'
    ];

    protected array $arguments = [
        'select' => ['all', 'some'],
        'asIs' => ['true', 'false']
    ];

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);
    }

    public function execute(): void
    {
        $this->output->write('Input arguments: ' . implode(', ', $this->input->getArguments()) ?: ' not set');

        $optionsString = '';
        foreach($this->input->getOptions() as $name => $value)
        {
            $optionsString .= PHP_EOL . $name . '=' . (is_array($value) ? implode(', ', $value) : $value);
        }

        $this->output->write('Input options: ' . $optionsString ?: ' not set');
    }
}
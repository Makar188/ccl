<?php


namespace Console\Output;


use Exceptions\CliException;

class Output implements OutputInterface
{
    protected $stream;

    public function __construct()
    {
        $this->stream = STDOUT;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $message): void
    {
        if (false === @fwrite($this->stream, $message . PHP_EOL)) {
            throw new CliException('Unable to write output.');
        }

        fflush($this->stream);
    }
}

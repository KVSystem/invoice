<?php

namespace Proengeno\Invoice\Formatter;

use Proengeno\Invoice\Formatter\Formatter;

trait FormatableTrait
{
    private $formatter;

    public function setFormatter(Formatter $formatter = null): void
    {
        $this->formatter = $formatter;
    }

    public function format(string $method, array $attributes = []): string
    {
        if ($this->formatter === null) {
            return (string)$this->$method();
        }
        return $this->formatter->format($this, $method, $attributes);
    }

}

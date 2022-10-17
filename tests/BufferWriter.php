<?php
declare(strict_types=1);

namespace MarcinOrlowski\AsciiTableTests;

use MarcinOrlowski\AsciiTable\OutputContract;

class BufferWriter implements OutputContract
{
    /** @var string[] */
    protected array $lines = [];

    public function getBuffer(): array
    {
        return $this->lines;
    }

    public function write(array|string $text = '', string $end = PHP_EOL): void
    {
        $this->lines[] = $text;
    }

}

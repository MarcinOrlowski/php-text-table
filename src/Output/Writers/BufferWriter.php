<?php
declare(strict_types=1);

/**
 * ASCII Table
 *
 * @package   MarcinOrlowski\AsciiTable
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2022 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-ascii-table
 */

namespace MarcinOrlowski\AsciiTable\Output\Writers;

use MarcinOrlowski\AsciiTable\Output\WriterContract;

class BufferWriter implements WriterContract
{
    /** @var string[] */
    protected array $lines = [];

    public function getBuffer(): array
    {
        return $this->lines;
    }

    /**
     * @param array|string $text
     *
     * @return void
     */
    public function write(array|string $text = ''): void
    {
        $this->lines = \array_merge($this->lines, (array)$text);
    }

}

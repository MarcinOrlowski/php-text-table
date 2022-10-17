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

class EchoWriter implements WriterContract
{
    /**
     * @inheritDoc
     */
    public function write(string|array $text = ''): void
    {
        foreach ((array)$text as $line) {
            echo $line;
        }
    }

}

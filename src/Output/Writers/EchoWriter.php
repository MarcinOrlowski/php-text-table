<?php
declare(strict_types=1);

/**
 * ASCII Table
 *
 * @package   MarcinOrlowski\TextTable
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2022 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable\Output\Writers;

use MarcinOrlowski\TextTable\Output\WriterContract;

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

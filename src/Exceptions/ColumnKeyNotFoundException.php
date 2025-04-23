<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable\Exceptions;

class ColumnKeyNotFoundException extends \Exception
{
    public static function forColumnKey(\Stringable|string|int $columnKey): self
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }
        $msg = \sprintf('Column key not found: %s', $columnKey);

        return new self($msg);
    }

}

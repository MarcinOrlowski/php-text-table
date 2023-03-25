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

class UnsupportedColumnTypeException extends \Exception
{
    public static function forColumnKeyVal(string $columnKey, mixed $columnVal): static
    {
        $msg = \sprintf('Unsupported column type (%s): %s', \get_debug_type($columnVal), $columnKey);
        return self($msg);
    }
// empty
}

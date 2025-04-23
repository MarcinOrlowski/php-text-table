<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   https://opensource.org/license/mit
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable\Exceptions;

class UnsupportedColumnTypeException extends \Exception
{
    public static function forColumnKeyVal(string $columnKey, mixed $columnVal): self
    {
        $msg = \sprintf('Unsupported column type (%s): %s', \get_debug_type($columnVal), $columnKey);
        return new self($msg);
    }
// empty
}

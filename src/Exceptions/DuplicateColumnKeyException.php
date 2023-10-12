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

class DuplicateColumnKeyException extends \Exception
{
    public static function forColumnKey(\Stringable|string|int $columnKey): static
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }
        $msg = \sprintf('Duplicate column key: %s', $columnKey);
        return self($msg);
    }
}

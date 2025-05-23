<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   https://opensource.org/license/mit
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable;

interface ArrayableContract
{
    /**
     * Returns array representation of the object.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

}

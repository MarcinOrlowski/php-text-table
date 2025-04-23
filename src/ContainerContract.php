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

/**
 * Contract for container classes
 */
interface ContainerContract extends \ArrayAccess, \Countable, \IteratorAggregate, ArrayableContract
{
    // nothing special yet.
}

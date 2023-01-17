<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @package   MarcinOrlowski\TextTable
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2022 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable\Traits;

use MarcinOrlowski\TextTable\ArrayableContract;

/**
 * Implements Arrayable contract;
 */
trait ArrayableTrait
{
    /** @inheritDoc */
    public function toArray(): array
    {
        return $this->container instanceof ArrayableContract
            ? $this->container->toArray()
            : $this->container;
    }
}

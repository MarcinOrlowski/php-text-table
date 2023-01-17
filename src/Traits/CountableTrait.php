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
use Traversable;

/**
 * implements Countable contract;
 */
trait CountableTrait
{
    /** @inheritDoc */
    public function count(): int
    {
        return \count($this->container);
    }

}

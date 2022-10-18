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

namespace MarcinOrlowski\AsciiTable\Traits;

use Traversable;

/**
 * implements ContainerContract;
 */
trait IteratorAggregateTrait
{
    /** @inheritDoc */
    public function getIterator(): Traversable
    {
        return $this->container instanceof Traversable
            ? $this->container->getIterator()
            : new \ArrayIterator($this->container);
    }

    /* ****************************************************************************************** */

}

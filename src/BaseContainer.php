<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable;

use MarcinOrlowski\TextTable\Traits\CountableTrait;
use MarcinOrlowski\TextTable\Traits\IteratorAggregateTrait;

abstract class BaseContainer extends \Lombok\Helper implements ContainerContract
{
    use CountableTrait;
    use IteratorAggregateTrait;

    /* **[ ArrayableContract ]******************************************************************* */

    public function toArray(): array
    {
        return $this->container->toArray();
    }

    /* **[ ArrayAccess ]************************************************************************* */

    public function offsetExists(mixed $offset): bool
    {
        /** @var string|int $offset */
        return $this->container instanceof \ArrayAccess
            ? $this->container->offsetExists($offset)
            : \array_key_exists($offset, $this->container);
    }

    public function offsetGet(mixed $offset): mixed
    {
        /** @var string|int $offset */
        return $this->container instanceof \ArrayAccess
            ? $this->container->offsetGet($offset)
            : $this->container[$offset];
    }

    /**
     * @inheritDoc
     *
     * @throws \InvalidArgumentException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($this->container instanceof \ArrayAccess) {
            $this->container->offsetSet($offset, $value);
        } else {
            if ($offset === null) {
                $this->container[] = $value;
            } else {
                /** @var string|int $offset */
                $this->container[$offset] = $value;
            }
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        /** @var string|int $offset */
        if ($this->container instanceof \ArrayAccess) {
            $this->container->offsetUnset($offset);
        } else {
            unset($this->container[$offset]);
        }
    }
}

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

use Traversable;

abstract class BaseContainer extends \Lombok\Helper implements ContainerContract
{
    /**
     * Type of container depends on the class extending this base class.
     * This declaration here is to make linters happy.
     */
    protected mixed $container;


    /* **[ IteratorAggregate ]******************************************************************* */

    public function getIterator(): Traversable
    {
        return $this->container instanceof Traversable
            ? $this->container->getIterator()
            : new \ArrayIterator($this->container);
    }

    /* **[ Countable ]*************************************************************************** */

    public function count(): int
    {
        return \count($this->container);
    }

    /* **[ ArrayableContract ]******************************************************************* */

    public function toArray(): array
    {
        if ($this->container instanceof ArrayableContract) {
            return $this->container->toArray();
        } elseif (\is_array($this->container)) {
            return $this->container;
        } else {
            throw new \RuntimeException('Container is not arrayable');
        }
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

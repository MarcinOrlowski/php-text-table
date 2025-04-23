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

class NoVisibleColumnsException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No visible columns in table. Enable some?');
    }
}

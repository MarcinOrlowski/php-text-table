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

enum Align: string
{
    /** Automated alignment (decided at runtime; default) */
    case AUTO = 'auto';

    /** Content is aligned to left */
    case LEFT = 'left';
    /** Content is aligned to right */
    case RIGHT = 'right';
    /** Content is centered */
    case CENTER = 'center';
}

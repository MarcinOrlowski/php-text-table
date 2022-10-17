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

namespace MarcinOrlowski\AsciiTable;

enum Align: string
{
    /** Automated alignment (decied at runtime; default) */
    case AUTO = 'auto';

    /** Content is aligned to left */
    case LEFT = 'left';
    /** Content is aligned to right */
    case RIGHT = 'right';
    /** Content is centered */
    case CENTER = 'center';
}

<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   https://opensource.org/license/mit
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable\Renderers;

class FancyRenderer extends AsciiTableRenderer
{
    public const ROW_FRAME_LEFT   = '│ ';
    public const ROW_FRAME_CENTER = ' │ ';
    public const ROW_FRAME_RIGHT  = ' │';

    /* ****************************************************************************************** */

    public const SEGMENT_ROW_FILL         = '─';
    public const SEGMENT_FIRST_ROW_LEFT   = '┌─';
    public const SEGMENT_FIRST_ROW_CENTER = '─┬─';
    public const SEGMENT_FIRST_ROW_RIGHT  = '─┐';
    public const SEGMENT_ROW_LEFT         = '├─';
    public const SEGMENT_ROW_CENTER       = '─┼─';
    public const SEGMENT_ROW_RIGHT        = '─┤';
    public const SEGMENT_LAST_ROW_LEFT    = '└─';
    public const SEGMENT_LAST_ROW_CENTER  = '─┴─';
    public const SEGMENT_LAST_ROW_RIGHT   = '─┘';

}

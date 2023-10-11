<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable\Renderers;

class CompactRenderer extends AsciiTableRenderer
{
    public const RENDER_TOP_SEPARATOR    = false;
    public const RENDER_MIDDLE_SEPARATOR = true;
    public const RENDER_BOTTOM_SEPARATOR = false;

    /* ****************************************************************************************** */

    public const ROW_FRAME_LEFT   = ' ';
    public const ROW_FRAME_CENTER = '   ';
    public const ROW_FRAME_RIGHT  = ' ';

    /* ****************************************************************************************** */

    public const SEGMENT_ROW_FILL         = '-';
    public const SEGMENT_FIRST_ROW_LEFT   = '';
    public const SEGMENT_FIRST_ROW_CENTER = '';
    public const SEGMENT_FIRST_ROW_RIGHT  = '';
    public const SEGMENT_ROW_LEFT         = '-';
    public const SEGMENT_ROW_CENTER       = '- -';
    public const SEGMENT_ROW_RIGHT        = '-';
    public const SEGMENT_LAST_ROW_LEFT    = '';
    public const SEGMENT_LAST_ROW_CENTER  = '';
    public const SEGMENT_LAST_ROW_RIGHT   = '';

}

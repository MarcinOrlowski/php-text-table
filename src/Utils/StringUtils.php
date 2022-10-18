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

namespace MarcinOrlowski\AsciiTable\Utils;

class StringUtils
{
    /**
     * Multibyte aware implementation of str_pad()
     *
     * @param string $str
     * @param int    $len
     * @param string $pad
     * @param int    $align
     *
     * @return string
     */
    public static function pad(string $str, int $len, string $pad = ' ', int $align = \STR_PAD_RIGHT): string
    {
        $strLen = \mb_strlen($str);
        if ($strLen >= $len) {
            return $str;
        }

        $diff = $len - $strLen;
        $padding = \mb_substr(\str_repeat($pad, $diff), 0, $diff);

        switch ($align) {
            case \STR_PAD_BOTH:
                $diffHalf = (int)($diff / 2 + 0.5);
                $padding = \str_repeat($pad, $diffHalf);
                $result = "{$padding}{$str}{$padding}";
                break;
            case \STR_PAD_LEFT:
                $result = "{$padding}{$str}";
                break;
            case \STR_PAD_RIGHT:
            default:
                $result = "{$str}{$padding}";
                break;
        }

        return \mb_substr($result, 0, $len);
    }

    /* ****************************************************************************************** */
}

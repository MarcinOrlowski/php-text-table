<?php
declare(strict_types=1);

/**
 * ASCII Table
 *
 * @package   MarcinOrlowski\TextTable
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2022 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable\Utils;

class StringUtils
{
    /**
     * Multibyte aware implementation of str_pad()
     *
     * @param string $input   The input string
     * @param int    $len     If the value of pad_length is negative, less than, or equal to the length of the
     *                        input string, no padding takes place.
     * @param string $pad     The pad_string may be truncated if the required number of padding characters
     *                        can't be evenly divided by the pad string's length.
     * @param int    $padType Optional argument pad_type can be STR_PAD_RIGHT, STR_PAD_LEFT or STR_PAD_BOTH.
     *                        If padType is not specified it is assumed to be STR_PAD_RIGHT.
     *
     * @return string
     */
    public static function pad(string $input, int $len, string $pad = ' ',
                               int    $padType = \STR_PAD_RIGHT): string
    {
        $strLen = \mb_strlen($input);
        if ($len <= 0 || $strLen >= $len) {
            return $input;
        }

        $diff = $len - $strLen;
        $padding = \mb_substr(\str_repeat($pad, $diff), 0, $diff);

        switch ($padType) {
            case \STR_PAD_BOTH:
                $diffHalf = (int)($diff / 2 + 0.5);
                $padding = \str_repeat($pad, $diffHalf);
                $result = "{$padding}{$input}{$padding}";
                break;
            case \STR_PAD_LEFT:
                $result = "{$padding}{$input}";
                break;
            case \STR_PAD_RIGHT:
            default:
                $result = "{$input}{$padding}";
                break;
        }

        return \mb_substr($result, 0, $len);
    }

    /* ****************************************************************************************** */
}

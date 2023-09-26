<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable\Utils;

class StringUtils
{
    /**
     * Multibyte aware implementation of str_pad()
     *
     * @param string $input     The input string
     * @param int    $padLength If the value of pad_length is negative, less than, or equal to the
     *                          length of the input string, no padding takes place.
     * @param string $padString The pad_string may be truncated if the required number of padding
     *                          characters can't be evenly divided by the pad string's length.
     * @param int    $padType   Optional argument pad_type can be STR_PAD_RIGHT, STR_PAD_LEFT or
     *                          STR_PAD_BOTH. If padType is not specified it is assumed to be
     *                          STR_PAD_RIGHT.
     *
     * @return string
     */
    public static function pad(string $input, int $padLength, string $padString = ' ',
                               int    $padType = \STR_PAD_RIGHT): string
    {
        $strLen = \mb_strlen($input);
        if ($padLength <= 0 || $strLen >= $padLength) {
            return $input;
        }

        $totalPadding = $padLength - $strLen;

        switch ($padType) {
            case \STR_PAD_BOTH:
                $diffLeft = (int)($totalPadding / 2);
                $diffRight = $totalPadding - $diffLeft;
                $paddingLeft = self::generatePadding($padString, $diffLeft);
                $paddingRight = self::generatePadding($padString, $diffRight);
                $result = "{$paddingLeft}{$input}{$paddingRight}";
                break;
            case \STR_PAD_LEFT:
                $padding = self::generatePadding($padString, $totalPadding);
                $result = "{$padding}{$input}";
                break;
            case \STR_PAD_RIGHT:
            default:
                $padding = self::generatePadding($padString, $totalPadding);
                $result = "{$input}{$padding}";
                break;
        }

        return \mb_substr($result, 0, $padLength);
    }

    protected static function generatePadding(string $padString, int $length): string
    {
        return \mb_substr(\str_repeat($padString, $length), 0, $length);
    }

    /* ****************************************************************************************** */
}

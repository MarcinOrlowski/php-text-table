<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace MarcinOrlowski\AsciiTableTests;

use MarcinOrlowski\AsciiTable\Utils\StringUtils;
use MarcinOrlowski\PhpunitExtraAsserts\Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class StringUtilsTest extends TestCase
{
    public function testPaddingDefaults(): void
    {
        $strLen = Generator::getRandomInt(10, 30);
        $str = Generator::getRandomString(length: $strLen);
        $extraLen = Generator::getRandomInt(10, 20);

        $padded = StringUtils::pad($str, $strLen + $extraLen);
        Assert::assertEquals($strLen + $extraLen, \mb_strlen($padded));
    }

    public function testPaddingLeft(): void
    {
        $strLen = Generator::getRandomInt(10, 30);
        $str = Generator::getRandomString(length: $strLen);
        $padLen = +Generator::getRandomInt(10, 20);
        $maxLen = $strLen + $padLen;

        $padded = StringUtils::pad($str, $maxLen, ' ', \STR_PAD_LEFT);
        Assert::assertEquals($maxLen, \mb_strlen($padded));

        $expected = \str_repeat(' ', $padLen) . $str;
        Assert::assertEquals($expected, $padded);
    }

    public function testPaddingRight(): void
    {
        $strLen = Generator::getRandomInt(10, 30);
        $str = Generator::getRandomString(length: $strLen);
        $padLen = +Generator::getRandomInt(10, 20);
        $maxLen = $strLen + $padLen;

        $padded = StringUtils::pad($str, $maxLen, ' ', \STR_PAD_RIGHT);
        Assert::assertEquals($maxLen, \mb_strlen($padded));

        $expected = $str . \str_repeat(' ', $padLen);
        Assert::assertEquals($expected, $padded);
    }

    public function testPaddingBoth(): void
    {
        $strLen = Generator::getRandomInt(10, 30);
        $str = Generator::getRandomString(length: $strLen);
        $padLen = Generator::getRandomInt(10, 20);
        $maxLen = $strLen + $padLen;

        $padLenHalf = (int)(($padLen / 2) + 0.5);

        $padded = StringUtils::pad($str, $maxLen, ' ', \STR_PAD_BOTH);
        Assert::assertEquals($maxLen, \mb_strlen($padded));

        $pad = \str_repeat(' ', $padLenHalf);
        $expected = $pad . $str . $pad;
        $expected = \mb_substr($expected, 0, $maxLen);
        Assert::assertEquals($expected, $padded);
    }

    /**
     * Tests if padding too long string is handled properly.
     */
    public function testPaddingTooLongString(): void
    {
        $strLen = Generator::getRandomInt(10, 30);
        $str = Generator::getRandomString(length: $strLen);
        $maxLen = (int)($strLen / 2);

        $padded = StringUtils::pad($str, $maxLen);
        Assert::assertEquals($strLen, \mb_strlen($padded));
    }
}


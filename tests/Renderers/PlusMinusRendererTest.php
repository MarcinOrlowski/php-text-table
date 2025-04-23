<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   https://opensource.org/license/mit
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTableTests\Renderers;

use MarcinOrlowski\TextTable\Renderers\PlusMinusRenderer;
use MarcinOrlowski\TextTable\Renderers\RendererContract;

/**
 * Tests for PlusMinusRenderer.
 */
class PlusMinusRendererTest extends BaseRendererTestCase
{
    protected function getRenderer(): RendererContract
    {
        return new PlusMinusRenderer();
    }

    public static function basicRenderDataProvider(): iterable
    {
        $table = self::createBasicTable();
        $expected = <<<OUTPUT
        +----+-------+-------------------+
        | ID | Name  | Email             |
        +----+-------+-------------------+
        | 1  | Alice | alice@example.com |
        | 2  | Bob   | bob@example.com   |
        +----+-------+-------------------+
        OUTPUT;
        yield 'basic table' => [$table, $expected];
    }

    public static function alignmentDataProvider(): iterable
    {
        $table = self::createAlignmentTable();
        $expected = <<<OUTPUT
+------------+--------------+-------------+
| Left Align | Center Align | Right Align |
+------------+--------------+-------------+
| abc        |     def      |         ghi |
| jklmno     |    pqrstu    |       vwxyz |
+------------+--------------+-------------+
OUTPUT;
        yield 'alignment' => [$table, $expected];
    }

    public static function hiddenColumnsDataProvider(): iterable
    {
        $table = self::createHiddenColumnsTable();
        $expected = <<<OUTPUT
        +-----------+-----------+
        | Visible 1 | Visible 2 |
        +-----------+-----------+
        | v1a       | v2a       |
        | v1b       | v2b       |
        +-----------+-----------+
        OUTPUT;
        yield 'hidden columns' => [$table, $expected];
    }

    public static function separatorDataProvider(): iterable
    {
        $table = self::createSeparatorTable();
        $expected = <<<OUTPUT
        +----+---------+
        | ID | Name    |
        +----+---------+
        | 1  | Alice   |
        +----+---------+
        | 2  | Bob     |
        +----+---------+
        | 3  | Charlie |
        +----+---------+
        OUTPUT;
        yield 'separators' => [$table, $expected];
    }

    public static function emptyTableDataProvider(): iterable
    {
        $table = self::createEmptyTable();
        $expected = <<<OUTPUT
+----+------+
| ID | Name |
+----+------+
|  NO DATA  |
+----+------+
OUTPUT;
        yield 'empty table' => [$table, $expected];
    }

    public static function onlyHeadersDataProvider(): iterable
    {
        $table = self::createOnlyHeadersTable();
        $expected = <<<OUTPUT
+----+------+
| ID | Name |
+----+------+
|  NO DATA  |
+----+------+
OUTPUT;
        // Note: PlusMinusRenderer shows header/footer even with no rows
        yield 'only headers' => [$table, $expected];
    }

    public static function onlyDataNoHeadersDataProvider(): iterable
    {
        $table = self::createOnlyDataNoHeadersTable();
        $expected = <<<OUTPUT
+----+-------+
| 1  | Alice |
| 2  | Bob   |
+----+-------+
OUTPUT;
        yield 'only data no headers' => [$table, $expected];
    }

    public static function nullValuesDataProvider(): iterable
    {
        $table = self::createNullValuesTable();
        $expected = <<<OUTPUT
+----+---------+----------+
| ID | Name    | Optional |
+----+---------+----------+
| 1  | Alice   | Present  |
| 2  | Bob     | NULL     |
| 3  | Charlie |          |
+----+---------+----------+
OUTPUT;
        yield 'null values' => [$table, $expected];
    }
}

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

use MarcinOrlowski\TextTable\Align;
use MarcinOrlowski\TextTable\Column;
use MarcinOrlowski\TextTable\Renderers\RendererContract;
use MarcinOrlowski\TextTable\TextTable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Base test case for renderers.
 */
abstract class BaseRendererTestCase extends TestCase
{
    /**
     * Provides the renderer instance to be tested.
     */
    abstract protected function getRenderer(): RendererContract;

    /**
     * Data provider for basic rendering tests.
     *
     * @return iterable<string, array{0: TextTable, 1: string}>
     */
    abstract public static function basicRenderDataProvider(): iterable;

    /**
     * Data provider for alignment tests.
     *
     * @return iterable<string, array{0: TextTable, 1: string}>
     */
    abstract public static function alignmentDataProvider(): iterable;

    /**
     * Data provider for hidden columns tests.
     *
     * @return iterable<string, array{0: TextTable, 1: string}>
     */
    abstract public static function hiddenColumnsDataProvider(): iterable;

    /**
     * Data provider for multi-byte character tests.
     *
     * @return iterable<string, array{0: TextTable, 1: string}>
     */
    public static function multiByteDataProvider(): iterable
    {
        // Default implementation for renderers that don't have specific multi-byte issues
        return [];
    }

    /**
     * Data provider for separator tests.
     *
     * @return iterable<string, array{0: TextTable, 1: string}>
     */
    abstract public static function separatorDataProvider(): iterable;

    /**
     * Data provider for empty table tests.
     *
     * @return iterable<string, array{0: TextTable, 1: string}>
     */
    abstract public static function emptyTableDataProvider(): iterable;

    /**
     * Data provider for tables with only headers.
     *
     * @return iterable<string, array{0: TextTable, 1: string}>
     */
    abstract public static function onlyHeadersDataProvider(): iterable;

    /**
     * Data provider for tables with only data (no headers).
     *
     * @return iterable<string, array{0: TextTable, 1: string}>
     */
    abstract public static function onlyDataNoHeadersDataProvider(): iterable;

    /**
     * Data provider for tables with null values.
     *
     * @return iterable<string, array{0: TextTable, 1: string}>
     */
    abstract public static function nullValuesDataProvider(): iterable;

    #[DataProvider('basicRenderDataProvider')]
    #[DataProvider('alignmentDataProvider')]
    #[DataProvider('hiddenColumnsDataProvider')]
    #[DataProvider('multiByteDataProvider')]
    #[DataProvider('separatorDataProvider')]
    #[DataProvider('emptyTableDataProvider')]
    #[DataProvider('onlyHeadersDataProvider')]
    #[DataProvider('onlyDataNoHeadersDataProvider')]
    #[DataProvider('nullValuesDataProvider')]
    public function testRender(TextTable $table, string $expectedOutput): void
    {
        $renderer = $this->getRenderer();
        $actualOutputLines = $renderer->render($table);
        $expectedOutputLines = \explode(\PHP_EOL, $expectedOutput);

        $this->assertSame($expectedOutputLines, $actualOutputLines);
    }

    // Helper methods to create common table configurations

    protected static function createBasicTable(): TextTable
    {
        $table = new TextTable();
        $table->addColumn('id', 'ID');
        $table->addColumn('name', 'Name');
        $table->addColumn('email', 'Email');
        $table->addRow(['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com']);
        $table->addRow(['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com']);
        return $table;
    }

    protected static function createAlignmentTable(): TextTable
    {
        $table = new TextTable();
        $table->addColumn('left', new Column('Left Align', align: Align::LEFT));
        $table->addColumn('center', new Column('Center Align', align: Align::CENTER));
        $table->addColumn('right', new Column('Right Align', align: Align::RIGHT));
        $table->addRow(['left' => 'abc', 'center' => 'def', 'right' => 'ghi']);
        $table->addRow(['left' => 'jklmno', 'center' => 'pqrstu', 'right' => 'vwxyz']);
        return $table;
    }

    protected static function createHiddenColumnsTable(): TextTable
    {
        $table = new TextTable();
        $table->addColumn('visible1', 'Visible 1');
        $table->addColumn('hidden', new Column('Hidden', visible: false));
        $table->addColumn('visible2', 'Visible 2');
        $table->addRow(['visible1' => 'v1a', 'hidden' => 'h1', 'visible2' => 'v2a']);
        $table->addRow(['visible1' => 'v1b', 'hidden' => 'h2', 'visible2' => 'v2b']);
        return $table;
    }

    protected static function createMultiByteTable(): TextTable
    {
        $table = new TextTable();
        $table->addColumn('col1', '項目1');         // Item 1
        $table->addColumn('col2', '項目2');         // Item 2
        $table->addRow(['col1' => '値A', 'col2' => '値B']);     // Value A, Value B
        $table->addRow(['col1' => '長い値C', 'col2' => '値D']); // Long Value C, Value D
        return $table;
    }

    protected static function createSeparatorTable(): TextTable
    {
        $table = new TextTable();
        $table->addColumn('id', 'ID');
        $table->addColumn('name', 'Name');
        $table->addRow(['id' => 1, 'name' => 'Alice']);
        $table->addSeparator();
        $table->addRow(['id' => 2, 'name' => 'Bob']);
        $table->addSeparator();
        $table->addRow(['id' => 3, 'name' => 'Charlie']);
        return $table;
    }

    protected static function createEmptyTable(): TextTable
    {
        $table = new TextTable();
        $table->addColumn('id', 'ID');
        $table->addColumn('name', 'Name');
        return $table;
    }

    protected static function createOnlyHeadersTable(): TextTable
    {
        $table = new TextTable();
        $table->addColumn('id', 'ID');
        $table->addColumn('name', 'Name');
        // No rows added
        return $table;
    }

    protected static function createOnlyDataNoHeadersTable(): TextTable
    {
        $table = new TextTable();
        $table->hideHeader();
        $table->addColumn('id', 'ID'); // Headers defined but not shown
        $table->addColumn('name', 'Name');
        $table->addRow(['id' => 1, 'name' => 'Alice']);
        $table->addRow(['id' => 2, 'name' => 'Bob']);
        return $table;
    }

    protected static function createNullValuesTable(): TextTable
    {
        $table = new TextTable();
        $table->addColumn('id', 'ID');
        $table->addColumn('name', 'Name');
        $table->addColumn('optional', 'Optional');
        $table->addRow(['id' => 1, 'name' => 'Alice', 'optional' => 'Present']);
        $table->addRow(['id' => 2, 'name' => 'Bob', 'optional' => null]);
        $table->addRow(['id' => 3, 'name' => 'Charlie']); // Implicit null
        return $table;
    }
}

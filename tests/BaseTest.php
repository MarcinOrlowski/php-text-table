<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace MarcinOrlowski\TextTableTests;

use MarcinOrlowski\PhpunitExtraAsserts\Generator;
use MarcinOrlowski\TextTable\Align;
use MarcinOrlowski\TextTable\Cell;
use MarcinOrlowski\TextTable\Column;
use MarcinOrlowski\TextTable\Exceptions\NoVisibleColumnsException;
use MarcinOrlowski\TextTable\Renderers\PlusMinusRenderer;
use MarcinOrlowski\TextTable\TextTable;
use MarcinOrlowski\TextTable\Utils\StringUtils;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/** @noinspection PhpUnhandledExceptionInspection */

class BaseTest extends TestCase
{
    protected function renderTable(TextTable $table, bool $echoTable = false): array
    {
        $renderer = new PlusMinusRenderer();
        $rendered = $renderer->render($table);
        if ($echoTable) {
            echo \implode(\PHP_EOL, $rendered) . \PHP_EOL;
        }
        return $rendered;
    }

    public function testSimpleTable(): void
    {
        $table = new TextTable();
        $table->addColumns([
            'A',
            'B',
            'C',
        ]);
        $table->addRow([
            'A' => 'a',
            'B' => 'b',
            'C' => 'c',
        ]);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+---+---+---+',
            '| A | B | C |',
            '+---+---+---+',
            '| a | b | c |',
            '+---+---+---+',
        ];

        Assert::assertEquals($expected, $renderedTable);
    }

    /* ****************************************************************************************** */

    public function testMultiRowTable(): void
    {
        $table = new TextTable();
        $table->addColumns([
            'A',
            'B',
            'C',
        ]);

        $rowCnt = Generator::getRandomInt(2, 10);

        $expected = [
            '+---+---+---+',
            '| A | B | C |',
            '+---+---+---+',
        ];

        for ($i = 0; $i < $rowCnt; $i++) {
            $table->addRow([
                'A' => "a",
                'B' => "b",
                'C' => "c",
            ]);
            $expected[] = '| a | b | c |';
        }
        $expected[] = '+---+---+---+';

        $renderedTable = $this->renderTable($table);

        Assert::assertEquals($expected, $renderedTable);
    }

    /* ****************************************************************************************** */

    public function testCustomColumnKeys(): void
    {
        $key1 = Generator::getRandomString('key1');
        $key2 = Generator::getRandomString('key2');
        $key3 = Generator::getRandomString('key3');
        $table = new TextTable();
        $table->addColumns([
            $key1 => 'A',
            $key2 => 'B',
            $key3 => 'C',
        ]);
        $table->addRow([
            $key3 => 'c',
            $key2 => 'b',
            $key1 => 'a',
        ]);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+---+---+---+',
            '| A | B | C |',
            '+---+---+---+',
            '| a | b | c |',
            '+---+---+---+',
        ];

        Assert::assertEquals($expected, $renderedTable);
    }

    /* ****************************************************************************************** */

    public function testCustomIndex(): void
    {
        $table = new TextTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12, 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);
        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----+-------+-------+',
            '| ID | NAME  | SCORE |',
            '+----+-------+-------+',
            '| 1  | John  | 12    |',
            '| 2  | Tommy | 15    |',
            '+----+-------+-------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    /* ****************************************************************************************** */

    public function testRowCellsAutoAssign(): void
    {
        $table = new TextTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            [1, 'John', 12],
        ]);
        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----+------+-------+',
            '| ID | NAME | SCORE |',
            '+----+------+-------+',
            '| 1  | John | 12    |',
            '+----+------+-------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    /* ****************************************************************************************** */

    public function testTableColumnAutoKey(): void
    {
        $table = new TextTable(['ID', new Column('SCORE')]);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12],
        ]);
        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----+-------+',
            '| ID | SCORE |',
            '+----+-------+',
            '| 1  | 12    |',
            '+----+-------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    /* ****************************************************************************************** */

    public function testColumnAlign(): void
    {
        $table = new TextTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12, 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);

        $table->setColumnAlign('ID', Align::RIGHT);
        $table->setColumnAlign('SCORE', Align::RIGHT);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----+-------+-------+',
            '| ID | NAME  | SCORE |',
            '+----+-------+-------+',
            '|  1 | John  |    12 |',
            '|  2 | Tommy |    15 |',
            '+----+-------+-------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testCellColumnAlign(): void
    {
        $table = new TextTable(['ID',
                                new Column('NAME', maxWidth: 20),
                                'SCORE',
        ]);
        $table->addRows([
            [1,
             new Cell('John', Align::CENTER),
             12,
            ],
        ]);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----+----------------------+-------+',
            '| ID | NAME                 | SCORE |',
            '+----+----------------------+-------+',
            '| 1  |         John         | 12    |',
            '+----+----------------------+-------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    /* ****************************************************************************************** */

    public function testCustomWidth(): void
    {
        $table = new TextTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12, 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);

        $table->setColumnMaxWidth('ID', 20);
        $table->setColumnMaxWidth('NAME', 5);
        $table->setColumnMaxWidth('SCORE', 25);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----------------------+-------+---------------------------+',
            '| ID                   | NAME  | SCORE                     |',
            '+----------------------+-------+---------------------------+',
            '| 1                    | John  | 12                        |',
            '| 2                    | Tommy | 15                        |',
            '+----------------------+-------+---------------------------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testCustomWidthAndUtf(): void
    {
        $table = new TextTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            [1, 'Foo', 12],
            [2, 'PBOX POH Po???????? (Restaura)', 15],
        ]);

        $table->setColumnMaxWidth('ID', 10);
        $table->setColumnMaxWidth('NAME', 15);
        $table->setColumnMaxWidth('SCORE', 4);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+------------+-----------------+------+',
            '| ID         | NAME            | SCO??? |',
            '+------------+-----------------+------+',
            '| 1          | Foo             | 12   |',
            '| 2          | PBOX POH Po????????? | 15   |',
            '+------------+-----------------+------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testCustomWidthAndUtfMulticolumn(): void
    {
        // Create display table
        $table = new TextTable([
            new Column('NAME', maxWidth: 25),
        ]);

        $table->addRows([
            ['????d??c?? depo Praha 704'],
            ['O??n?? optika M. E??erov??'],
            ['AKY chovatelsk?? pot??eby a krmiva'],
        ]);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----------------------------------+',
            '| NAME                             |',
            '+----------------------------------+',
            '| ????d??c?? depo Praha 704            |',
            '| O??n?? optika M. E??erov??           |',
            '| AKY chovatelsk?? pot??eby a krmiva |',
            '+----------------------------------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testCustomWidthAndAlign(): void
    {
        $table = new TextTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12, 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);

        $table->setColumnMaxWidth('ID', 20);
        $table->setColumnMaxWidth('NAME', 5);
        $table->setColumnMaxWidth('SCORE', 25);

        $table->setColumnAlign('ID', Align::RIGHT);
        $table->setCellAlign('NAME', Align::CENTER);
        $table->getColumn('SCORE')
            ->setCellAlign(Align::RIGHT)
            ->setTitleAlign(Align::CENTER);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----------------------+-------+---------------------------+',
            '|                   ID | NAME  |           SCORE           |',
            '+----------------------+-------+---------------------------+',
            '|                    1 |  John |                        12 |',
            '|                    2 | Tommy |                        15 |',
            '+----------------------+-------+---------------------------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testCustomWidthAndMixedAlign(): void
    {
        $table = new TextTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => new Cell(12, Align::CENTER), 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);

        $table->setColumnMaxWidth('ID', 10);
        $table->setColumnMaxWidth('NAME', 5);
        $table->setColumnMaxWidth('SCORE', 25);

        $table->setColumnAlign('ID', Align::RIGHT);
        $table->setCellAlign('NAME', Align::CENTER);
        $table->getColumn('SCORE')
            ->setCellAlign(Align::RIGHT)
            ->setTitleAlign(Align::CENTER);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+------------+-------+---------------------------+',
            '|         ID | NAME  |           SCORE           |',
            '+------------+-------+---------------------------+',
            '|          1 |  John |             12            |',
            '|          2 | Tommy |                        15 |',
            '+------------+-------+---------------------------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    /**
     * Tests it too long values are correctly truncated.
     */
    public function testCustomWidthClipping(): void
    {
        $maxLength = Generator::getRandomInt(10, 20);
        $longName = Generator::getRandomString('name', $maxLength * 2);

        $clipped = \mb_substr($longName, 0, $maxLength - 1) . '???';

        $key = 'NAME';

        $table = new TextTable([$key]);
        $table->addRows([
            [
                $key => $longName,
            ],
        ]);

        $table->setColumnMaxWidth('NAME', $maxLength);

        $renderedTable = $this->renderTable($table);

        $expected = [
            \sprintf('+-%s-+', StringUtils::pad('', $maxLength, '-')),
            \sprintf('| %s%s |', $key, \str_repeat(' ', $maxLength - \mb_strlen($key))),
            \sprintf('+-%s-+', StringUtils::pad('', $maxLength, '-')),
            "| {$clipped} |",
            \sprintf('+-%s-+', StringUtils::pad('', $maxLength, '-')),
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    /* ****************************************************************************************** */

    public function testNoData(): void
    {
        $table = new TextTable(['ID', new Column('NAME', maxWidth: 20), 'SCORE']);
        $table->setColumnAlign('SCORE', Align::RIGHT);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----+----------------------+-------+',
            '| ID | NAME                 | SCORE |',
            '+----+----------------------+-------+',
            '|              NO DATA              |',
            '+----+----------------------+-------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testNoDataWithClipping(): void
    {
        $maxLength = Generator::getRandomInt(10, 20);
        $key = Generator::getRandomString('name', $maxLength * 2);

        $table = new TextTable([$key]);
        $table->setColumnMaxWidth($key, $maxLength);

        $renderedTable = $this->renderTable($table);

        $clippedTitle = \mb_substr($key, 0, $maxLength - 1) . '???';
        $tableSeparatorLine = \sprintf('+-%s-+', \str_repeat('-', $maxLength));
        $expected = [
            $tableSeparatorLine,
            \sprintf('| %s |', $clippedTitle),
            $tableSeparatorLine,
            \sprintf('| %s |', $this->formatNoData($maxLength)),
            $tableSeparatorLine,
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testNoDataWithHiddenFirstColumn(): void
    {
        $table = new TextTable([
            'ID',
            'HIDDEN',
            new Column('NAME', maxWidth: 20),
            'SCORE',
        ]);
        $table->hideColumn('ID');

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+--------+----------------------+-------+',
            '| HIDDEN | NAME                 | SCORE |',
            '+--------+----------------------+-------+',
            '|                NO DATA                |',
            '+--------+----------------------+-------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testNoDataWithHiddenMiddleColumn(): void
    {
        $table = new TextTable([
            'ID',
            'HIDDEN',
            new Column('NAME', maxWidth: 20),
            'SCORE',
        ]);
        $table->hideColumn('HIDDEN');

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----+----------------------+-------+',
            '| ID | NAME                 | SCORE |',
            '+----+----------------------+-------+',
            '|              NO DATA              |',
            '+----+----------------------+-------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testNoDataWithHiddenLastColumn(): void
    {
        $table = new TextTable([
            'ID',
            'HIDDEN',
            new Column('NAME', maxWidth: 20),
            'SCORE',
        ]);
        $table->hideColumn('SCORE');

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----+--------+----------------------+',
            '| ID | HIDDEN | NAME                 |',
            '+----+--------+----------------------+',
            '|               NO DATA              |',
            '+----+--------+----------------------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testNoDataWithHiddenAllColumns(): void
    {
        $table = new TextTable([
            'ID',
            'HIDDEN',
            'SCORE',
        ]);
        $table->hideColumn([
            'ID',
            'HIDDEN',
            'SCORE',
        ]);

        $this->expectException(NoVisibleColumnsException::class);
        $renderedTable = $this->renderTable($table);
    }


    protected function formatNoData(int $maxLength): string
    {
        $noDataLabel = 'NO DATA';
        if (\mb_strlen($noDataLabel) > $maxLength) {
            $noDataLabel = \mb_substr($noDataLabel, 0, $maxLength - 1) . '???';
        } else {
            $noDataLabel = StringUtils::pad($noDataLabel, $maxLength, ' ', \STR_PAD_BOTH);
        }

        return $noDataLabel;
    }

    /* ****************************************************************************************** */

    public function testHidden(): void
    {
        $key1 = Generator::getRandomString('key1');
        $key2 = Generator::getRandomString('key2');
        $key3 = Generator::getRandomString('key3');
        $table = new TextTable();
        $table->addColumns([
            $key1 => 'A',
            $key2 => 'B',
            $key3 => 'C',
        ]);
        $table->addRow([
            $key3 => 'c',
            $key2 => 'b',
            $key1 => 'a',
        ]);

        $table->hideColumn($key2);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+---+---+',
            '| A | C |',
            '+---+---+',
            '| a | c |',
            '+---+---+',
        ];

        Assert::assertEquals($expected, $renderedTable);
    }


    public function testNoVisibleColumn(): void
    {
        $columns = ['ID', 'NAME', 'SCORE'];
        $table = new TextTable($columns);

        foreach ($columns as $column) {
            $table->hideColumn($column);
        }

        $this->expectException(NoVisibleColumnsException::class);
        $table->render();
    }

    public function testHiddingFirstColumn(): void
    {
        $key1 = Generator::getRandomString('key1');
        $key2 = Generator::getRandomString('key2');
        $key3 = Generator::getRandomString('key3');
        $table = new TextTable();
        $table->addColumns([
            $key1 => 'A',
            $key2 => 'B',
            $key3 => 'C',
        ]);
        $table->addRow([
            $key3 => 'c',
            $key2 => 'b',
            $key1 => 'a',
        ]);

        $table->hideColumn($key1);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+---+---+',
            '| B | C |',
            '+---+---+',
            '| b | c |',
            '+---+---+',
        ];

        Assert::assertEquals($expected, $renderedTable);
    }

    public function testHiddingLastColumn(): void
    {
        $key1 = Generator::getRandomString('key1');
        $key2 = Generator::getRandomString('key2');
        $key3 = Generator::getRandomString('key3');
        $table = new TextTable();
        $table->addColumns([
            $key1 => 'A',
            $key2 => 'B',
            $key3 => 'C',
        ]);
        $table->addRow([
            $key3 => 'c',
            $key2 => 'b',
            $key1 => 'a',
        ]);

        $table->hideColumn($key3);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+---+---+',
            '| A | B |',
            '+---+---+',
            '| a | b |',
            '+---+---+',
        ];

        Assert::assertEquals($expected, $renderedTable);
    }

    /* ****************************************************************************************** */

    /**
     * Tests if missing column values in rows are correctly handled.
     */
    public function testMissingRowCellsMulticolumn(): void
    {
        $table = new TextTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['SCORE' => 15,],
            [1, 'John', 12],
            ['ID' => 2, 'NAME' => 'Alan'],
            ['SCORE' => 32, 'NAME' => 'Robert'],
        ]);

        $table->setColumnAlign('ID', Align::RIGHT);
        $table->setColumnAlign('SCORE', Align::RIGHT);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----+--------+-------+',
            '| ID | NAME   | SCORE |',
            '+----+--------+-------+',
            '|    |        |    15 |',
            '|  1 | John   |    12 |',
            '|  2 | Alan   |       |',
            '|    | Robert |    32 |',
            '+----+--------+-------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testMissingRowCells(): void
    {
        $table = new TextTable(['ID', 'NAME']);
        $table->addRows([
            [1],
            ['NAME' => 'John'],
        ]);

        $renderedTable = $this->renderTable($table);

        $expected = [
            '+----+------+',
            '| ID | NAME |',
            '+----+------+',
            '| 1  |      |',
            '|    | John |',
            '+----+------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

}


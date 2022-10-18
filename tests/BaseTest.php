<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace MarcinOrlowski\AsciiTableTests;

use MarcinOrlowski\AsciiTable\Align;
use MarcinOrlowski\AsciiTable\AsciiTable;
use MarcinOrlowski\AsciiTable\Cell;
use MarcinOrlowski\AsciiTable\Column;
use MarcinOrlowski\AsciiTable\Output\Writers\BufferWriter;
use MarcinOrlowski\AsciiTable\Utils\StringUtils;
use MarcinOrlowski\PhpunitExtraAsserts\Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/** @noinspection PhpUnhandledExceptionInspection */

class BaseTest extends TestCase
{
    protected function render(AsciiTable $table): array
    {
        $bufferWriter = new BufferWriter();
        $table->render($bufferWriter);
        $renderedTable = $bufferWriter->getBuffer();

        // Strip trailing PHP_EOL to make comparing results
        // in tests easier.
        return \array_map(static function(string $line) {
            if (\mb_substr($line, -1) === PHP_EOL) {
                $line = \mb_substr($line, 0, -1);
            }
            return $line;
        }, $renderedTable);
    }

    /* ****************************************************************************************** */

    public function testSimpleTable(): void
    {
        $table = new AsciiTable();
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

        $renderedTable = $this->render($table);

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
        $table = new AsciiTable();
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

        $renderedTable = $this->render($table);

        Assert::assertEquals($expected, $renderedTable);
    }

    /* ****************************************************************************************** */

    public function testCustomColumnKeys(): void
    {
        $key1 = Generator::getRandomString('key1');
        $key2 = Generator::getRandomString('key2');
        $key3 = Generator::getRandomString('key3');
        $table = new AsciiTable();
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

        $renderedTable = $this->render($table);

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
        $table = new AsciiTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12, 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);
        $renderedTable = $this->render($table);

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
        $table = new AsciiTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            [1, 'John', 12],
        ]);
        $renderedTable = $this->render($table);

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
        $table = new AsciiTable(['ID', new Column('SCORE')]);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12],
        ]);
        $renderedTable = $this->render($table);

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
        $table = new AsciiTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12, 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);

        $table->setDefaultColumnAlign('ID', Align::RIGHT);
        $table->setDefaultColumnAlign('SCORE', Align::RIGHT);

        $renderedTable = $this->render($table);

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
        $table = new AsciiTable(['ID',
                                 new Column('NAME', maxWidth: 20),
                                 'SCORE',
        ]);
        $table->addRows([
            [1,
             new Cell('John', Align::CENTER),
             12,
            ],
        ]);

        $renderedTable = $this->render($table);

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
        $table = new AsciiTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12, 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);

        $table->setColumnMaxWidth('ID', 20);
        $table->setColumnMaxWidth('NAME', 5);
        $table->setColumnMaxWidth('SCORE', 25);

        $renderedTable = $this->render($table);

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
        $table = new AsciiTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            [1, 'Foo', 12],
            [2, 'PBOX POH Poříčí (Restaura)', 15],
        ]);

        $table->setColumnMaxWidth('ID', 10);
        $table->setColumnMaxWidth('NAME', 15);
        $table->setColumnMaxWidth('SCORE', 4);

        $renderedTable = $this->render($table);

        $expected = [
            '+------------+-----------------+------+',
            '| ID         | NAME            | SCO… |',
            '+------------+-----------------+------+',
            '| 1          | Foo             | 12   |',
            '| 2          | PBOX POH Poříč… | 15   |',
            '+------------+-----------------+------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testCustomWidthAndUtfMulticolumn(): void
    {
        // Create display table
        $table = new AsciiTable([
            new Column('NAME', maxWidth: 25),
        ]);

        $table->addRows([
            ['Řídící depo Praha 704'],
            ['Oční optika M. Ečerová'],
            ['AKY chovatelské potřeby a krmiva'],
        ]);

        $renderedTable = $this->render($table);
        $expected = [
            '+----------------------------------+',
            '| NAME                             |',
            '+----------------------------------+',
            '| Řídící depo Praha 704            |',
            '| Oční optika M. Ečerová           |',
            '| AKY chovatelské potřeby a krmiva |',
            '+----------------------------------+',
        ];
        Assert::assertEquals($expected, $renderedTable);
    }


    public function testCustomWidthAndAlign(): void
    {
        $table = new AsciiTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12, 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);

        $table->setColumnMaxWidth('ID', 20);
        $table->setColumnMaxWidth('NAME', 5);
        $table->setColumnMaxWidth('SCORE', 25);

        $table->setDefaultColumnAlign('ID', Align::RIGHT);
        $table->setDefaultColumnAlign('NAME', Align::CENTER);
        $table->setDefaultColumnAlign('SCORE', Align::RIGHT);

        $renderedTable = $this->render($table);

        $expected = [
            '+----------------------+-------+---------------------------+',
            '| ID                   | NAME  | SCORE                     |',
            '+----------------------+-------+---------------------------+',
            '|                    1 |  John |                        12 |',
            '|                    2 | Tommy |                        15 |',
            '+----------------------+-------+---------------------------+',
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

        $clipped = \mb_substr($longName, 0, $maxLength - 1) . '…';

        $key = 'NAME';

        $table = new AsciiTable([$key]);
        $table->addRows([
            [
                $key => $longName,
            ],
        ]);

        $table->setColumnMaxWidth('NAME', $maxLength);

        $renderedTable = $this->render($table);

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
        $table = new AsciiTable(['ID', new Column('NAME', maxWidth: 20), 'SCORE']);
        $table->setDefaultColumnAlign('SCORE', Align::RIGHT);

        $renderedTable = $this->render($table);

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

        $table = new AsciiTable([$key]);
        $table->setColumnMaxWidth($key, $maxLength);

        $renderedTable = $this->render($table);

        $clippedTitle = \mb_substr($key, 0, $maxLength - 1) . '…';
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

    protected function formatNoData(int $maxLength): string
    {
        $noDataLabel = 'NO DATA';
        if (\mb_strlen($noDataLabel) > $maxLength) {
            $noDataLabel = \mb_substr($noDataLabel, 0, $maxLength - 1) . '…';
        } else {
            $noDataLabel = StringUtils::pad($noDataLabel, $maxLength, ' ', \STR_PAD_BOTH);
        }

        return $noDataLabel;
    }

    public function testHidden(): void
    {
        $key1 = Generator::getRandomString('key1');
        $key2 = Generator::getRandomString('key2');
        $key3 = Generator::getRandomString('key3');
        $table = new AsciiTable();
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

        $renderedTable = $this->render($table);

        $expected = [
            '+---+---+',
            '| A | C |',
            '+---+---+',
            '| a | c |',
            '+---+---+',
        ];

        Assert::assertEquals($expected, $renderedTable);
    }
}


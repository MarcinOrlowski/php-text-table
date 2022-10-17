<?php
declare(strict_types=1);

namespace MarcinOrlowski\AsciiTableTests;

use MarcinOrlowski\AsciiTable\Align;
use MarcinOrlowski\AsciiTable\AsciiTable;
use MarcinOrlowski\AsciiTable\Column;
use MarcinOrlowski\AsciiTable\Output\Writers\BufferWriter;
use MarcinOrlowski\PhpunitExtraAsserts\Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

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
            if (\substr($line, -1) === PHP_EOL) {
                $line = \substr($line, 0, -1);
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

    public function testCustomIndex()
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

    public function testRowCellsAutoAssign()
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

    public function testTableColumnAutoKey()
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

        $table->setColumnAlign('ID', Align::RIGHT);
        $table->setColumnAlign('SCORE', Align::RIGHT);

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

    /* ****************************************************************************************** */

    public function testCustomWidth(): void
    {
        $table = new AsciiTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12, 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);

        $table->setColumnWidth('ID', 20);
        $table->setColumnWidth('NAME', 5);
        $table->setColumnWidth('SCORE', 25);

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

    public function testCustomWidthAndAlign(): void
    {
        $table = new AsciiTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12, 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);

        $table->setColumnWidth('ID', 20);
        $table->setColumnWidth('NAME', 5);
        $table->setColumnWidth('SCORE', 25);

        $table->setColumnAlign('ID', Align::RIGHT);
        $table->setColumnAlign('NAME', Align::CENTER);
        $table->setColumnAlign('SCORE', Align::RIGHT);

        $renderedTable = $this->render($table);

        $expected = [
            '+----------------------+-------+---------------------------+',
            '| ID                   | NAME  | SCORE                     |',
            '+----------------------+-------+---------------------------+',
            '|                    1 | John  |                        12 |',
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

        $clipped = substr($longName, 0, $maxLength - 1) . '…';

        $key = 'NAME';

        $table = new AsciiTable([$key]);
        $table->addRows([
            [
                $key => $longName,
            ],
        ]);

        $table->setColumnWidth('NAME', $maxLength);

        $renderedTable = $this->render($table);

        $expected = [
            \sprintf('+-%s-+', \str_pad('', $maxLength, '-')),
            \sprintf('| %s%s |', $key, \str_repeat(' ', $maxLength - \strlen($key))),
            \sprintf('+-%s-+', \str_pad('', $maxLength, '-')),
            "| {$clipped} |",
            \sprintf('+-%s-+', \str_pad('', $maxLength, '-')),
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    /* ****************************************************************************************** */

    public function testNoData(): void
    {
        $maxLength = Generator::getRandomInt(10, 20);

        $key = 'NAME';

        $table = new AsciiTable([$key]);
        $table->setColumnWidth($key, $maxLength);

        $renderedTable = $this->render($table);

        $expected = [
            \sprintf('+-%s-+', \str_pad('', $maxLength, '-')),
            \sprintf('| %s%s |', $key, \str_repeat(' ', $maxLength - \strlen($key))),
            \sprintf('+-%s-+', \str_pad('', $maxLength, '-')),
            \sprintf('| %s |', $this->formatNoData($maxLength)),
            \sprintf('+-%s-+', \str_pad('', $maxLength, '-')),
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    public function testNoDataWithClipping(): void
    {
        $maxLength = Generator::getRandomInt(10, 20);
        $key = Generator::getRandomString('name', $maxLength * 2);
        $clipped = \substr($key, 0, $maxLength - 1) . '…';

        $table = new AsciiTable([$key]);
        $table->setColumnWidth($key, $maxLength);

        $renderedTable = $this->render($table);

        $expected = [
            \sprintf('+-%s-+', \str_pad('', $maxLength, '-')),
            \sprintf('| %s |', $clipped),
            \sprintf('+-%s-+', \str_pad('', $maxLength, '-')),
            \sprintf('| %s |', $this->formatNoData($maxLength)),
            \sprintf('+-%s-+', \str_pad('', $maxLength, '-')),
        ];
        Assert::assertEquals($expected, $renderedTable);
    }

    protected function formatNoData(int $maxLength): string
    {
        $noDataLabel = 'NO DATA';
        if (\strlen($noDataLabel) > $maxLength) {
            $noDataLabel = substr($noDataLabel, 0, $maxLength - 1) . '…';
        } else {
            $noDataLabel = \str_pad($noDataLabel, $maxLength, ' ', \STR_PAD_BOTH);
        }

        return $noDataLabel;
    }
}


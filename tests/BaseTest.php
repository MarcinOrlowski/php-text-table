<?php
declare(strict_types=1);

namespace MarcinOrlowski\AsciiTableTests;

use MarcinOrlowski\AsciiTable\AsciiTable;
use MarcinOrlowski\AsciiTable\Output\Writers\BufferWriter;
use MarcinOrlowski\PhpunitExtraAsserts\Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

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

        $tableRender = $this->render($table);

        $expected = [
            '+---+---+---+',
            '| A | B | C |',
            '+---+---+---+',
            '| a | b | c |',
            '+---+---+---+',
        ];

        Assert::assertEquals($expected, $tableRender);
    }

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

        $tableRender = $this->render($table);

        Assert::assertEquals($rowCnt, $table->getRowCount());
        Assert::assertEquals($expected, $tableRender);
    }

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

        $tableRender = $this->render($table);

        $expected = [
            '+---+---+---+',
            '| A | B | C |',
            '+---+---+---+',
            '| a | b | c |',
            '+---+---+---+',
        ];

        Assert::assertEquals($expected, $tableRender);
    }

    public function testCustomIndex()
    {
        $table = new AsciiTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'SCORE' => 12, 'NAME' => 'John'],
            ['SCORE' => 15, 'ID' => 2, 'NAME' => 'Tommy'],
        ]);
        $tableRender = $this->render($table);

        $expected = [
            '+----+-------+-------+',
            '| ID | NAME  | SCORE |',
            '+----+-------+-------+',
            '| 1  | John  | 12    |',
            '| 2  | Tommy | 15    |',
            '+----+-------+-------+',
        ];
        Assert::assertEquals($expected, $tableRender);
    }
}

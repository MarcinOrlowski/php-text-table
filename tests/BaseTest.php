<?php
declare(strict_types=1);

namespace MarcinOrlowski\AsciiTableTests;

use MarcinOrlowski\AsciiTable\AsciiTable;
use MarcinOrlowski\AsciiTable\Output\OutputContract;
use MarcinOrlowski\AsciiTable\Output\Writers\BufferWriter;
use MarcinOrlowski\PhpunitExtraAsserts\Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    protected OutputContract $bufferWriter;

    public function setUp(): void
    {
        parent::setUp();

        $this->bufferWriter = new BufferWriter();
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

        $table->render($this->bufferWriter);

        $result = $this->bufferWriter->getBuffer();

        $expected = [
            '+---+---+---+',
            '| A | B | C |',
            '+---+---+---+',
            '| a | b | c |',
            '+---+---+---+',
        ];

        Assert::assertEquals($expected, $result);
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

        $table->render($this->bufferWriter);
        $result = $this->bufferWriter->getBuffer();

        Assert::assertEquals($rowCnt, $table->getRowCount());
        Assert::assertEquals($expected, $result);
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

        $table->render($this->bufferWriter);
        $result = $this->bufferWriter->getBuffer();

        $expected = [
            '+---+---+---+',
            '| A | B | C |',
            '+---+---+---+',
            '| a | b | c |',
            '+---+---+---+',
        ];

        Assert::assertEquals($expected, $result);
    }

    public function testX()
    {
        $table = new AsciiTable(['ID', 'NAME', 'SCORE']);
        $table->addRows([
            ['ID' => 1, 'NAME' => 'John', 'SCORE' => 12],
            ['ID' => 2, 'NAME' => 'Tommy', 'SCORE' => 15],
        ]);
        $table->render();
    }
}

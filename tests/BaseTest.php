<?php
declare(strict_types=1);

namespace MarcinOrlowski\AsciiTableTests;

use MarcinOrlowski\AsciiTable\Table;
use MarcinOrlowski\PhpunitExtraAsserts\Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    protected BufferWriter $bufferWriter;

    public function setUp(): void
    {
        parent::setUp();

        $this->bufferWriter = new BufferWriter();
    }

    public function testSimpleTable(): void
    {
        $table = new Table();
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
        $table = new Table();
        $table->addColumns([
            'A',
            'B',
            'C',
        ]);


        $rowCnt = Generator::getRandomInt(2, 10);

        for ($i = 0; $i < $rowCnt; $i++) {
            $table->addRow([
                'A' => "a",
                'B' => "b",
                'C' => "c",
            ]);
        }

        $table->render($this->bufferWriter);

        $result = $this->bufferWriter->getBuffer();

        $expected = [
            '+---+---+---+',
            '| A | B | C |',
            '+---+---+---+',
        ];

        for ($i = 0; $i < $rowCnt; $i++) {
            $expected[] = '| a | b | c |';
        }

        $expected[] = '+---+---+---+';

        Assert::assertEquals($rowCnt, $table->getRowCount());
        Assert::assertEquals($expected, $result);
    }

}

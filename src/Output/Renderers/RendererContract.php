<?php
declare(strict_types=1);

/**
 * ASCII Table
 *
 * @package   MarcinOrlowski\AsciiTable
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2022 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-ascii-table
 */

namespace MarcinOrlowski\AsciiTable\Renderers;

use MarcinOrlowski\AsciiTable\AsciiTable;
use MarcinOrlowski\AsciiTable\Output\OutputContract;

interface RendererContract
{
    /**
     * Renders provided `Table` using provided output writer.
     *
     * @param AsciiTable     $table  Instance of `AsciiTable` to render.
     * @param OutputContract $writer Output writer to use.
     */
    public function render(AsciiTable $table, OutputContract $writer): void;
}

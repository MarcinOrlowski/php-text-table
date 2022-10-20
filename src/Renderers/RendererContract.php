<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @package   MarcinOrlowski\TextTable
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2022 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable\Renderers;

use MarcinOrlowski\TextTable\TextTable;
use MarcinOrlowski\TextTable\Output\WriterContract;

interface RendererContract
{
    /**
     * Renders provided `Table` using provided output writer.
     *
     * @param TextTable $table Instance of `AsciiTable` to render.
     */
    public function render(TextTable $table): array;
}

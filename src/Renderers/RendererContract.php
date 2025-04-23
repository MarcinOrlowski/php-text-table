<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable\Renderers;

use MarcinOrlowski\TextTable\TextTable;

interface RendererContract
{
    /**
     * Renders provided `Table` using provided output writer.
     *
     * @param TextTable $table Instance of `TextTable` to render.
     *
     * @return string[]
     */
    public function render(TextTable $table): array;

    /**
     * Helper that returns rendered table as single string.
     *
     * @param TextTable $table Instance of `TextTable` to render.
     */
    public function renderAsString(TextTable $table): string;
}

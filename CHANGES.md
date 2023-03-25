# Text Table

Fast and flexible PHP library for text tables.

---

## Changes

* 2.3.0
  * Fixed support for adding row with more columns than table declared [#2].
  * Added `TextTable::getColumnCount()` helper method.
  * Added factory methods to each of special exception classes.


* 2.2.0 (2022-11-21)
  * Added `MsDosRenderer` producing tables reminiscent of ancient MS DOS times.
  * Added `renderAsString()` to `RendererContract` (and all renderers).
  * The `hideColumn()` method now also accepts `array` of column IDs to hide.
  * Rendering table with all columns hidden always throws `NoVisibleColumnsException`.
  * Added more tests.
  * `addCell()` now accepts `float` values too.


* 2.1.0 (2022-10-21)
  * Added `FancyRenderer` producing nicer looking table frames. It's now default one.
  * Former `DefaultRenderer` is now `PlusMinusRenderer`.


* 2.0.0 (2022-10-20)
  * Main class name changed to `TextTable`.
  * `RendererContract`'s `render()` no longer expects returns `string[]`.
  * Writers and `WriterContract` are completely gone as they are no longer needed.
  * `Cell`'s constructor allows to set title/cell align separately.
  * `Column`'s `show()` and `hide()` helpers are now removed.


* 1.7.0 (2022-10-19)
  * Corrected rendering tables with first/last column hidden.
  * Attempt to render table with all columns hidden throws `NoVisibleColumnsException`.
  * All custom exception class names now ends with `Exception`.


* 1.6.0 (2022-10-19)
  * Rows can now contain incomplete set of column cells.


* 1.5.0 (2022-10-18)
  * Columns can now be hidden from the rendered output.


* 1.4.0 (2022-10-18)
  * Corrected handling of multibyte characters (UTF-8).
  * Fixed 'NO DATA' case being rendered in too narrow cell.


* 1.3.0 (2022-10-18)
  * Improved code documentation.
  * Renamed `OutputContract` to `WriterContract`.
  * Added own exceptions for most critical errors.
  * Added support for individual cell alignment.
  * Added more tests.


* v1.2.0 (2022-10-17)
  * Main class name changed to `AsciiTable`.
  * Added option to configure column default align.
  * Added option to separately control table column title align.
  * Custom column max width is now correctly handled.
  * Fixed width columns are now truncated automatically with ellipsis.
  * Improved code creating row cells from plain string arrays.
  * Added more tests.


* v1.1.0 (2022-10-17)
  * Corrected `\ArrayAccess` implementation.
  * Added more tests.


* v1.0.0 (2022-10-17)
  * Initial public release.

# Text Table

Fast and flexible PHP library rendering plain ASCII tables.

---

## Changes

* dev
  * Main class name changed to `TextTable`.
  * `RendererContract`'s `render()` no longer expects returns `string[]`.
  * Writers and `WriterContract` are completely gone as they are no longer needed.


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

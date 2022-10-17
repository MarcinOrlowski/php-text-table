# ASCII Table

Fast and flexible PHP library rendering plain ASCII tables.

---

## Changes

* @dev
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

# Text Table

Fast and flexible PHP library for text tables.

---

## Usage examples

1. Simplest use case
1. Custom align & and cells' width limit

### Simplest use case

This is probably the most common and yet the simplest possible usage:

```php
// Create the tablie with 3 columns, of which IDs will be their values.
$table = new TextTable(['ID', 'NAME', 'SCORE']);

// Add 2 rows to the table, assignig cells in order of appearance.
$table->addRows([
    [1, 'John', 12],
    [2, 'Tommy', 15],
]);

// Print the whole table.
echo $table->renderAsString();
```

which in turn should produce this table:

```php
┌────┬───────┬───────┐
│ ID │ NAME  │ SCORE │
├────┼───────┼───────┤
│ 1  │ John  │ 12    │
│ 2  │ Tommy │ 15    │
└────┴───────┴───────┘
```

---

### Use different table renderer

The common usage of `TextTable` library is to eventually present used the
content of the table in the nice tabular form. As the table itself is just
a data structure, "visualisation" of the table is done via the dedicated
renderer which will return the table as a string. It therefore can influence
the final look of the table, incl. the way it is formatted.

To override the renreder, simply pass instance of the renderer of your
choice to the `TextTable`'s rendering shortcuts:

```php
use MarcinOrlowski\TextTable\Renderers\PlusMinusRenderer;

...
$renderer = new PlusMinusRenderer();
echo($table->renderAsString($renderer));
```

or

```php
...
$renderer = new PlusMinusRenderer();
echo($table->renderAsString($renderer));
```

would produce table rendered using `+`, `-` and `|` characters
instead of table shaped characters:

```php
+----+-------+-------+
| ID | NAME  | SCORE |
+----+-------+-------+
| 1  | John  | 12    |
| 2  | Tommy | 15    |
+----+-------+-------+
```

**NOTE:** You can provide your own renderer (i.e. producing `HTML` or whatever you wish,
by implementing `RendererContract` in your class.

For available built-in renderers, see [src/Renderers/](../src/Renderers/) sources.

**HINT:** If you want to just introduce new frame characters, just extend built-in
[AsciiTableRenderer](../src/Renderers/AsciiTableRenderer.php) and provide characters of your choices
only. See i.e. [src/Renderers/MsDosRenderer.php](../src/Renderers/MsDosRenderer.php) code for
reference.

---

### Custom align and cells' width limit

```php
// Create the tablie with 3 columns, of which IDs will be their values.
// The definition of 2nd column is created explicitly, using instance 
// of Column class that is automatically created for other columns.
$table = new TextTable(['ID', new Column('NAME', maxWidth: 20), 'SCORE']);

$table->setColumnAlign('SCORE', Align::RIGHT);

// Add 2 rows to the table, assignig cells in order of appearance.
// Similarly to the column above, the 2nd cell in second row is also
// created directly using instance of Cell, to gain more control. 
$table->addRows([
    [1, 'John', 12],
    [2, new Cell('Tommy', Align::CENTER), 15],
]);

// print the whole table using MS-DOS style frames.
$renderer = new MsDosRenderer();
echo $renderer->renderAsString()
```

would produce this nicely formatted text table:

```php
╔════╦══════════════════════╦═══════╗
║ ID ║ NAME                 ║ SCORE ║
╠════╬══════════════════════╬═══════╣
║ 1  ║ John                 ║    12 ║
║ 2  ║        Tommy         ║    15 ║
╚════╩══════════════════════╩═══════╝
```

---

## License

* Written and copyrighted &copy;2022 by Marcin Orlowski <mail (#) marcinorlowski (.) com>
* Text Table is open-sourced software licensed under
  the [MIT license](http://opensource.org/licenses/MIT)

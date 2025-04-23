# Text Table

```ascii
..######.#####.##...##.######...######...#....#####..##....#####..
....##...##.....##.##....##.......##....###...##..##.##....##.....
....##...##......###.....##.......##...##.##..##..##.##....##.....
....##...####.....#......##.......##..##...##.#####..##....####...
....##...##......###.....##.......##..#######.##..##.##....##.....
....##...##.....##.##....##.......##..##...##.##..##.##....##.....
....##...#####.##...##...##.......##..##...##.#####..#####.#####..
```

Fast and flexible PHP library for text tables.

---

## Usage examples

1. Simplest use case
1. Custom align & and cells' width limit

**NOTE:** For proper rendering in environments like web browsers, ensure you are using a monospace
font or wrap the output in `<pre>` tags.

### Simplest use case

This is likely the most common and yet the simplest possible usage:

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

The primary use of the `TextTable` library is to present table content in a visually appealing
tabular form. Since the table is merely a data structure, its "visualization" is handled by a
dedicated renderer that returns the table as a string. This can influence the final appearance of
the table, including its formatting.

To override the renderer, simply pass an instance of your chosen renderer to the `TextTable`'s
rendering shortcuts:

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

These would produce a table rendered using `+`, `-`, and `|` characters, instead of using
table-shaped characters:

```php
+----+-------+-------+
| ID | NAME  | SCORE |
+----+-------+-------+
| 1  | John  | 12    |
| 2  | Tommy | 15    |
+----+-------+-------+
```

**NOTE:** You can provide your own renderer (e.g., producing `HTML` or whatever you wish) by
implementing the `RendererContract` in your class.

For available built-in renderers, see the [src/Renderers/](../src/Renderers/) source code.

**HINT:** If you want to introduce new frame characters, simply extend the
built-in [AsciiTableRenderer](../src/Renderers/AsciiTableRenderer.php) and provide the characters of
your choice. See [src/Renderers/MsDosRenderer.php](../src/Renderers/MsDosRenderer.php) for
reference.

---

### Custom align and cells' width limit

```php
// Create a table with 3 columns, where the values will be the IDs.
// The 2nd column's definition is created explicitly using an instance 
// of the Column class. Instances for the other columns are created automatically.
$table = new TextTable(['ID', new Column('NAME', maxWidth: 20), 'SCORE']);

$table->setColumnAlign('SCORE', Align::RIGHT);

// Add 2 rows to the table, assigning cells in the order they appear.
// Similar to the column example above, the 2nd cell in the second row is 
// explicitly created using an instance of the Cell class for greater control.
$table->addRows([
    [1, 'John', 12],
    [2, new Cell('Tommy', Align::CENTER), 15],
]);

// Print the entire table using MS-DOS style frames.
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

* Written and copyrighted &copy;2022-2025 by Marcin Orlowski
* Text Table is open-sourced software licensed under
  the [MIT license](https://opensource.org/license/mit)

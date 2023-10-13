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

## Fundamentals

In the `TextTable` there are two fundamental elements: the table data (represented by
the `TextTable` class) which holds all the data like rows, cells and column definition and the
renderer (represented by the class implementing the `TextTable\Renderer\RendererContact` contract)
which is responsible for creating visual representation of the table data.

Thanks to that separation, it's easy to create new renderers and use them with the same table data,
so you can have i.e. nice ASCII table as well as HTML table with the same data.

The table data structure is build with the concept of rows (represented by `Row` class), which is a
container for cells (represented by `Cell` class). As the table usually has a form of a grid, each
table must have a column definition (represented by `Column` class) which contains information about
number of data columns, header information and column width.

When you create new table, you need to specify the column definition:

```php
$table = new TextTable([
  'FOO',
  'BAR',
]);
```

This will create table that is expected to contain two columns. The first column will be named `FOO`
and the second one `BAR`. The column width will be calculated automatically based on the data
provided once you start adding rows.

Because of simplifed form used, provided strings must be unique as these are also used as column
reference keys. The above syntax is just a shortcut for:

```php
$table = new TextTable([
   'FOO' => 'FOO',
   'BAR' => 'BAR',
]);
```

But under the hood, it all ends up as instance of `Column` class. So this in fact is equivalent to:

```php
$table = new TextTable([
   'FOO' => new Column('FOO'),
   'BAR' => new Column('BAR'),
]);
```

If you, for any reason need column with the same header, you **MUST** provide each of the column
with unique reference key, otherwise you will get an exception.

```php
$table = new TextTable([
   'FOO' => 'FOO',
   'BAR' => 'FOO',
]);
```

If you need more control over column definition (i.e. max width, or alignment), you need to
use `Column` as there's no syntactic sugar to pass any additional information to the column
definition.

## Adding rows

Once the table object definition is created, you can start adding rows. Each row is represented by
the `Row` class and you can be added to the table using the `addRow()` method:

```php
$table->addRow([
  'foo',
  'bar',
]);
```

Data is assigned to columns in order, but you can specify the target columns by using keys from the
column definition:

```php
$table->addRow([
  'BAR' => 'bar',
  'FOO' => 'foo',
]);
```

you can also fill some columns only too:

```php
$table->addRow([
  'BAR' => 'bar',
]);
```

### Adding multiple rows

You can add multiple rows at once by using the `addRows()` method:

```php
$table->addRows([
  ['foo', 'bar'],
  ['baz', 'qux'],
]);
```

### Separator rows

Sometimes you may want to add separator row to the table. This can be done by using
the `addSeparator()`
method that under the hood adds instance of `Separator` row class:

```php
$table->addSeparator();
```

## Rendering

Once your table is ready, you can render it using the `render()` method:

```php
echo $table->render();
```

This will use the default renderer which is the `AsciiTableRenderer` class. If you want to use
different or custom renderer, you need do it that way:

```php
$renderer = new AsciiTableRenderer();
echo $renderer->render($table);
```

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

which in turn should produce this nice ASCII table:

```php
+----+-------+-------+
| ID | NAME  | SCORE |
+----+-------+-------+
| 1  | John  | 12    |
| 2  | Tommy | 15    |
+----+-------+-------+
```

---

### Custom align & and cells' width limit

```php
// Create the tablie with 3 columns, of which IDs will be their values.
// The definition of 2nd column is created explicitly, using instance 
// of Column class that is automatically created for other columns.
$table = new TextTable(['ID', new Column('NAME', maxWidth: 20), 'SCORE']);

$table->setDefaultColumnAlign('SCORE', Align::RIGHT);

// Add 2 rows to the table, assignig cells in order of appearance.
// Similarly to the column above, the 2nd cell in second row is also
// created directly using instance of Cell, to gain more control. 
$table->addRows([
    [1, 'John', 12],
    [2, new Cell('Tommy', Align::CENTER), 15],
]);

// Print the whole table.
echo $table->renderAsString()
```

would produce this nicely formatted ASCII table:

```php
+----+----------------------+-------+
| ID | NAME                 | SCORE |
+----+----------------------+-------+
| 1  | John                 |    12 |
| 2  |        Tommy         |    15 |
+----+----------------------+-------+
```

---

## License

* Written and copyrighted &copy;2022 by Marcin Orlowski <mail (#) marcinorlowski (.) com>
* Text Table is open-sourced software licensed under
  the [MIT license](http://opensource.org/licenses/MIT)

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

[![Latest Stable Version](http://poser.pugx.org/marcin-orlowski/text-table/v)](https://packagist.org/packages/marcin-orlowski/text-table)
[![Total Downloads](http://poser.pugx.org/marcin-orlowski/text-table/downloads)](https://packagist.org/packages/marcin-orlowski/text-table)
[![License](http://poser.pugx.org/marcin-orlowski/text-table/license)](https://packagist.org/packages/marcin-orlowski/text-table)
[![PHP Version Require](http://poser.pugx.org/marcin-orlowski/text-table/require/php)](https://packagist.org/packages/marcin-orlowski/text-table)

There's also
a [Python version of this library](https://github.com/MarcinOrlowski/python-flex-text-table/).

---

## Features

1. Production ready,
1. Easy to use,
1. No additional dependencies,
1. [Documented](docs/README.md).

---

## Usage examples

**NOTE:** For proper rendering in environments like web browsers, ensure you are using a monospace
font or wrap the output in `<pre>` tags.

```php
$table = new TextTable(['ID', 'NAME', 'SCORE']);
$table->addRows([
    [1, 'John', 12],
    [2, 'Tommy', 15],
]);
echo $table->renderAsString();
```

would produce nice text table:

```php
┌────┬───────┬───────┐
│ ID │ NAME  │ SCORE │
├────┼───────┼───────┤
│ 1  │ John  │ 12    │
│ 2  │ Tommy │ 15    │
└────┴───────┴───────┘
```

Check docs for more [usage examples](docs/README.md).

---

## License

* Written and copyrighted &copy;2022-2025 by Marcin Orlowski <mail (#) marcinorlowski (.) com>
* Text Table is open-sourced software licensed under
  the [MIT license](https://opensource.org/license/mit)

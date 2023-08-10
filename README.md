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

---

Table of contents

1. [Features](#features)
1. [Installation & requirements](docs/setup.md)
1. [Examples](docs/examples.md)
1. [License](#license) 

---

## Features

1. Simple API, easy to use,
1. Lightweight (no additional dependencies),
1. Production ready.

---

## Usage examples

Simples possible usage:

```php
$table = new TextTable(['ID', 'NAME', 'SCORE']);
$table->addRows([
    [1, 'John', 12],
    [2, 'Tommy', 15],
]);
$table->render();
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

See more [usage examples](docs/examples.md).

---

## License

* Written and copyrighted &copy;2022-2023 by Marcin Orlowski <mail (#) marcinorlowski (.) com>
* Text Table is open-sourced software licensed under
  the [MIT license](http://opensource.org/licenses/MIT)

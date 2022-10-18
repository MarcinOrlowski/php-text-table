# ASCII Table

Fast and flexible PHP library rendering plain ASCII tables.

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
$table = new AsciiTable(['ID', 'NAME', 'SCORE']);
$table->addRows([
    [1, 'John', 12],
    [2, 'Tommy', 15],
]);
$table->render();
```

would produce nice ASCII table:

```php
+----+-------+-------+
| ID | NAME  | SCORE |
+----+-------+-------+
| 1  | John  | 12    |
| 2  | Tommy | 15    |
+----+-------+-------+
```

See more [usage examples](docs/examples.md).

---

## License

* Written and copyrighted &copy;2022 by Marcin Orlowski <mail (#) marcinorlowski (.) com>
* ASCII Table is open-sourced software licensed under
  the [MIT license](http://opensource.org/licenses/MIT)

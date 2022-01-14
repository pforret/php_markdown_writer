# Write Markdown documents

Github: 
![GitHub tag](https://img.shields.io/github/v/tag/pforret/php_markdown_writer)
![Tests](https://github.com/pforret/php_markdown_writer/workflows/Run%20Tests/badge.svg)
![Psalm](https://github.com/pforret/php_markdown_writer/workflows/Detect%20Psalm%20warnings/badge.svg)
![Styling](https://github.com/pforret/php_markdown_writer/workflows/Check%20&%20fix%20styling/badge.svg)

Packagist: 
[![Packagist Version](https://img.shields.io/packagist/v/pforret/php_markdown_writer.svg?style=flat-square)](https://packagist.org/packages/pforret/php_markdown_writer)
[![Packagist Downloads](https://img.shields.io/packagist/dt/pforret/php_markdown_writer.svg?style=flat-square)](https://packagist.org/packages/pforret/php_markdown_writer)

Write Markdown documents

	created on 2022-01-14 by peter@forret.com

## Installation

You can install the package via composer:

```bash
composer require pforret/php_markdown_writer
```

## Usage

``` php
$obj = new Pforret\PhpMarkdownWriter();
echo $obj->echoPhrase('Hello, pforret!');
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email author_email instead of using the issue tracker.

## Credits

- [Peter Forret](https://github.com/pforret)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

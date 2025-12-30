# Write Markdown documents

Github: 
![GitHub tag](https://img.shields.io/github/v/tag/pforret/php_markdown_writer)
![Tests](https://github.com/pforret/php_markdown_writer/workflows/Run%20Tests/badge.svg)
![Psalm](https://github.com/pforret/php_markdown_writer/workflows/Detect%20Psalm%20warnings/badge.svg)

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

### Basic Usage (in-memory)

```php
use Pforret\PhpMarkdownWriter\PhpMarkdownWriter;

$writer = new PhpMarkdownWriter();
$writer
    ->h1('Document Title')
    ->paragraph('Introduction text')
    ->h2('Section 1')
    ->bullet('Point 1')
    ->bullet('Point 2', 1)  // indented
    ->h2('Section 2')
    ->code('echo "Hello";', 'php');

echo $writer->asMarkdown();
```

### Write directly to file

```php
$writer = new PhpMarkdownWriter('output.md');
$writer->h1('Report')->paragraph('Content here.');
// File is written automatically when $writer goes out of scope
```

### Export to different formats

```php
$writer = new PhpMarkdownWriter();
$writer->h1('Title')->paragraph('Content');

// Save as Markdown
$writer->saveAsMarkdown('output.md');

// Save as HTML
$writer->saveAsHtml('output.html');

// Save as PDF (requires mpdf/mpdf)
$writer->saveAsPdf('output.pdf');

// Get HTML string
$html = $writer->asHtml();
```

## Available Methods

### Headings & Structure
- `h1($text)` - Level 1 heading
- `h2($text)` - Level 2 heading
- `h3($text)` - Level 3 heading
- `h4($text)` - Level 4 heading
- `h5($text)` - Level 5 heading
- `h6($text)` - Level 6 heading
- `hr()` - Horizontal rule/divider
- `pagebreak()` - Page break for PDF output (adds `<pagebreak />`)

### Text Formatting
- `paragraph($text, $continued = false)` - Paragraph (use `$continued = true` for single line break)
- `bold($text, $continued = false)` - Bold text
- `italic($text, $continued = false)` - Italic text
- `strikethrough($text, $continued = false)` - Strikethrough text (~~text~~)
- `blockquote($text, $continued = false)` - Blockquote (> text)

### Links & Images
- `link($text, $url)` - Inline link [text](url)
- `image($alt, $url, $title = '')` - Image ![alt](url "title")

### Lists
- `bullet($text, $indent = 0)` - Bullet point (use `$indent` for nested items)
- `numbered($text, $number = 1, $indent = 0)` - Numbered list item
- `check($text, $indent = 0, $done = false)` - Checkbox item

### Code
- `code($text, $language = '')` - Fenced code block with optional language
- `fixed($text)` - Fixed-width/preformatted text (indented)
- `inlineCode($text)` - Returns inline code string (\`text\`)

### Tables
- `table($array, $with_headers = true)` - Full table from array
- `table_header($array)` - Table header row
- `table_row($array)` - Table data row

### Output
- `asMarkdown()` - Get accumulated Markdown as string
- `asHtml()` - Convert to HTML string
- `saveAsMarkdown($filename)` - Save to Markdown file
- `saveAsHtml($filename)` - Save to HTML file
- `saveAsPdf($filename)` - Save to PDF file

### Utility
- `reset()` - Clear accumulated content
- `setOutput($filename)` - Set file output after construction
- `markup($text)` - Auto-convert URLs and emails to links
- `getConverterConfig()` - Get current CommonMark converter config
- `setConverterConfig($config)` - Modify CommonMark converter settings
- `getPdfConfig()` - Get current mPDF config
- `setPdfConfig($config)` - Modify mPDF settings

### Converter Configuration

The HTML converter can be configured with these options:

```php
$writer = new PhpMarkdownWriter();
$writer->setConverterConfig([
    'html_input' => 'escape',      // 'strip', 'allow', or 'escape'
    'allow_unsafe_links' => false, // Block javascript:, vbscript:, etc.
]);
```

### PDF Configuration

The PDF output can be configured with these options:

```php
$writer = new PhpMarkdownWriter();

// Quick font settings
$writer->setPdfFontFamily('DejaVuSerif');  // DejaVuSans, DejaVuSerif, FreeSans, etc.
$writer->setPdfFontSize(11);

// Or use full config
$writer->setPdfConfig([
    'format' => 'Letter',          // 'A4', 'Letter', 'A5', etc.
    'default_font_size' => 11,
    'margin_left' => 20,
    'margin_right' => 20,
]);
$writer->saveAsPdf('document.pdf');
```

### PDF Headers, Footers, and Metadata

Add headers, footers, metadata, and watermarks to PDF documents:

```php
$writer = new PhpMarkdownWriter();

// Set document metadata
$writer->addPdfTitle('Annual Report 2025');
$writer->addPdfAuthor('John Doe');
$writer->addPdfSubject('Financial Overview');
$writer->addPdfKeywords('finance, report, annual, 2025');

// Set header and footer
$writer->addPdfHeader('<div style="text-align: center;">My Document</div>');
$writer->addPdfFooter('<div style="text-align: center;">Page {PAGENO} of {nbpg}</div>');

// Add watermark (optional)
$writer->addPdfWatermark('DRAFT');

$writer->h1('Title')->paragraph('Content...');
$writer->saveAsPdf('document.pdf');
```

Available mPDF variables: `{PAGENO}`, `{nbpg}`, `{DATE j-m-Y}`, `{DOCNUM}`

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

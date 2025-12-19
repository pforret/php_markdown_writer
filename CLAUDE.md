# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP library for programmatically generating Markdown documents. Supports fluent API for building documents with headings, paragraphs, lists, tables, links, images, and code blocks. Can write directly to files or accumulate in memory. Also supports export to HTML and PDF formats.

## Common Commands

```bash
composer test                        # Run all tests (68 tests)
composer test -- --filter=TestName   # Run a specific test
composer format                      # Fix code style with Laravel Pint
composer psalm                       # Static analysis
```

## Architecture

Single class library in `src/PhpMarkdownWriter.php`:
- Constructor optionally takes a filename to write output directly to disk
- All formatting methods return `$this` for fluent chaining
- `asMarkdown()` returns accumulated content as a string
- `asHtml()` converts to HTML using league/commonmark
- `saveAsPdf()` generates PDF using mpdf/mpdf
- `markup()` auto-converts URLs (http, https, ftp) and emails to Markdown links
- File handle is automatically closed in destructor

## Available Methods

**Headings & Structure:** `h1()`, `h2()`, `h3()`, `h4()`, `h5()`, `h6()`, `hr()`, `pagebreak()`

**Text:** `paragraph($text, $continued)`, `bold($text, $continued)`, `italic($text, $continued)`, `strikethrough($text, $continued)`, `blockquote($text, $continued)`

**Links & Images:** `link($text, $url)`, `image($alt, $url, $title)`

**Lists:** `bullet($text, $indent)`, `numbered($text, $number, $indent)`, `check($text, $indent, $done)`

**Code:** `code($text, $language)`, `fixed($text)`, `inlineCode($text)` (returns string)

**Tables:** `table($array, $with_headers)`, `table_header($array)`, `table_row($array)`

**Output:** `asMarkdown()`, `asHtml()`, `saveAsMarkdown($file)`, `saveAsHtml($file)`, `saveAsPdf($file)`

**Utility:** `reset()`, `setOutput($filename)`, `markup($text)`, `getConverterConfig()`, `setConverterConfig($config)`, `getPdfConfig()`, `setPdfConfig($config)`, `addPdfHeader($html)`, `addPdfFooter($html)`, `addPdfTitle($title)`, `addPdfAuthor($author)`

## Key Patterns

- Methods that output Markdown return `PhpMarkdownWriter` for chaining
- Two output modes: file-based (via constructor or `setOutput()`) or memory-only (call `asMarkdown()` to retrieve)
- `reset()` clears accumulated Markdown and file handle
- The `$continued` parameter on text methods controls line endings (single vs double newline)
- The `$indent` parameter on list methods controls nesting depth (3 spaces per level)
- `inlineCode()` returns a string (for use within other content) rather than adding to the buffer
- `link()` does not add trailing newlines (for inline use within paragraphs)

## Dependencies

- `league/commonmark` - Markdown to HTML conversion
- `mpdf/mpdf` - HTML to PDF conversion

## Test Coverage

All public methods are tested in `tests/PhpMarkdownWriterTest.php` (83 tests, 119 assertions).

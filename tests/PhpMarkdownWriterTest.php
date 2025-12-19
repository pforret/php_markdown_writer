<?php

namespace Pforret\PhpMarkdownWriter\Tests;

use Pforret\PhpMarkdownWriter\PhpMarkdownWriter;
use PHPUnit\Framework\TestCase;

class PhpMarkdownWriterTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir().'/php_markdown_writer_test_'.uniqid();
        if (! is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        // Clean up temp files
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir.'/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->tempDir);
        }
    }

    public function test_markup_urls()
    {
        $writer = new PhpMarkdownWriter;
        $this->assertEquals(
            'this is a [www.google.com](https://www.google.com) link',
            $writer->markup('this is a https://www.google.com link')
        );
    }

    public function test_markup_http_urls()
    {
        $writer = new PhpMarkdownWriter;
        $this->assertEquals(
            'this is a [www.google.com](http://www.google.com) link',
            $writer->markup('this is a http://www.google.com link')
        );
    }

    public function test_markup_ftp_urls()
    {
        $writer = new PhpMarkdownWriter;
        $this->assertEquals(
            'download from [ftp.example.com/file.zip](ftp://ftp.example.com/file.zip)',
            $writer->markup('download from ftp://ftp.example.com/file.zip')
        );
    }

    public function test_markup_emails()
    {
        $writer = new PhpMarkdownWriter;
        $this->assertEquals(
            'send email to [peter@forret.com](mailto:peter@forret.com), [test@toolstud.io](mailto:test@toolstud.io), [hans12@mail.first-responder.com](mailto:hans12@mail.first-responder.com)',
            $writer->markup('send email to peter@forret.com, test@toolstud.io, hans12@mail.first-responder.com')
        );
    }

    public function test_h1()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h1('test');
        $this->assertEquals("\n# test\n\n", $writer->asMarkdown(), 'h1 -> #');
    }

    public function test_h2()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h2('test');
        $this->assertEquals("\n## test\n\n", $writer->asMarkdown(), 'h2 -> ##');
    }

    public function test_h3()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h3('test');
        $this->assertEquals("\n### test\n\n", $writer->asMarkdown(), 'h3 -> ###');
    }

    public function test_h4()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h4('test');
        $this->assertEquals("\n#### test\n\n", $writer->asMarkdown(), 'h4 -> ####');
    }

    public function test_h5()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h5('test');
        $this->assertEquals("\n##### test\n\n", $writer->asMarkdown(), 'h5 -> #####');
    }

    public function test_h6()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h6('test');
        $this->assertEquals("\n###### test\n\n", $writer->asMarkdown(), 'h6 -> ######');
    }

    public function test_hr()
    {
        $writer = new PhpMarkdownWriter;
        $writer->hr();
        $this->assertEquals("\n---\n\n", $writer->asMarkdown());
    }

    public function test_hr_between_sections()
    {
        $writer = new PhpMarkdownWriter;
        $writer->paragraph('Section 1');
        $writer->hr();
        $writer->paragraph('Section 2');
        $this->assertStringContainsString('---', $writer->asMarkdown());
    }

    public function test_pagebreak()
    {
        $writer = new PhpMarkdownWriter;
        $writer->pagebreak();
        $this->assertEquals("\n<pagebreak />\n\n", $writer->asMarkdown());
    }

    public function test_pagebreak_in_html()
    {
        $writer = new PhpMarkdownWriter;
        $writer->paragraph('Page 1');
        $writer->pagebreak();
        $writer->paragraph('Page 2');
        $html = $writer->asHtml();
        $this->assertStringContainsString('<pagebreak />', $html);
    }

    public function test_bullet()
    {
        $writer = new PhpMarkdownWriter;
        $writer->bullet('item one');
        $this->assertEquals("* item one\n", $writer->asMarkdown());
    }

    public function test_bullet_with_indent()
    {
        $writer = new PhpMarkdownWriter;
        $writer->bullet('item one', 0);
        $writer->bullet('sub item', 1);
        $writer->bullet('sub sub item', 2);
        $this->assertEquals("* item one\n   * sub item\n      * sub sub item\n", $writer->asMarkdown());
    }

    public function test_bullet_with_url_markup()
    {
        $writer = new PhpMarkdownWriter;
        $writer->bullet('visit https://example.com');
        $this->assertEquals("* visit [example.com](https://example.com)\n", $writer->asMarkdown());
    }

    public function test_check_unchecked()
    {
        $writer = new PhpMarkdownWriter;
        $writer->check('task one');
        $this->assertEquals("* [ ] task one\n", $writer->asMarkdown());
    }

    public function test_check_checked()
    {
        $writer = new PhpMarkdownWriter;
        $writer->check('task done', 0, true);
        $this->assertEquals("* [x] task done\n", $writer->asMarkdown());
    }

    public function test_check_with_indent()
    {
        $writer = new PhpMarkdownWriter;
        $writer->check('main task', 0, false);
        $writer->check('sub task done', 1, true);
        $this->assertEquals("* [ ] main task\n   * [x] sub task done\n", $writer->asMarkdown());
    }

    public function test_numbered()
    {
        $writer = new PhpMarkdownWriter;
        $writer->numbered('First item', 1);
        $this->assertEquals("1. First item\n", $writer->asMarkdown());
    }

    public function test_numbered_list()
    {
        $writer = new PhpMarkdownWriter;
        $writer->numbered('First', 1);
        $writer->numbered('Second', 2);
        $writer->numbered('Third', 3);
        $this->assertEquals("1. First\n2. Second\n3. Third\n", $writer->asMarkdown());
    }

    public function test_numbered_with_indent()
    {
        $writer = new PhpMarkdownWriter;
        $writer->numbered('Main item', 1, 0);
        $writer->numbered('Sub item', 1, 1);
        $this->assertEquals("1. Main item\n   1. Sub item\n", $writer->asMarkdown());
    }

    public function test_numbered_with_url_markup()
    {
        $writer = new PhpMarkdownWriter;
        $writer->numbered('Visit https://example.com', 1);
        $this->assertEquals("1. Visit [example.com](https://example.com)\n", $writer->asMarkdown());
    }

    public function test_paragraph()
    {
        $writer = new PhpMarkdownWriter;
        $writer->paragraph('This is a paragraph.');
        $this->assertEquals("This is a paragraph.\n\n", $writer->asMarkdown());
    }

    public function test_paragraph_continued()
    {
        $writer = new PhpMarkdownWriter;
        $writer->paragraph('Line one', true);
        $writer->paragraph('Line two', false);
        $this->assertEquals("Line one\nLine two\n\n", $writer->asMarkdown());
    }

    public function test_paragraph_with_url_markup()
    {
        $writer = new PhpMarkdownWriter;
        $writer->paragraph('Visit https://example.com for more info.');
        $this->assertEquals("Visit [example.com](https://example.com) for more info.\n\n", $writer->asMarkdown());
    }

    public function test_italic()
    {
        $writer = new PhpMarkdownWriter;
        $writer->italic('emphasized text');
        $this->assertEquals("*emphasized text*\n\n", $writer->asMarkdown());
    }

    public function test_italic_continued()
    {
        $writer = new PhpMarkdownWriter;
        $writer->italic('first', true);
        $writer->italic('second', false);
        $this->assertEquals("*first*\n*second*\n\n", $writer->asMarkdown());
    }

    public function test_bold()
    {
        $writer = new PhpMarkdownWriter;
        $writer->bold('test');
        $this->assertEquals("**test**\n\n", $writer->asMarkdown(), 'bold -> **');
    }

    public function test_bold_continued()
    {
        $writer = new PhpMarkdownWriter;
        $writer->bold('first', true);
        $writer->bold('second', false);
        $this->assertEquals("**first**\n**second**\n\n", $writer->asMarkdown());
    }

    public function test_strikethrough()
    {
        $writer = new PhpMarkdownWriter;
        $writer->strikethrough('deleted text');
        $this->assertEquals("~~deleted text~~\n\n", $writer->asMarkdown());
    }

    public function test_strikethrough_continued()
    {
        $writer = new PhpMarkdownWriter;
        $writer->strikethrough('first', true);
        $writer->strikethrough('second', false);
        $this->assertEquals("~~first~~\n~~second~~\n\n", $writer->asMarkdown());
    }

    public function test_blockquote()
    {
        $writer = new PhpMarkdownWriter;
        $writer->blockquote('This is a quote.');
        $this->assertEquals("> This is a quote.\n\n", $writer->asMarkdown());
    }

    public function test_blockquote_continued()
    {
        $writer = new PhpMarkdownWriter;
        $writer->blockquote('Line one of quote', true);
        $writer->blockquote('Line two of quote', false);
        $this->assertEquals("> Line one of quote\n> Line two of quote\n\n", $writer->asMarkdown());
    }

    public function test_blockquote_with_url_markup()
    {
        $writer = new PhpMarkdownWriter;
        $writer->blockquote('Source: https://example.com');
        $this->assertStringContainsString('> Source: [example.com](https://example.com)', $writer->asMarkdown());
    }

    public function test_link()
    {
        $writer = new PhpMarkdownWriter;
        $writer->link('Click here', 'https://example.com');
        $this->assertEquals('[Click here](https://example.com)', $writer->asMarkdown());
    }

    public function test_link_in_paragraph()
    {
        $writer = new PhpMarkdownWriter;
        $writer->paragraph('Check out ');
        $writer->link('this site', 'https://example.com');
        $writer->paragraph(' for more info.');
        $markdown = $writer->asMarkdown();
        $this->assertStringContainsString('[this site](https://example.com)', $markdown);
    }

    public function test_image()
    {
        $writer = new PhpMarkdownWriter;
        $writer->image('Alt text', 'https://example.com/image.png');
        $this->assertEquals("![Alt text](https://example.com/image.png)\n\n", $writer->asMarkdown());
    }

    public function test_image_with_title()
    {
        $writer = new PhpMarkdownWriter;
        $writer->image('Alt text', 'https://example.com/image.png', 'Image title');
        $this->assertEquals("![Alt text](https://example.com/image.png \"Image title\")\n\n", $writer->asMarkdown());
    }

    public function test_code_without_language()
    {
        $writer = new PhpMarkdownWriter;
        $writer->code('echo "hello";');
        $this->assertEquals("\n```\necho \"hello\";\n```\n", $writer->asMarkdown());
    }

    public function test_code_with_language()
    {
        $writer = new PhpMarkdownWriter;
        $writer->code('echo "hello";', 'php');
        $this->assertEquals("\n```php\necho \"hello\";\n```\n", $writer->asMarkdown());
    }

    public function test_code_multiline()
    {
        $writer = new PhpMarkdownWriter;
        $writer->code("line1\nline2\nline3", 'bash');
        $this->assertEquals("\n```bash\nline1\nline2\nline3\n```\n", $writer->asMarkdown());
    }

    public function test_fixed()
    {
        $writer = new PhpMarkdownWriter;
        $writer->fixed('fixed width text');
        $this->assertEquals("      fixed width text\n", $writer->asMarkdown());
    }

    public function test_fixed_multiple_lines()
    {
        $writer = new PhpMarkdownWriter;
        $writer->fixed('line one');
        $writer->fixed('line two');
        $this->assertEquals("      line one\n      line two\n", $writer->asMarkdown());
    }

    public function test_table_one_dimensional_without_headers()
    {
        $writer = new PhpMarkdownWriter;
        $writer->table(['alfa', 'beta'], false);
        $this->assertEquals("| alfa | beta |\n|------|------|\n", $writer->asMarkdown(), 'table -> | | |');
    }

    public function test_table_one_dimensional_with_headers()
    {
        $writer = new PhpMarkdownWriter;
        $writer->table(['col1' => 'val1', 'col2' => 'val2'], true);
        $this->assertEquals("| col1 | col2 |\n|------|------|\n| val1 | val2 |\n", $writer->asMarkdown());
    }

    public function test_table_two_dimensional_with_headers()
    {
        $writer = new PhpMarkdownWriter;
        $writer->table([
            ['name' => 'Peter', 'email' => 'peter@forret.com'],
            ['name' => 'John', 'email' => 'john@forret.com'],
        ], true);
        $this->assertEquals(
            "| name | email |\n|------|-------|\n| Peter | peter@forret.com |\n| John | john@forret.com |\n",
            $writer->asMarkdown(),
            'table -> | | |'
        );
    }

    public function test_table_two_dimensional_without_headers()
    {
        $writer = new PhpMarkdownWriter;
        $writer->table([
            ['Name', 'Email'],
            ['Peter', 'peter@forret.com'],
        ], false);
        $this->assertEquals(
            "| Name | Email |\n|------|-------|\n| Peter | peter@forret.com |\n",
            $writer->asMarkdown()
        );
    }

    public function test_table_header()
    {
        $writer = new PhpMarkdownWriter;
        $writer->table_header(['Column A', 'Column B', 'Column C']);
        $this->assertEquals(
            "| Column A | Column B | Column C |\n|----------|----------|----------|\n",
            $writer->asMarkdown()
        );
    }

    public function test_table_row()
    {
        $writer = new PhpMarkdownWriter;
        $writer->table_row(['value1', 'value2', 'value3']);
        $this->assertEquals("| value1 | value2 | value3 |\n", $writer->asMarkdown());
    }

    public function test_reset()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h1('Title');
        $writer->paragraph('Content');
        $this->assertNotEmpty($writer->asMarkdown());

        $writer->reset();
        $this->assertEquals('', $writer->asMarkdown());
    }

    public function test_reset_returns_self()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->reset();
        $this->assertSame($writer, $result);
    }

    public function test_constructor_with_filename()
    {
        $filename = $this->tempDir.'/test_constructor.md';
        $writer = new PhpMarkdownWriter($filename);
        $writer->h1('Test');
        $writer->paragraph('Content');

        // Destructor writes to file, so we need to unset
        unset($writer);

        $this->assertFileExists($filename);
        $content = file_get_contents($filename);
        $this->assertStringContainsString('# Test', $content);
        $this->assertStringContainsString('Content', $content);
    }

    public function test_constructor_creates_directory()
    {
        $newDir = $this->tempDir.'/newsubdir';
        $filename = $newDir.'/test.md';
        $writer = new PhpMarkdownWriter($filename);
        $writer->h1('Test');
        unset($writer);

        $this->assertDirectoryExists($newDir);
        $this->assertFileExists($filename);

        // Clean up
        unlink($filename);
        rmdir($newDir);
    }

    public function test_set_output()
    {
        $writer = new PhpMarkdownWriter;
        $filename = $this->tempDir.'/test_set_output.md';

        $result = $writer->setOutput($filename);
        $this->assertSame($writer, $result);

        $writer->h1('Title');
        unset($writer);

        $this->assertFileExists($filename);
        $content = file_get_contents($filename);
        $this->assertStringContainsString('# Title', $content);
    }

    public function test_set_output_creates_directory()
    {
        $writer = new PhpMarkdownWriter;
        $newDir = $this->tempDir.'/another_subdir';
        $filename = $newDir.'/test.md';

        $writer->setOutput($filename);
        $writer->paragraph('test');
        unset($writer);

        $this->assertDirectoryExists($newDir);

        // Clean up
        unlink($filename);
        rmdir($newDir);
    }

    public function test_as_html()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h1('Title');
        $writer->paragraph('A paragraph.');

        $html = $writer->asHtml();

        $this->assertStringContainsString('<h1>Title</h1>', $html);
        $this->assertStringContainsString('<p>A paragraph.</p>', $html);
    }

    public function test_as_html_with_formatted_content()
    {
        $writer = new PhpMarkdownWriter;
        $writer->bold('bold text');
        $writer->italic('italic text');

        $html = $writer->asHtml();

        $this->assertStringContainsString('<strong>bold text</strong>', $html);
        $this->assertStringContainsString('<em>italic text</em>', $html);
    }

    public function test_get_converter_config()
    {
        $writer = new PhpMarkdownWriter;
        $config = $writer->getConverterConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('html_input', $config);
        $this->assertArrayHasKey('allow_unsafe_links', $config);
        $this->assertEquals('allow', $config['html_input']);
    }

    public function test_set_converter_config()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->setConverterConfig(['html_input' => 'escape']);

        $this->assertSame($writer, $result);
        $config = $writer->getConverterConfig();
        $this->assertEquals('escape', $config['html_input']);
    }

    public function test_converter_config_html_escape()
    {
        $writer = new PhpMarkdownWriter;
        $writer->setConverterConfig(['html_input' => 'escape']);
        $writer->paragraph('<script>alert("xss")</script>');

        $html = $writer->asHtml();
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_converter_config_html_allow()
    {
        $writer = new PhpMarkdownWriter;
        $writer->setConverterConfig(['html_input' => 'allow']);
        $writer->paragraph('<div class="custom">Content</div>');

        $html = $writer->asHtml();
        $this->assertStringContainsString('<div class="custom">Content</div>', $html);
    }

    public function test_save_as_markdown()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h1('Save Test');
        $writer->paragraph('Content here.');

        $filename = $this->tempDir.'/saved.md';
        $result = $writer->saveAsMarkdown($filename);

        $this->assertSame($writer, $result);
        $this->assertFileExists($filename);
        $content = file_get_contents($filename);
        $this->assertStringContainsString('# Save Test', $content);
        $this->assertStringContainsString('Content here.', $content);
    }

    public function test_save_as_html()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h1('HTML Test');
        $writer->paragraph('Paragraph content.');

        $filename = $this->tempDir.'/saved.html';
        $result = $writer->saveAsHtml($filename);

        $this->assertSame($writer, $result);
        $this->assertFileExists($filename);
        $content = file_get_contents($filename);
        $this->assertStringContainsString('<h1>HTML Test</h1>', $content);
        $this->assertStringContainsString('<p>Paragraph content.</p>', $content);
    }

    public function test_save_as_pdf()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h1('PDF Test');
        $writer->paragraph('PDF content here.');

        $filename = $this->tempDir.'/saved.pdf';
        $result = $writer->saveAsPdf($filename);

        $this->assertSame($writer, $result);
        $this->assertFileExists($filename);

        // Verify it's a valid PDF
        $content = file_get_contents($filename);
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_get_pdf_config()
    {
        $writer = new PhpMarkdownWriter;
        $config = $writer->getPdfConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('format', $config);
        $this->assertArrayHasKey('default_font_size', $config);
        $this->assertEquals('A4', $config['format']);
    }

    public function test_set_pdf_config()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->setPdfConfig(['format' => 'Letter']);

        $this->assertSame($writer, $result);
        $config = $writer->getPdfConfig();
        $this->assertEquals('Letter', $config['format']);
    }

    public function test_save_as_pdf_with_custom_config()
    {
        $writer = new PhpMarkdownWriter;
        $writer->setPdfConfig([
            'format' => 'A5',
            'default_font_size' => 10,
        ]);
        $writer->h1('Custom PDF');
        $writer->paragraph('With custom settings.');

        $filename = $this->tempDir.'/custom.pdf';
        $writer->saveAsPdf($filename);

        $this->assertFileExists($filename);
        $content = file_get_contents($filename);
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_set_pdf_font_family()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->setPdfFontFamily('DejaVuSerif');

        $this->assertSame($writer, $result);
        $config = $writer->getPdfConfig();
        $this->assertEquals('DejaVuSerif', $config['default_font']);
    }

    public function test_set_pdf_font_size()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->setPdfFontSize(14);

        $this->assertSame($writer, $result);
        $config = $writer->getPdfConfig();
        $this->assertEquals(14, $config['default_font_size']);
    }

    public function test_set_pdf_font_size_float()
    {
        $writer = new PhpMarkdownWriter;
        $writer->setPdfFontSize(10.5);

        $config = $writer->getPdfConfig();
        $this->assertEquals(10.5, $config['default_font_size']);
    }

    public function test_save_as_pdf_with_custom_font()
    {
        $writer = new PhpMarkdownWriter;
        $writer->setPdfFontFamily('DejaVuSerif');
        $writer->setPdfFontSize(11);
        $writer->h1('Custom Font Test');
        $writer->paragraph('This uses a serif font.');

        $filename = $this->tempDir.'/custom_font.pdf';
        $writer->saveAsPdf($filename);

        $this->assertFileExists($filename);
        $content = file_get_contents($filename);
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_add_pdf_header()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->addPdfHeader('<div style="text-align: center;">Header Text</div>');

        $this->assertSame($writer, $result);
    }

    public function test_add_pdf_footer()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->addPdfFooter('<div style="text-align: center;">Page {PAGENO} of {nbpg}</div>');

        $this->assertSame($writer, $result);
    }

    public function test_save_as_pdf_with_header_and_footer()
    {
        $writer = new PhpMarkdownWriter;
        $writer->addPdfHeader('<div style="text-align: center; font-size: 10pt;">Document Header</div>');
        $writer->addPdfFooter('<div style="text-align: center; font-size: 9pt;">Page {PAGENO}</div>');
        $writer->h1('Test Document');
        $writer->paragraph('Content with header and footer.');

        $filename = $this->tempDir.'/with_header_footer.pdf';
        $writer->saveAsPdf($filename);

        $this->assertFileExists($filename);
        $content = file_get_contents($filename);
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_add_pdf_title()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->addPdfTitle('My Document Title');

        $this->assertSame($writer, $result);
    }

    public function test_add_pdf_author()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->addPdfAuthor('John Doe');

        $this->assertSame($writer, $result);
    }

    public function test_save_as_pdf_with_metadata()
    {
        $writer = new PhpMarkdownWriter;
        $writer->addPdfTitle('Test Report');
        $writer->addPdfAuthor('Test Author');
        $writer->h1('Report');
        $writer->paragraph('Content here.');

        $filename = $this->tempDir.'/with_metadata.pdf';
        $writer->saveAsPdf($filename);

        $this->assertFileExists($filename);
        $content = file_get_contents($filename);
        $this->assertStringStartsWith('%PDF', $content);
        // PDF metadata is embedded in the file (may be hex-encoded)
        $this->assertGreaterThan(1000, strlen($content));
    }

    public function test_add_pdf_subject()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->addPdfSubject('Monthly Sales Report');

        $this->assertSame($writer, $result);
    }

    public function test_add_pdf_keywords()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->addPdfKeywords('sales, report, monthly, 2025');

        $this->assertSame($writer, $result);
    }

    public function test_add_pdf_watermark()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->addPdfWatermark('DRAFT');

        $this->assertSame($writer, $result);
    }

    public function test_save_as_pdf_with_watermark()
    {
        $writer = new PhpMarkdownWriter;
        $writer->addPdfWatermark('CONFIDENTIAL');
        $writer->h1('Secret Document');
        $writer->paragraph('This is confidential content.');

        $filename = $this->tempDir.'/watermarked.pdf';
        $writer->saveAsPdf($filename);

        $this->assertFileExists($filename);
        $content = file_get_contents($filename);
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_save_as_pdf_with_full_metadata()
    {
        $writer = new PhpMarkdownWriter;
        $writer->addPdfTitle('Annual Report');
        $writer->addPdfAuthor('Finance Team');
        $writer->addPdfSubject('Financial Overview 2025');
        $writer->addPdfKeywords('finance, annual, report, 2025');
        $writer->h1('Annual Report');
        $writer->paragraph('Financial summary.');

        $filename = $this->tempDir.'/full_metadata.pdf';
        $writer->saveAsPdf($filename);

        $this->assertFileExists($filename);
        $content = file_get_contents($filename);
        $this->assertStringStartsWith('%PDF', $content);
        $this->assertGreaterThan(1000, strlen($content));
    }

    public function test_fluent_chaining()
    {
        $writer = new PhpMarkdownWriter;

        $result = $writer
            ->h1('Document Title')
            ->paragraph('Introduction')
            ->h2('Section 1')
            ->bullet('Item 1')
            ->bullet('Item 2', 1)
            ->h2('Section 2')
            ->code('echo "test";', 'php')
            ->h3('Subsection')
            ->check('Todo item', 0, false)
            ->check('Done item', 0, true)
            ->bold('Important')
            ->italic('Note');

        $this->assertSame($writer, $result);

        $markdown = $writer->asMarkdown();
        $this->assertStringContainsString('# Document Title', $markdown);
        $this->assertStringContainsString('## Section 1', $markdown);
        $this->assertStringContainsString('* Item 1', $markdown);
        $this->assertStringContainsString('```php', $markdown);
        $this->assertStringContainsString('* [ ] Todo item', $markdown);
        $this->assertStringContainsString('* [x] Done item', $markdown);
    }

    public function test_as_markdown_returns_string()
    {
        $writer = new PhpMarkdownWriter;
        $writer->h1('Test');

        $this->assertIsString($writer->asMarkdown());
    }

    public function test_empty_writer()
    {
        $writer = new PhpMarkdownWriter;
        $this->assertEquals('', $writer->asMarkdown());
    }

    public function test_markup_plain_text_unchanged()
    {
        $writer = new PhpMarkdownWriter;
        $this->assertEquals('plain text without links', $writer->markup('plain text without links'));
    }

    public function test_markup_complex_url()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->markup('https://example.com/path?query=value&other=123');
        $this->assertStringContainsString('[example.com/path?query=value&other=123]', $result);
        $this->assertStringContainsString('(https://example.com/path?query=value&other=123)', $result);
    }

    public function test_inline_code()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->inlineCode('variable');
        $this->assertEquals('`variable`', $result);
    }

    public function test_inline_code_in_paragraph()
    {
        $writer = new PhpMarkdownWriter;
        $writer->paragraph('Use the '.$writer->inlineCode('print()').' function.');
        $this->assertEquals("Use the `print()` function.\n\n", $writer->asMarkdown());
    }

    public function test_inline_code_with_special_chars()
    {
        $writer = new PhpMarkdownWriter;
        $result = $writer->inlineCode('array[0]');
        $this->assertEquals('`array[0]`', $result);
    }
}

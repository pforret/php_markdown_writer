<?php

namespace Pforret\PhpMarkdownWriter;

use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class PhpMarkdownWriter
{
    /**
     * @var false|resource
     */
    private $fp;

    private string $markdown = '';

    /**
     * @var array<string, mixed>
     */
    private array $converterConfig = [
        'html_input' => 'allow',           // 'strip', 'allow', or 'escape'
        'allow_unsafe_links' => false,     // Allow javascript:, vbscript:, etc.
        'max_nesting_level' => PHP_INT_MAX, // Maximum block nesting level
        'renderer' => [
            'block_separator' => "\n",
            'inner_separator' => "\n",
            'soft_break' => "\n",
        ],
    ];

    /**
     * @var array<string, mixed>
     */
    private array $pdfConfig = [
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font_size' => 12,
        'default_font' => 'DejaVuSans',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 16,
        'margin_bottom' => 16,
        'margin_header' => 9,
        'margin_footer' => 9,
    ];

    private string $pdfHeader = '';

    private string $pdfFooter = '';

    private string $pdfTitle = '';

    private string $pdfAuthor = '';

    private string $pdfSubject = '';

    private string $pdfKeywords = '';

    private string $pdfWatermark = '';

    public function __construct(string $filename = '')
    {
        if ($filename) {
            $folder = dirname($filename);
            if (! is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            $this->setOutput($filename);
        }
    }

    public function reset(): static
    {
        $this->markdown = '';
        $this->fp = false;

        return $this;
    }

    public function setOutput(string $filename): static
    {
        $parentFolder = dirname($filename);
        if (! is_dir($parentFolder)) {
            mkdir($parentFolder, 0777, true);
        }
        $this->fp = fopen($filename, 'w');

        return $this;
    }

    // formatted output

    public function h1(string $text): static
    {
        $this->add("\n# $text\n\n");

        return $this;
    }

    public function h2(string $text): static
    {
        $this->add("\n## $text\n\n");

        return $this;
    }

    public function h3(string $text): static
    {
        $this->add("\n### $text\n\n");

        return $this;
    }

    public function h4(string $text): static
    {
        $this->add("\n#### $text\n\n");

        return $this;
    }

    public function h5(string $text): static
    {
        $this->add("\n##### $text\n\n");

        return $this;
    }

    public function h6(string $text): static
    {
        $this->add("\n###### $text\n\n");

        return $this;
    }

    public function hr(): static
    {
        $this->add("\n---\n\n");

        return $this;
    }

    public function pagebreak(): static
    {
        $this->add("\n<pagebreak />\n\n");

        return $this;
    }

    public function bullet(string $text, int $indent = 0): static
    {
        $this->add(str_repeat('   ', $indent).'* '.$this->markup($text)."\n");

        return $this;
    }

    public function check(string $text, int $indent = 0, bool $done = false): static
    {
        $prefix = $done ? '* [x] ' : '* [ ] ';
        $this->add(str_repeat('   ', $indent).$prefix.$this->markup($text)."\n");

        return $this;
    }

    public function numbered(string $text, int $number = 1, int $indent = 0): static
    {
        $this->add(str_repeat('   ', $indent).$number.'. '.$this->markup($text)."\n");

        return $this;
    }

    public function paragraph(string $text, bool $continued = false): static
    {
        $eol = $continued ? "\n" : "\n\n";
        $this->add($this->markup($text).$eol);

        return $this;
    }

    public function italic(string $text, bool $continued = false): static
    {
        $eol = $continued ? "\n" : "\n\n";
        $this->add('*'.trim($this->markup($text)).'*'.$eol);

        return $this;
    }

    public function bold(string $text, bool $continued = false): static
    {
        $eol = $continued ? "\n" : "\n\n";
        $this->add('**'.trim($this->markup($text)).'**'.$eol);

        return $this;
    }

    public function strikethrough(string $text, bool $continued = false): static
    {
        $eol = $continued ? "\n" : "\n\n";
        $this->add('~~'.trim($this->markup($text)).'~~'.$eol);

        return $this;
    }

    public function blockquote(string $text, bool $continued = false): static
    {
        $eol = $continued ? "\n" : "\n\n";
        $this->add('> '.$this->markup($text).$eol);

        return $this;
    }

    public function link(string $text, string $url): static
    {
        $this->add('['.$text.']('.$url.')');

        return $this;
    }

    public function image(string $alt, string $url, string $title = ''): static
    {
        if ($title) {
            $this->add('!['.$alt.']('.$url.' "'.$title.'")'."\n\n");
        } else {
            $this->add('!['.$alt.']('.$url.')'."\n\n");
        }

        return $this;
    }

    public function code(string $text, string $language = ''): static
    {
        $this->add("\n```$language\n$text\n```\n");

        return $this;
    }

    public function fixed(string $text): static
    {
        $this->add("      $text\n");

        return $this;
    }

    public function table_header(array $array): static
    {
        $line = '';
        foreach ($array as $cell) {
            $line .= "| $cell ";
        }
        $line .= "|\n";
        $this->add($line);

        $line = '';
        // line underneath  |---|---|
        foreach ($array as $cell) {
            $cell = str_repeat('-', strlen($cell) + 2);
            $line .= "|$cell";
        }
        $line .= "|\n";
        $this->add($line);

        return $this;
    }

    public function table_row(array $array): static
    {
        $line = '';
        foreach ($array as $cell) {
            $line .= "| $cell ";
        }
        $line .= "|\n";
        $this->add($line);

        return $this;
    }

    public function table(array $table, bool $with_headers = true): static
    {
        $first_element = current($table);
        if (is_array($first_element)) {
            // 2 or more dimensional table
            $row_number = 0;
            foreach ($table as $row) {
                $row_number++;
                if ($row_number == 1) {
                    if ($with_headers) {
                        $this->table_header(array_keys($row));
                        $this->table_row(array_values($row));
                    } else {
                        $this->table_header(array_values($row));
                    }
                } else {
                    $this->table_row(array_values($row));
                }
            }
        } else {
            // 1-dimensional table -- just a row
            if ($with_headers) {
                $this->table_header(array_keys($table));
                $this->table_row(array_values($table));
            } else {
                $this->table_header(array_values($table));
            }
        }

        return $this;
    }

    public function asMarkdown(): string
    {
        return $this->markdown;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConverterConfig(): array
    {
        return $this->converterConfig;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function setConverterConfig(array $config): static
    {
        $this->converterConfig = array_merge($this->converterConfig, $config);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPdfConfig(): array
    {
        return $this->pdfConfig;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function setPdfConfig(array $config): static
    {
        $this->pdfConfig = array_merge($this->pdfConfig, $config);

        return $this;
    }

    /**
     * Set PDF default font family.
     * Common fonts: DejaVuSans, DejaVuSerif, FreeSans, FreeSerif, Arial, Times, Courier
     */
    public function setPdfFontFamily(string $font): static
    {
        $this->pdfConfig['default_font'] = $font;

        return $this;
    }

    /**
     * Set PDF default font size in points.
     */
    public function setPdfFontSize(int|float $size): static
    {
        $this->pdfConfig['default_font_size'] = $size;

        return $this;
    }

    /**
     * Set PDF header HTML. Supports mPDF variables like {PAGENO}, {DATE}, {nbpg}.
     */
    public function addPdfHeader(string $html): static
    {
        $this->pdfHeader = $html;

        return $this;
    }

    /**
     * Set PDF footer HTML. Supports mPDF variables like {PAGENO}, {DATE}, {nbpg}.
     */
    public function addPdfFooter(string $html): static
    {
        $this->pdfFooter = $html;

        return $this;
    }

    /**
     * Set PDF document title (metadata).
     */
    public function addPdfTitle(string $title): static
    {
        $this->pdfTitle = $title;

        return $this;
    }

    /**
     * Set PDF document author (metadata).
     */
    public function addPdfAuthor(string $author): static
    {
        $this->pdfAuthor = $author;

        return $this;
    }

    /**
     * Set PDF document subject (metadata).
     */
    public function addPdfSubject(string $subject): static
    {
        $this->pdfSubject = $subject;

        return $this;
    }

    /**
     * Set PDF document keywords (metadata).
     */
    public function addPdfKeywords(string $keywords): static
    {
        $this->pdfKeywords = $keywords;

        return $this;
    }

    /**
     * Set PDF watermark text displayed on all pages.
     */
    public function addPdfWatermark(string $text): static
    {
        $this->pdfWatermark = $text;

        return $this;
    }

    /**
     * @throws CommonMarkException
     */
    public function asHtml(): string
    {
        $converter = new GithubFlavoredMarkdownConverter($this->converterConfig);

        return (string) $converter->convert($this->markdown);
    }

    public function saveAsMarkdown(string $filename): static
    {
        file_put_contents($filename, $this->markdown);

        return $this;
    }

    /**
     * @throws CommonMarkException
     */
    public function saveAsHtml(string $filename): static
    {
        file_put_contents($filename, $this->asHtml());

        return $this;
    }

    /**
     * @throws CommonMarkException
     * @throws MpdfException
     */
    public function saveAsPdf(string $filename): static
    {
        $mpdf = new Mpdf($this->pdfConfig);

        // Set metadata
        if ($this->pdfTitle !== '') {
            $mpdf->SetTitle($this->pdfTitle);
        }
        if ($this->pdfAuthor !== '') {
            $mpdf->SetAuthor($this->pdfAuthor);
        }
        if ($this->pdfSubject !== '') {
            $mpdf->SetSubject($this->pdfSubject);
        }
        if ($this->pdfKeywords !== '') {
            $mpdf->SetKeywords($this->pdfKeywords);
        }

        // Set header and footer
        if ($this->pdfHeader !== '') {
            $mpdf->SetHTMLHeader($this->pdfHeader);
        }
        if ($this->pdfFooter !== '') {
            $mpdf->SetHTMLFooter($this->pdfFooter);
        }

        // Set watermark
        if ($this->pdfWatermark !== '') {
            $mpdf->SetWatermarkText($this->pdfWatermark);
            $mpdf->showWatermarkText = true;
        }

        $mpdf->WriteHTML($this->asHtml());
        $mpdf->Output($filename, 'F');

        return $this;
    }

    public function __destruct()
    {
        // clean up
        if ($this->fp) {
            fclose($this->fp);
        }
    }

    // ----------------------------------
    // helper methods

    public function inlineCode(string $text): string
    {
        return '`'.$text.'`';
    }

    public function markup(string $text): string
    {
        // markup urls
        $markup = preg_replace("|http://([a-zA-Z0-9/_\-.?=&:%]*)|", '[$1]($0)', $text);
        $markup = preg_replace("|https://([a-zA-Z0-9/_\-.?=&:%]*)|", '[$1]($0)', $markup);
        // markup email addresses
        $markup = preg_replace("|([\w_\-.]+@[\w_\-.]+\.[a-z][a-z]+)|", '[$1](mailto:$1)', $markup);

        return preg_replace("|ftp://([a-zA-Z0-9/_\-.?=]*)|", '[$1]($0)', $markup);
    }

    private function add($line): void
    {
        $this->markdown .= $line;
        if ($this->fp) {
            fwrite($this->fp, $line);
        }
    }
}

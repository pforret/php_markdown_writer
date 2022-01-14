<?php
namespace Pforret\PhpMarkdownWriter;

class PhpMarkdownWriter
{
    private bool $enabled = false;
    /**
     * @var false|resource
     */
    private $fp;


    public function __construct(string $filename = "")
    {
        if ($filename) {
            $this->setOutput($filename);
        }

        return $this;
    }

    public function setOutput($filename)
    {
        try {
            $this->fp = fopen($filename, "w");
        } catch (WriterException $e) {
            print($e->getMessage());
        }
        if ($this->fp) {
            $this->enabled = true;
        }
    }

    // formatted output

    public function h1($text): PhpMarkdownWriter
    {
        if (! $this->enabled) {
            return $this;
        }
        fwrite($this->fp, "# $text\n");

        return $this;
    }

    public function h2($text): PhpMarkdownWriter
    {
        if (! $this->enabled) {
            return $this;
        }
        fwrite($this->fp, "## $text\n");

        return $this;
    }

    public function h3($text): PhpMarkdownWriter
    {
        if (! $this->enabled) {
            return $this;
        }
        fwrite($this->fp, "### $text\n");

        return $this;
    }

    public function h4($text): PhpMarkdownWriter
    {
        if (! $this->enabled) {
            return $this;
        }
        fwrite($this->fp, "#### $text\n");

        return $this;
    }

    public function bullet($text, $indent = 0): PhpMarkdownWriter
    {
        if (! $this->enabled) {
            return $this;
        }
        $prefix = str_repeat("   ", $indent);
        fwrite($this->fp, $prefix . "* " . $this->markup($text) ."\n");

        return $this;
    }

    public function paragraph($text, $continued = false): PhpMarkdownWriter
    {
        if (! $this->enabled) {
            return $this;
        }
        $eol = "\n";
        if (! $continued) {
            $eol .= "\n";
        }
        fwrite($this->fp, $this->markup($text) . $eol);

        return $this;
    }

    public function code($text, $language = ""): PhpMarkdownWriter
    {
        if (! $this->enabled) {
            return $this;
        }
        fwrite($this->fp, "```$language\n$text\n```\n");

        return $this;
    }

    public function __destruct()
    {
        // clean up
        if ($this->fp) {
            fclose($this->fp);
        }
    }

    // internal stuff
    public function markup($text)
    {
        $markup = preg_replace("|http://([a-zA-Z0-9/_\-\.\?=]*)|", "[\$1](\$0)", $text);
        $markup = preg_replace("|https://([a-zA-Z0-9/_\-\.\?=]*)|", "[\$1](\$0)", $markup);

        return preg_replace("|ftp://([a-zA-Z0-9/_\-\.\?=]*)|", "[\$1](\$0)", $markup);
    }
}

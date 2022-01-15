<?php

namespace Pforret\PhpMarkdownWriter;

class PhpMarkdownWriter
{
    /**
     * @var false|resource
     */
    private $fp;
    private string $markdown = "";

    public function __construct(string $filename = "")
    {
        if ($filename) {
            $this->setOutput($filename);
        }

        return $this;
    }

    public function reset(): PhpMarkdownWriter
    {
        $this->markdown = "";
        $this->fp = false;
        return $this;
    }

    public function setOutput($filename)
    {
        $this->fp = fopen($filename, "w");
    }

    // formatted output

    public function h1($text): PhpMarkdownWriter
    {
        $this->add("\n# $text\n");
        return $this;
    }

    public function h2($text): PhpMarkdownWriter
    {
        $this->add("\n## $text\n");
        return $this;
    }

    public function h3($text): PhpMarkdownWriter
    {
        $this->add("\n### $text\n");
        return $this;
    }

    public function h4($text): PhpMarkdownWriter
    {
        $this->add("\n#### $text\n");
        return $this;
    }

    public function bullet($text, $indent = 0): PhpMarkdownWriter
    {
        $this->add(str_repeat("   ", $indent) . "* " . $this->markup($text) . "\n");
        return $this;
    }

    public function check($text, $done = false): PhpMarkdownWriter
    {
        $prefix = $done ? "[x] " : "[ ] ";
        $this->add($prefix . $this->markup($text) . "\n");
        return $this;
    }

    public function paragraph($text, $continued = false): PhpMarkdownWriter
    {
        $eol = $continued ? "\n" : "\n\n";
        $this->add($this->markup($text) . $eol);
        return $this;
    }

    public function code($text, $language = ""): PhpMarkdownWriter
    {
        $this->add("\n```$language\n$text\n```\n");
        return $this;
    }

    public function fixed($text): PhpMarkdownWriter
    {
        $this->add("    $text\n");
        return $this;
    }

    public function table($table, $with_headers = true): PhpMarkdownWriter
    {
        $first_element = current($table);
        if (is_array($first_element)) {
            // 2 or more dimensional table
            if ($with_headers) {
                $line = "|";
                foreach ($first_element as $key => $val) {
                    $line .= " $key |";
                }
                $line .= "\n";
                $this->add($line);
            }
            foreach ($table as $id => $row) {
                $line = "|";
                foreach ($row as $key => $val) {
                    $line .= " $val |";
                }
                $line .= "\n";
                $this->add($line);
            }
        } else {
            // 1-dimensional table
            if ($with_headers) {
                foreach ($table as $key => $val) {
                    $this->add("| $key | $val |\n");
                }
            } else {
                foreach ($table as $key => $val) {
                    $this->add("| $val |\n");
                }
            }
        }
        return $this;
    }

    public function asMarkdown(): string
    {
        return $this->markdown;
    }

    public function __destruct()
    {
        // clean up
        if ($this->fp) {
            fclose($this->fp);
        }
    }

    // ----------------------------------
    // internal stuff

    public function markup($text)
    {
        $markup = preg_replace("|http://([a-zA-Z0-9/_\-\.\?=]*)|", "[\$1](\$0)", $text);
        $markup = preg_replace("|https://([a-zA-Z0-9/_\-\.\?=]*)|", "[\$1](\$0)", $markup);
        return preg_replace("|ftp://([a-zA-Z0-9/_\-\.\?=]*)|", "[\$1](\$0)", $markup);
    }

    private function add($line)
    {
        $this->markdown .= $line;
        if ($this->fp) {
            fwrite($this->fp, $line);
        }
    }
}

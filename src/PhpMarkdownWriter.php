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
            $folder = dirname($filename);
            if(!is_dir($folder)){
                mkdir($folder,0777,true);
            }
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
        $prefix = $done ? "* [x] " : "* [ ] ";
        $this->add($prefix . $this->markup($text) . "\n");

        return $this;
    }

    public function paragraph($text, $continued = false): PhpMarkdownWriter
    {
        $eol = $continued ? "\n" : "\n\n";
        $this->add($this->markup($text) . $eol);

        return $this;
    }

    public function italic($text, $continued = false): PhpMarkdownWriter
    {
        $eol = $continued ? "\n" : "\n\n";
        $this->add("*" .trim($this->markup($text)) ."*" . $eol);

        return $this;
    }

    public function bold($text, $continued = false): PhpMarkdownWriter
    {
        $eol = $continued ? "\n" : "\n\n";
        $this->add("**" .trim($this->markup($text)) ."**" . $eol);

        return $this;
    }

    public function code($text, $language = ""): PhpMarkdownWriter
    {
        $this->add("\n```$language\n$text\n```\n");

        return $this;
    }

    public function fixed($text): PhpMarkdownWriter
    {
        $this->add("      $text\n");

        return $this;
    }

    public function table_header($array)
    {
        $line = "";
        foreach ($array as $cell) {
            $line .= "| $cell ";
        }
        $line .= "|\n";
        $this->add($line);

        $line = "";
        // line underneath  |---|---|
        foreach ($array as $cell) {
            $cell = str_repeat("-", strlen($cell) + 2);
            $line .= "|$cell";
        }
        $line .= "|\n";
        $this->add($line);
    }

    public function table_row($array)
    {
        $line = "";
        foreach ($array as $cell) {
            $line .= "| $cell ";
        }
        $line .= "|\n";
        $this->add($line);
    }
    
    public function table($table, $with_headers = true): PhpMarkdownWriter
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
        $markup = preg_replace("|http://([a-zA-Z0-9/_\-\.\?=&:%]*)|", "[\$1](\$0)", $text);
        $markup = preg_replace("|https://([a-zA-Z0-9/_\-\.\?=&:%]*)|", "[\$1](\$0)", $markup);
        $markup = preg_replace("|([\w_\-.]+@[\w_\-.]+\.[a-z][a-z]+)|", "[\$1](mailto:\$1)", $markup);

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

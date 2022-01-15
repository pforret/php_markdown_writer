<?php

namespace Pforret\PhpMarkdownWriter\Tests;

use Pforret\PhpMarkdownWriter\PhpMarkdownWriter;
use PHPUnit\Framework\TestCase;

class PhpMarkdownWriterTest extends TestCase
{
    public function testMarkup()
    {
        $writer = new PhpMarkdownWriter();
        $this->assertEquals("this is a [www.google.com](https://www.google.com) link", $writer->markup("this is a https://www.google.com link"));
    }

    public function testH1()
    {
        $writer = new PhpMarkdownWriter();
        $writer->h1("test");
        $this->assertEquals("\n# test\n",$writer->asMarkdown(),"h1 -> #");
    }
    public function testH2()
    {
        $writer = new PhpMarkdownWriter();
        $writer->h2("test");
        $this->assertEquals("\n## test\n",$writer->asMarkdown(),"h1 -> #");
    }
    public function testH3()
    {
        $writer = new PhpMarkdownWriter();
        $writer->h3("test");
        $this->assertEquals("\n### test\n",$writer->asMarkdown(),"h1 -> #");
    }
    public function testH4()
    {
        $writer = new PhpMarkdownWriter();
        $writer->h4("test");
        $this->assertEquals("\n#### test\n",$writer->asMarkdown(),"h1 -> #");
    }
}

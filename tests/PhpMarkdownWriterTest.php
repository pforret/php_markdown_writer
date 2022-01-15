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
        $this->assertEquals("\n# test\n", $writer->asMarkdown(), "h1 -> #");
    }

    public function testH2()
    {
        $writer = new PhpMarkdownWriter();
        $writer->h2("test");
        $this->assertEquals("\n## test\n",$writer->asMarkdown(),"h2 -> ##");
    }

    public function testH3()
    {
        $writer = new PhpMarkdownWriter();
        $writer->h3("test");
        $this->assertEquals("\n### test\n",$writer->asMarkdown(),"h3 -> ###");
    }

    public function testH4()
    {
        $writer = new PhpMarkdownWriter();
        $writer->h4("test");
        $this->assertEquals("\n#### test\n",$writer->asMarkdown(),"h4 -> ####");
    }

    public function testTable(){
        $writer = new PhpMarkdownWriter();
        $writer->table(["alfa","beta"],false);
        $this->assertEquals("| alfa |\n| beta |\n",$writer->asMarkdown(),"table -> | | |");
        $writer->reset();
        $writer->table([ ["name" => "Peter", "email" => "peter@forret.com" ],["name" => "John", "email" => "john@forret.com" ] ],true);
        $this->assertEquals(
            "| name | email |\n| Peter | peter@forret.com |\n| John | john@forret.com |\n",
            $writer->asMarkdown(),
            "table -> | | |");
    }
}

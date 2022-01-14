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
}

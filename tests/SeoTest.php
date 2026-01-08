<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Modules\SEO;

class SeoTest extends TestCase
{
    public function testGenerateArticle()
    {
        $seo = new SEO();
        $article = $seo->generateArticle('php');
        $this->assertArrayHasKey('title', $article);
        $this->assertArrayHasKey('description', $article);
        $this->assertArrayHasKey('content', $article);
    }

    public function testGenerateMeta()
    {
        $seo = new SEO();
        $meta = $seo->generateMeta('php');
        $this->assertArrayHasKey('title', $meta);
        $this->assertArrayHasKey('description', $meta);
    }
}

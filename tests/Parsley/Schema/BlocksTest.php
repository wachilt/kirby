<?php

namespace Kirby\Parsley\Schema;

use Kirby\Parsley\Element;
use Kirby\Toolkit\Dom;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Kirby\Parsley\Schema\Blocks
 */
class BlocksTest extends TestCase
{
    protected $schema;

    public function setUp(): void
    {
        $this->schema = new Blocks();
    }

    protected function element($html, $query)
    {
        $dom  = new Dom($html);
        $node = $dom->query($query)[0];
        return new Element($node);
    }

    public function testBlockquote()
    {
        $html = <<<HTML
            <blockquote>
                Test
            </blockquote>
        HTML;

        $element  = $this->element($html, '//blockquote');
        $expected = [
            'content' => [
                'citation' => null,
                'text'     => 'Test'
            ],
            'type' => 'quote',
        ];

        return $this->assertSame($expected, $this->schema->blockquote($element));
    }

    public function testBlockquoteWithMarks()
    {
        $html = <<<HTML
            <blockquote>
                <p><b>Bold</b> <i>Italic</i></p>
            </blockquote>
        HTML;

        $element  = $this->element($html, '//blockquote');
        $expected = [
            'content' => [
                'citation' => null,
                'text'     => '<b>Bold</b> <i>Italic</i>'
            ],
            'type' => 'quote',
        ];

        return $this->assertSame($expected, $this->schema->blockquote($element));
    }

    public function testBlockquoteWithParagraphs()
    {
        $html = <<<HTML
            <blockquote>
                <p>A</p>
                <p>B</p>
            </blockquote>
        HTML;

        $element  = $this->element($html, '//blockquote');
        $expected = [
            'content' => [
                'citation' => null,
                'text'     => 'A<br /><br />B'
            ],
            'type' => 'quote',
        ];

        return $this->assertSame($expected, $this->schema->blockquote($element));
    }

    public function testBlockquoteWithFooter()
    {
        $html = <<<HTML
            <blockquote>
                Test
                <footer>Albert Einstein</footer>
            </blockquote>
        HTML;

        $element  = $this->element($html, '//blockquote');
        $expected = [
            'content' => [
                'citation' => 'Albert Einstein',
                'text'     => 'Test'
            ],
            'type' => 'quote',
        ];

        return $this->assertSame($expected, $this->schema->blockquote($element));
    }

    /**
     * @covers ::fallback
     */
    public function testFallback()
    {
        $expected = [
            'content' => [
                'text' => '<p>Test</p>'
            ],
            'type' => 'text',
        ];

        return $this->assertSame($expected, $this->schema->fallback('Test'));
    }

    public function testHeading()
    {
        $html = <<<HTML
            <h1>
                Test
            </h1>
        HTML;

        $element  = $this->element($html, '//h1');
        $expected = [
            'content' => [
                'level' => 'h1',
                'text'  => 'Test'
            ],
            'type' => 'heading',
        ];

        return $this->assertSame($expected, $this->schema->heading($element));
    }

    public function headingLevels()
    {
        return [
            ['h1'], ['h2'], ['h3'], ['h4'], ['h5'], ['h6']
        ];
    }

    /**
     * @dataProvider headingLevels
     */
    public function testHeadingLevel($level)
    {
        $html = <<<HTML
            <$level>
                Test
            </$level>
        HTML;

        $element  = $this->element($html, '//' . $level);
        $expected = [
            'content' => [
                'level' => $level,
                'text'  => 'Test'
            ],
            'type' => 'heading',
        ];

        return $this->assertSame($expected, $this->schema->heading($element));
    }

    public function testHeadingId()
    {
        $html = <<<HTML
            <h1 id="test">
                Test
            </h1>
        HTML;

        $element  = $this->element($html, '//h1');
        $expected = [
            'content' => [
                'id'    => 'test',
                'level' => 'h1',
                'text'  => 'Test'
            ],
            'type' => 'heading',
        ];

        return $this->assertSame($expected, $this->schema->heading($element));
    }

    public function testIframe()
    {
        $html = <<<HTML
            <iframe src="https://getkirby.com"></iframe>
        HTML;

        $element  = $this->element($html, '//iframe');
        $expected = [
            'content' => [
                'text' => '<iframe src="https://getkirby.com"></iframe>'
            ],
            'type' => 'markdown',
        ];

        return $this->assertSame($expected, $this->schema->iframe($element));
    }

    public function testIframeWithVimeoVideo()
    {
        $html = <<<HTML
            <iframe src="https://player.vimeo.com/video/1"></iframe>
        HTML;

        $element  = $this->element($html, '//iframe');
        $expected = [
            'content' => [
                'caption' => null,
                'url'     => 'https://vimeo.com/1'
            ],
            'type' => 'video',
        ];

        return $this->assertSame($expected, $this->schema->iframe($element));
    }

    public function testIframeWithYoutubeVideo()
    {
        $html = <<<HTML
            <iframe src="https://youtube.com/embed/1"></iframe>
        HTML;

        $element  = $this->element($html, '//iframe');
        $expected = [
            'content' => [
                'caption' => null,
                'url'     => 'https://youtube.com/watch?v=1'
            ],
            'type' => 'video',
        ];

        return $this->assertSame($expected, $this->schema->iframe($element));
    }

    public function testIframeWithYoutubeNoCookieVideo()
    {
        $html = <<<HTML
            <iframe src="https://youtube-nocookie.com/embed/1"></iframe>
        HTML;

        $element  = $this->element($html, '//iframe');
        $expected = [
            'content' => [
                'caption' => null,
                'url'     => 'https://youtube.com/watch?v=1'
            ],
            'type' => 'video',
        ];

        return $this->assertSame($expected, $this->schema->iframe($element));
    }

    public function testIframeWithCaption()
    {
        $html = <<<HTML
            <figure>
                <iframe src="https://youtube.com/embed/1"></iframe>
                <figcaption>Test</figcaption>
            </figure>
        HTML;

        $element  = $this->element($html, '//iframe');
        $expected = [
            'content' => [
                'caption' => 'Test',
                'url'     => 'https://youtube.com/watch?v=1'
            ],
            'type' => 'video',
        ];

        return $this->assertSame($expected, $this->schema->iframe($element));
    }

    public function testIframeWithCaptionAndMarks()
    {
        $html = <<<HTML
            <figure>
                <iframe src="https://youtube.com/embed/1"></iframe>
                <figcaption><b>Bold</b><i>Italic</i></figcaption>
            </figure>
        HTML;

        $element  = $this->element($html, '//iframe');
        $expected = [
            'content' => [
                'caption' => '<b>Bold</b><i>Italic</i>',
                'url'     => 'https://youtube.com/watch?v=1'
            ],
            'type' => 'video',
        ];

        return $this->assertSame($expected, $this->schema->iframe($element));
    }

    public function testImg()
    {
        $html = <<<HTML
            <img src="https://getkirby.com/image.jpg">
        HTML;

        $element  = $this->element($html, '//img');
        $expected = [
            'content' => [
                'alt'      => null,
                'caption'  => null,
                'link'     => null,
                'location' => 'web',
                'src'      => 'https://getkirby.com/image.jpg'
            ],
            'type' => 'image',
        ];

        return $this->assertSame($expected, $this->schema->img($element));
    }

    public function testImgWithAlt()
    {
        $html = <<<HTML
            <img src="https://getkirby.com/image.jpg" alt="Test">
        HTML;

        $element  = $this->element($html, '//img');
        $expected = [
            'content' => [
                'alt'      => 'Test',
                'caption'  => null,
                'link'     => null,
                'location' => 'web',
                'src'      => 'https://getkirby.com/image.jpg'
            ],
            'type' => 'image',
        ];

        return $this->assertSame($expected, $this->schema->img($element));
    }

    public function testImgWithLink()
    {
        $html = <<<HTML
            <a href="https://getkirby.com">
                <img src="https://getkirby.com/image.jpg" alt="Test">
            </a>
        HTML;

        $element  = $this->element($html, '//img');
        $expected = [
            'content' => [
                'alt'      => 'Test',
                'caption'  => null,
                'link'     => 'https://getkirby.com',
                'location' => 'web',
                'src'      => 'https://getkirby.com/image.jpg'
            ],
            'type' => 'image',
        ];

        return $this->assertSame($expected, $this->schema->img($element));
    }

    public function testImgWithCaption()
    {
        $html = <<<HTML
            <figure>
                <img src="https://getkirby.com/image.jpg" alt="Test">
                <figcaption>Test</figcaption>
            </figure>
        HTML;

        $element  = $this->element($html, '//img');
        $expected = [
            'content' => [
                'alt'      => 'Test',
                'caption'  => 'Test',
                'link'     => null,
                'location' => 'web',
                'src'      => 'https://getkirby.com/image.jpg'
            ],
            'type' => 'image',
        ];

        return $this->assertSame($expected, $this->schema->img($element));
    }

    public function testImgWithLinkAndCaption()
    {
        $html = <<<HTML
            <figure>
                <a href="https://getkirby.com">
                    <img src="https://getkirby.com/image.jpg" alt="Test">
                    <figcaption>Test</figcaption>
                </a>
            </figure>
        HTML;

        $element  = $this->element($html, '//img');
        $expected = [
            'content' => [
                'alt'      => 'Test',
                'caption'  => 'Test',
                'link'     => 'https://getkirby.com',
                'location' => 'web',
                'src'      => 'https://getkirby.com/image.jpg'
            ],
            'type' => 'image',
        ];

        return $this->assertSame($expected, $this->schema->img($element));
    }

    public function testList()
    {
        $html = <<<HTML
            <ul>
                <li>A</li>
                <li>B</li>
                <li>C</li>
            </ul>
        HTML;

        $element  = $this->element($html, '//ul');
        $expected = '<ul><li>A</li><li>B</li><li>C</li></ul>';

        return $this->assertSame($expected, $this->schema->list($element));
    }

    public function testListNested()
    {
        $html = <<<HTML
            <ul>
                <li>A</li>
                <li>
                    <ol>
                        <li>1</li>
                        <li>2</li>
                        <li>3</li>
                    </ol>
                </li>
                <li>C</li>
            </ul>
        HTML;

        $element  = $this->element($html, '//ul');
        $expected = '<ul><li>A</li><li><ol><li>1</li><li>2</li><li>3</li></ol></li><li>C</li></ul>';

        return $this->assertSame($expected, $this->schema->list($element));
    }

    public function testPre()
    {
        $html = <<<HTML
            <pre>Code</pre>
        HTML;

        $element  = $this->element($html, '//pre');
        $expected = [
            'content' => [
                'code'     => 'Code',
                'language' => 'text'
            ],
            'type' => 'code',
        ];

        return $this->assertSame($expected, $this->schema->pre($element));
    }

    public function testPreWithCode()
    {
        $html = <<<HTML
            <pre><code>Code</code></pre>
        HTML;

        $element  = $this->element($html, '//pre');
        $expected = [
            'content' => [
                'code'     => 'Code',
                'language' => 'text'
            ],
            'type' => 'code',
        ];

        return $this->assertSame($expected, $this->schema->pre($element));
    }

    public function testPreWithLanguage()
    {
        $html = <<<HTML
            <pre><code class="language-php">Code</code></pre>
        HTML;

        $element  = $this->element($html, '//pre');
        $expected = [
            'content' => [
                'code'     => 'Code',
                'language' => 'php'
            ],
            'type' => 'code',
        ];

        return $this->assertSame($expected, $this->schema->pre($element));
    }

    /**
     * @covers ::skip
     */
    public function testSkip()
    {
        return $this->assertSame(['head', 'meta', 'script', 'style'], $this->schema->skip());
    }

    public function testTable()
    {
        $html = <<<HTML
            <table>
                <tr><th>Column A</th><th>Column A</th></tr>
                <tr><td>Cell A</td><td>Cell B</td></tr>
            </table>
        HTML;

        $element  = $this->element($html, '//table');
        $expected = [
            'content' => [
                'text' => '<table><tr><th>Column A</th><th>Column A</th></tr><tr><td>Cell A</td><td>Cell B</td></tr></table>',
            ],
            'type' => 'markdown',
        ];

        return $this->assertSame($expected, $this->schema->table($element));
    }
}

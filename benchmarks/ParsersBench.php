<?php

use PPC\CharStream;
use function PPC\Parsers\alnum;
use function PPC\Parsers\alpha;
use function PPC\Parsers\eof;
use function PPC\Parsers\eol;
use function PPC\Parsers\in;
use function PPC\Parsers\is;
use function PPC\Parsers\not;
use function PPC\Parsers\numeric;
use function PPC\Parsers\regex;
use function PPC\Parsers\space;

/**
 * @Warmup(5)
 * @Revs(100)
 * @Iterations(10)
 */
class ParsersBench
{
    public function benchIs()
    {
        $parser = is('a');
        $stream = new CharStream('abc');

        $parser($stream);
    }

    public function benchNot()
    {
        $parser = not('a');
        $stream = new CharStream('def');

        $parser($stream);
    }

    public function benchRegex()
    {
        $parser = regex('/\w/');
        $stream = new CharStream('abc');

        $parser($stream);
    }

    public function benchIn()
    {
        $parser = in(['a', 'b', 'c']);
        $stream = new CharStream('abc');

        $parser($stream);
    }

    public function benchEof()
    {
        $parser = eof();
        $stream = new CharStream('');

        $parser($stream);
    }

    public function benchEol()
    {
        $parser = eol();
        $stream = new CharStream("\n");

        $parser($stream);
    }

    public function benchAlpha()
    {
        $parser = alpha();
        $stream = new CharStream('abc');

        $parser($stream);
    }

    public function benchAlnum()
    {
        $parser = alnum();
        $stream = new CharStream('abc');

        $parser($stream);
    }

    public function benchSpace()
    {
        $parser = space();
        $stream = new CharStream("\t");

        $parser($stream);
    }

    public function benchNumeric()
    {
        $parser = numeric();
        $stream = new CharStream('123');

        $parser($stream);
    }
}

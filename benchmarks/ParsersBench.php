<?php

use PPC\CharStream;
use function PPC\Combinators\repeat;
use function PPC\Parsers\alnum;
use function PPC\Parsers\alpha;
use function PPC\Parsers\eof;
use function PPC\Parsers\eol;
use function PPC\Parsers\in;
use function PPC\Parsers\is;
use function PPC\Parsers\manyIs;
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
    public function benchRegex()
    {
        $parser = regex('/\w/');
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
}

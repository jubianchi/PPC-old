<?php

use PPC\CharStream;
use function PPC\Combinators\repeat;
use function PPC\Handlers\merge;
use function PPC\Parsers\is;
use function PPC\Parsers\manyIs;

/**
 * @Warmup(5)
 * @Revs(100)
 * @Iterations(10)
 */
class IsBench
{
    public function benchIs()
    {
        $parser = is('a');
        $stream = new CharStream('abc');

        $parser($stream);
    }

    public function benchManyIs()
    {
        $parser = manyIs('ab');
        $stream = new CharStream('ababc');

        $parser($stream);
    }

    public function benchRepeatIs()
    {
        $parser = repeat(is('ab'), merge());
        $stream = new CharStream('ababc');

        $parser($stream);
    }
}

<?php

use PPC\CharStream;
use function PPC\Combinators\repeat;
use function PPC\Handlers\merge;
use function PPC\Parsers\not;
use function PPC\Parsers\manyNot;

/**
 * @Warmup(5)
 * @Revs(100)
 * @Iterations(10)
 */
class NotBench
{
    public function benchNot()
    {
        $parser = not('d');
        $stream = new CharStream('abc');

        $parser($stream);
    }

    public function benchManyNot()
    {
        $parser = manyNot('c');
        $stream = new CharStream('abc');

        $parser($stream);
    }

    public function benchRepeatNot()
    {
        $parser = repeat(not('c'), merge());
        $stream = new CharStream('abc');

        $parser($stream);
    }
}

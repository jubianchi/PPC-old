<?php

use PPC\CharStream;
use function PPC\Combinators\repeat;
use function PPC\Handlers\merge;
use function PPC\Parsers\in;
use function PPC\Parsers\manyIn;

/**
 * @Warmup(5)
 * @Revs(100)
 * @Iterations(10)
 */
class InBench
{
    public function benchIn()
    {
        $parser = in(['a', 'b']);
        $stream = new CharStream('abc');

        $parser($stream);
    }

    public function benchManyIn()
    {
        $parser = manyIn(['a', 'b']);
        $stream = new CharStream('abc');

        $parser($stream);
    }

    public function benchRepeatIn()
    {
        $parser = repeat(in(['a', 'b']), merge());
        $stream = new CharStream('abc');

        $parser($stream);
    }
}

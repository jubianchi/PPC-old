<?php

use PPC\CharStream;
use function PPC\Combinators\repeat;
use function PPC\Handlers\merge;
use function PPC\Parsers\alnum;
use function PPC\Parsers\manyAlnum;

/**
 * @Warmup(5)
 * @Revs(100)
 * @Iterations(10)
 */
class AlnumBench
{
    public function benchAlnum()
    {
        $parser = alnum();
        $stream = new CharStream('ab0-');

        $parser($stream);
    }

    public function benchManyAlnum()
    {
        $parser = manyAlnum();
        $stream = new CharStream('ab0-');

        $parser($stream);
    }

    public function benchRepeatAlnum()
    {
        $parser = repeat(alnum(), merge());
        $stream = new CharStream('ab0-');

        $parser($stream);
    }
}

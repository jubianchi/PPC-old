<?php

use PPC\CharStream;
use function PPC\Combinators\repeat;
use function PPC\Handlers\merge;
use function PPC\Parsers\space;
use function PPC\Parsers\manySpace;

/**
 * @Warmup(5)
 * @Revs(100)
 * @Iterations(10)
 */
class SpaceBench
{
    public function benchSpace()
    {
        $parser = space();
        $stream = new CharStream(" \ta");

        $parser($stream);
    }

    public function benchManySpace()
    {
        $parser = manySpace();
        $stream = new CharStream(" \ta");

        $parser($stream);
    }

    public function benchRepeatSpace()
    {
        $parser = repeat(space(), merge());
        $stream = new CharStream(" \ta");

        $parser($stream);
    }
}

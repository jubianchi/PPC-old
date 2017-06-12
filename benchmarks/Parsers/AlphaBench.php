<?php

use PPC\CharStream;
use function PPC\Combinators\repeat;
use function PPC\Handlers\merge;
use function PPC\Parsers\alpha;
use function PPC\Parsers\manyAlpha;

/**
 * @Warmup(5)
 * @Revs(100)
 * @Iterations(10)
 */
class AlphaBench
{
    public function benchAlpha()
    {
        $parser = alpha();
        $stream = new CharStream('ab0');

        $parser($stream);
    }

    public function benchManyAlpha()
    {
        $parser = manyAlpha();
        $stream = new CharStream('ab0');

        $parser($stream);
    }

    public function benchRepeatAlpha()
    {
        $parser = repeat(alpha(), merge());
        $stream = new CharStream('ab0');

        $parser($stream);
    }
}

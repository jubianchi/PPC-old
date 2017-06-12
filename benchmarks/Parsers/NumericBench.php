<?php

use PPC\CharStream;
use function PPC\Combinators\repeat;
use function PPC\Handlers\merge;
use function PPC\Parsers\numeric;
use function PPC\Parsers\manyNumeric;

/**
 * @Warmup(5)
 * @Revs(100)
 * @Iterations(10)
 */
class NumericBench
{
    public function benchNumeric()
    {
        $parser = numeric();
        $stream = new CharStream('12c');

        $parser($stream);
    }

    public function benchManyNumeric()
    {
        $parser = manyNumeric();
        $stream = new CharStream('12c');

        $parser($stream);
    }

    public function benchRepeatNumeric()
    {
        $parser = repeat(numeric(), merge());
        $stream = new CharStream('12c');

        $parser($stream);
    }
}

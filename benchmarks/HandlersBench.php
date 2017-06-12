<?php

use PPC\CharStream;
use function PPC\Handlers\merge;
use PPC\Slice;

/**
 * @Warmup(5)
 * @Revs(100)
 * @Iterations(10)
 */
class HandlersBench
{
    public function benchMerge()
    {
        $handler = merge();
        $stream = new CharStream('abcdefghijklm');
        $slices = [new Slice(0, 1, $stream), new Slice(2, 1, $stream), new Slice(7, 2, $stream)];

        $handler($slices);
    }
}

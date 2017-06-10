<?php

declare(strict_types=1);

namespace PPC\Handlers;

use PPC\Slice;

function toString(callable $next = null) : callable
{
    $next = $next ?? function (string $string) {
        return $string;
    };

    return function (Slice $slice) use ($next) {
        return $next((string) $slice);
    };
}

function extract(string $item, callable $next = null) : callable
{
    $next = $next ?? function ($result) {
        return $result;
    };

    return function (array $result) use ($item, $next) {
        return $next($result[$item]);
    };
}

function flatMap(callable $next = null)
{
    $next = $next ?? function ($string) {
        return $string;
    };

    return function (array $results) use ($next) {
        return $next(
            array_reduce(
                $results,
                function ($prev, $result) use ($next) {
                    if (is_array($result)) {
                        return array_merge($prev, $result);
                    }

                    $prev[] = $result;

                    return $prev;
                },
                []
            )
        );
    };
}

/**
 * ## Example
 *
 * ```php
 * use PPC\CharStream;
 * use function PPC\Combinators\chain;
 * use function PPC\Handlers\merge;
 * use function PPC\Parsers\is;
 * use function PPC\Parsers\not;
 *
 * $parser = chain([is('a'), not('a')], merge());
 * $stream = new CharStream('abcd');
 *
 * assert($parser($stream) == new \PPC\Slice(0, 2, $stream));
 * assert($stream->current() === 'c');
 * ```
 */
function merge(callable $next = null)
{
    $next = $next ?? function ($string) {
        return $string;
    };

    return function (array $results) use ($next) {
        $first = null;
        $length = array_reduce(
            $results,
            function ($prev, Slice $result) use (&$first) {
                if (null === $first) {
                    $first = $result;
                }

                return $prev + $result->length();
            },
            0
        );

        return $next(new Slice(current($results)->offset(), $length, $first->stream()));
    };
}

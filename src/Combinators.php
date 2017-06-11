<?php

declare(strict_types=1);

namespace PPC\Combinators;

use Exception;
use PPC\CharStream;
use PPC\Slice;

/**
 * Chains several parsers and returns an array of the matched slices.
 *
 * The chain will match only if all chained parsers match. It will stop and raise an error when a prser fails.
 *
 * ## Example
 *
 * ```php
 * use PPC\CharStream;
 * use function PPC\Combinators\chain;
 * use function PPC\Parsers\is;
 * use function PPC\Parsers\not;
 *
 * $parser = chain([is('a'), not('a')]);
 * $stream = new CharStream('abcd');
 *
 * assert($parser($stream) == [new \PPC\Slice(0, 1, $stream), new \PPC\Slice(1, 1, $stream)]);
 * assert($stream->current() === 'c');
 * ```
 */
function chain(array $parsers, callable $next = null) : callable
{
    $next = $next ?? function (array $chain) {
        return $chain;
    };

    return function (CharStream $stream) use ($parsers, $next) {
        $chain = [];

        foreach ($parsers as $parser) {
            $key = null;

            if (is_array($parser)) {
                list($key, $parser) = $parser;
            }

            $result = $parser($stream);

            if (null !== $key) {
                $chain[$key] = $result;
            } else {
                $chain[] = $result;
            }
        }

        return $next($chain);
    };
}

/**
 * ```php
 * use PPC\CharStream;
 * use function PPC\Combinators\repeat;
 * use function PPC\Parsers\is;
 *
 * $parser = repeat(is('a'));
 * $stream = new CharStream('aaabcd');
 *
 * assert($parser($stream) == [new \PPC\Slice(0, 1, $stream), new \PPC\Slice(1, 1, $stream), new \PPC\Slice(2, 1, $stream)]);
 * assert($stream->current() === 'b');
 * ```
 */
function repeat(callable $parser, callable $next = null) : callable
{
    $next = $next ?? function ($repeat) {
        return $repeat;
    };

    return function (CharStream $stream) use ($parser, $next) {
        $repeat = [];
        $error = null;

        while ($stream->valid()) {
            try {
                $repeat[] = $parser($stream);
            } catch (Exception $error) {
                break;
            }
        }

        if (empty($repeat)) {
            $message = 'Could not match repetition';

            if (null !== $error) {
                $message .= ":\n".$error->getMessage();
            }

            throw new Exception($message);
        }

        return $next($repeat);
    };
}

/**
 * ```php
 * use PPC\CharStream;
 * use function PPC\Combinators\optional;
 * use function PPC\Parsers\is;
 *
 * $parser = optional(is('a'));
 * $stream = new CharStream('abcd');
 *
 * assert($parser($stream) == new \PPC\Slice(0, 1, $stream));
 * assert($stream->current() === 'b');
 * ```
 */
function optional(callable $parser, callable $next = null) : callable
{
    $next = $next ?? function ($option) {
        return $option;
    };

    return function (CharStream $stream) use ($parser, $next) {
        try {
            return $next($parser($stream));
        } catch (Exception $e) {
            return $next(new Slice($stream->key(), 0, $stream));
        }
    };
};

function alternatives(array $parsers, callable $next = null) : callable
{
    $next = $next ?? function ($option) {
        return $option;
    };

    return function (CharStream $stream) use ($parsers, $next) {
        $errors = [];

        foreach ($parsers as $k => $parser) {
            try {
                return $next($parser($stream));
            } catch (Exception $error) {
                $errors[] = $error;
            }
        }

        $errors = array_map(function (Exception $error) {
            return $error->getMessage();
        }, $errors);

        throw new Exception(sprintf('None of the alternatives matched: %s', "\n  ".implode("\n  ", $errors)));
    };
};

function boxed(callable $left, callable $content, callable $right, callable $next = null) : callable
{
    $next = $next ?? function ($option) {
        return $option;
    };
    $parser = chain([$left, $content, $right]);

    return function (CharStream $stream) use ($parser, $next) {
        return $next($parser($stream)[1]);
    };
}

function until(callable $parser, callable $next = null) : callable
{
    $next = $next ?? function (Slice $slice) {
        return $slice;
    };

    return function (CharStream $stream) use ($parser, $next) {
        $offset = $stream->key();
        $length = 0;

        while ($stream->valid()) {
            try {
                $before = $stream->key();
                $parser($stream);
                $stream->seek($before);

                break;
            } catch (Exception $error) {
                ++$length;

                $stream->seek($offset + $length);
            }
        }

        return $next(new Slice($offset, $length, $stream));
    };
}

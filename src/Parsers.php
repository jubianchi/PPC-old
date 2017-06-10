<?php

declare(strict_types=1);

namespace PPC\Parsers;

use Exception;
use PPC\CharStream;
use PPC\Slice;

function is(string $word, callable $next = null) : callable
{
    $next = $next ?? function (Slice $slice) {
        return $slice;
    };

    return function (CharStream $stream) use ($word, $next) {
        $length = mb_strlen($word);
        $actual = $stream->cut($stream->key(), $length);

        if ($actual !== $word) {
            throw new Exception(sprintf('Expected "%s", got "%s" at offset %d', $word, $actual, $stream->key()));
        }

        $result = new Slice($stream->key(), $length, $stream);

        $stream->seek($stream->key() + $length);

        return $next($result);
    };
}

function regex(string $regex, callable $next = null) : callable
{
    $next = $next ?? function (Slice $slice) {
        return $slice;
    };

    return function (CharStream $stream) use ($regex, $next) {
        $offset = $stream->key();

        if ($stream->valid() === false || preg_match($regex, $actual = $stream->current()) === 0) {
            throw new Exception(sprintf('Expected character matching "%s", got "%s" at offset %d', $regex, $actual ?? 'EOF', $offset));
        }

        $stream->next();

        return $next(new Slice($offset, 1, $stream));
    };
}

function in(array $words, callable $next = null) : callable
{
    $next = $next ?? function (Slice $slice) {
        return $slice;
    };

    return function (CharStream $stream) use ($words, $next) {
        foreach ($words as $char) {
            $length = strlen($char);
            $actual = $stream->cut($stream->key(), $length);

            if ($actual === $char) {
                $result = new Slice($stream->key(), $length, $stream);

                $stream->seek($stream->key() + $length);

                return $next($result);
            }
        }


        throw new Exception(sprintf('Expected one of "%s", got "%s" at offset %d', implode('", "', $words), $actual, $stream->key()));
    };
}

function not(string $char, callable $next = null) : callable
{
    $next = $next ?? function (Slice $slice) {
        return $slice;
    };

    return function (CharStream $stream) use ($char, $next) {
        $offset = $stream->key();
        $length = strlen($char);
        $actual = $stream->cut($stream->key(), $length);

        if ($stream->current() === $char) {
            throw new Exception(sprintf('Expected anything but "%s", got "%s" at offset "%d"', $char, $actual, $offset));
        }

        $stream->seek($stream->key() + $length);

        return $next(new Slice($offset, $length, $stream));
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

                $stream->next();
            }
        }

        return $next(new Slice($offset, $length, $stream));
    };
}

function eof(callable $next = null) : callable
{
    $next = $next ?? function ($result) {
        return $result;
    };

    return function (CharStream $stream) use (&$parser, $next) {
        $offset = $stream->key();

        $stream->next();

        if ($stream->valid()) {
            throw new Exception(sprintf("Expected EOF, got %s at offset %d: %s", $stream->current(), $stream->key(), $stream->cut($stream->key(), $stream->key() + 5)));
        }

        return new Slice($offset, 0, $stream);
    };
}

function eol(callable $next = null) : callable
{
    return in(
        [
            "\r",
            "\n"
        ],
        $next
    );
}

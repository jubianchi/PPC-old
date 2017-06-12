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

    $length = mb_strlen($word);

    return function (CharStream $stream) use ($word, $length, $next) {
        $slice = new Slice($stream->key(), $length, $stream);

        if ($slice->equals($word) === false) {
            throw new Exception(sprintf('Expected "%s", got "%s" at offset %d', $word, $slice, $slice->offset()));
        }

        $stream->seek($slice->offset() + $slice->length());

        return $next($slice);
    };
}

function not(string $char, callable $next = null) : callable
{
    $next = $next ?? function (Slice $slice) {
        return $slice;
    };

    return function (CharStream $stream) use ($char, $next) {
        $slice = new Slice($stream->key(), 1, $stream);

        if ($slice->equals($char)) {
            throw new Exception(sprintf('Expected anything but "%s", got "%s" at offset "%d"', $char, $slice, $slice->offset()));
        }

        $stream->seek($slice->offset() + $slice->length());

        return $next($slice);
    };
}

function regex(string $regex, callable $next = null) : callable
{
    $next = $next ?? function (Slice $slice) {
        return $slice;
    };

    return function (CharStream $stream) use ($regex, $next) {
        $slice = new Slice($stream->key(), 1, $stream);

        if ($slice->matches($regex) === false) {
            throw new Exception(sprintf('Expected character matching "%s", got "%s" at offset %d', $regex, $slice, $slice->offset()));
        }

        $stream->next();

        return $next($slice);
    };
}

function in(array $words, callable $next = null) : callable
{
    $next = $next ?? function (Slice $slice) {
        return $slice;
    };

    return function (CharStream $stream) use ($words, $next) {
        foreach ($words as $word) {
            $slice = new Slice($stream->key(), mb_strlen($word), $stream);

            if ($slice->equals($word)) {
                $stream->seek($slice->offset() + $slice->length());

                return $next($slice);
            }
        }

        throw new Exception(sprintf('Expected one of "%s" at offset %d', implode('", "', $words), $stream->key()));
    };
}

function notIn(array $chars, callable $next = null) : callable
{
    $next = $next ?? function (Slice $slice) {
            return $slice;
        };

    return function (CharStream $stream) use ($chars, $next) {
        $slice = new Slice($stream->key(), 1, $stream);

        foreach ($chars as $word) {
            if ($slice->equals($word)) {
                throw new Exception(sprintf('Expected none of "%s", got "%s" at offset %d', implode('", "', $chars), $slice, $stream->key()));
            }
        }

        $stream->next();

        return $next($slice);
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
            throw new Exception(sprintf("Expected EOF, got %s at offset %d", $stream->current(), $stream->key()));
        }

        return new Slice($offset, 0, $stream);
    };
}

function eol(callable $next = null) : callable
{
    $next = $next ?? function ($result) {
        return $result;
    };

    return function (CharStream $stream) use ($next) {
        if ($stream->current() === "\n" || $stream->current() === "\r") {
            $slice = new Slice($stream->key(), 1, $stream);
            $stream->next();

            return $next($slice);
        }


        throw new Exception(sprintf('Expected "\n" or "\r", got "%s" at offset %d', $stream->current(), $stream->key()));
    };
}

function space(callable $next = null) : callable
{
    $next = $next ?? function ($result) {
        return $result;
    };

    return function (CharStream $stream) use ($next) {
        if (preg_match('/\s/', $stream->current()) > 0) {
            $slice = new Slice($stream->key(), 1, $stream);
            $stream->next();

            return $next($slice);
        }

        throw new Exception(sprintf('Expected any space character, got "%s" at offset %d', $stream->current(), $stream->key()));
    };
}

function alpha(callable $next = null) : callable
{
    $next = $next ?? function ($result) {
        return $result;
    };

    return function (CharStream $stream) use ($next) {
        if (preg_match('/[[:alpha:]]/u', $stream->current()) > 0) {
            $slice = new Slice($stream->key(), 1, $stream);
            $stream->next();

            return $next($slice);
        }

        throw new Exception(sprintf('Expected any alphabetic character, got "%s" at offset %d', $stream->current(), $stream->key()));
    };
}

function numeric(callable $next = null) : callable
{
    $next = $next ?? function ($result) {
        return $result;
    };

    return function (CharStream $stream) use ($next) {
        if (preg_match('/\d/', $stream->current()) > 0) {
            $slice = new Slice($stream->key(), 1, $stream);
            $stream->next();

            return $next($slice);
        }

        throw new Exception(sprintf('Expected any alphabetic character, got "%s" at offset %d', $stream->current(), $stream->key()));
    };
}

function alnum(callable $next = null) : callable
{
    $next = $next ?? function ($result) {
        return $result;
    };

    return function (CharStream $stream) use ($next) {
        if (preg_match('/[[:alnum:]]/u', $stream->current()) > 0) {
            $slice = new Slice($stream->key(), 1, $stream);
            $stream->next();

            return $next($slice);
        }

        throw new Exception(sprintf('Expected any alphabetic character, got "%s" at offset %d', $stream->current(), $stream->key()));
    };
}

<?php

declare(strict_types=1);

namespace PPC\Controls;

use PPC\CharStream;

function run(callable $parser, callable $next = null) : callable
{
    $next = $next ?? function ($result) {
        return $result;
    };

    return function (CharStream $stream) use ($parser, $next) {
        return $next($parser($stream));
    };
}

function call(&$parser, callable $next = null) : callable
{
    $next = $next ?? function ($result) {
        return $result;
    };

    return function (CharStream $stream) use (&$parser, $next) {
        return $next($parser($stream));
    };
}

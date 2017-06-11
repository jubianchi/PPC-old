<?php

declare(strict_types=1);

namespace PPC\Parser\Log;

use DateTime;
use PPC\CharStream;
use function PPC\Combinators\chain;
use function PPC\Combinators\optional;
use function PPC\Combinators\repeat;
use function PPC\Combinators\until;
use function PPC\Handlers\extract;
use function PPC\Handlers\merge;
use function PPC\Handlers\toString;
use function PPC\Parsers\eof;
use function PPC\Parsers\in;
use function PPC\Parsers\is;
use function PPC\Parsers\numeric;
use function PPC\Parsers\space;

class LogParser
{
    private static $parser;

    public static function parse(CharStream $stream)
    {
        if (null === self::$parser) {
            $space = repeat(space());
            $separator = chain([
                optional($space),
                is('|&|'),
                optional($space),
            ]);
            $numeric = repeat(numeric(), merge());
            $date = chain(
                [
                    [@DAY, $numeric],
                    is('/'),
                    [@MONTH, $numeric],
                    is('/'),
                    [@YEAR, $numeric],
                    $space,
                    [@HOUR, $numeric],
                    is(':'),
                    [@MINUTE, $numeric],
                    is(':'),
                    [@SECOND, $numeric],
                ],
                function ($result) {
                    return new DateTime(sprintf(
                        '%s-%s-%s %s:%s:%s',
                        $result[@YEAR],
                        $result[@MONTH],
                        $result[@DAY],
                        $result[@HOUR],
                        $result[@MINUTE],
                        $result[@SECOND]
                    ));
                }
            );
            $tag = in(['BEGIN', 'ERROR', 'WARNING', 'LOG', 'END'], toString());

            $line = chain(
                [
                    $separator,
                    [@DATE, $date],
                    $separator,
                    [@TAG, $tag],
                    $separator,
                    [@CONTENT, until($separator)]
                ],
                extract([@DATE, @TAG, @CONTENT])
            );

            self::$parser = chain([[@LINES, repeat($line)], eof()], function (array $result) {
                return $result[@LINES];
            });
        }

        $parser = self::$parser;

        return $parser($stream);
    }
}

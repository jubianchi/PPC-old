<?php

declare(strict_types=1);

namespace PPC\Parser\Log;

use DateTime;
use PPC\CharStream;
use function PPC\Combinators\chain;
use function PPC\Combinators\optional;
use function PPC\Combinators\repeat;
use function PPC\Handlers\merge;
use function PPC\Handlers\toString;
use function PPC\Parsers\eof;
use function PPC\Parsers\in;
use function PPC\Parsers\is;
use function PPC\Parsers\regex;
use function PPC\Parsers\until;

class LogParser
{
    private static $parser;

    public static function parse(CharStream $stream)
    {
        if (null === self::$parser) {
            $separator = chain([
                optional(repeat(regex('/\s/'))),
                is('|&|'),
                optional(repeat(regex('/\s/'))),
            ]);
            $date = chain(
                [
                    [@DAY, repeat(regex('/\d/'), merge())],
                    is('/'),
                    [@MONTH, repeat(regex('/\d/'), merge())],
                    is('/'),
                    [@YEAR, repeat(regex('/\d/'), merge())],
                    repeat(regex('/\s/')),
                    [@HOUR, repeat(regex('/\d/'), merge())],
                    is(':'),
                    [@MINUTE, repeat(regex('/\d/'), merge())],
                    is(':'),
                    [@SECOND, repeat(regex('/\d/'), merge())],
                ],
                function ($result) {
                    return new DateTime(sprintf(
                        '%d-%d-%d %d:%d:%d',
                        (string) $result[@YEAR],
                        (string) $result[@MONTH],
                        (string) $result[@DAY],
                        (string) $result[@HOUR],
                        (string) $result[@MINUTE],
                        (string) $result[@SECOND]
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
                function (array $result) {
                    return ['date' => $result[@DATE], 'tag' => $result[@TAG], 'content' => $result[@CONTENT]];
                }
            );

            self::$parser = chain([[@LINES, repeat($line)], eof()], function (array $result) {
                return $result[@LINES];
            });
        }

        $parser = self::$parser;

        return $parser($stream);
    }
}

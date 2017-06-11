<?php

declare(strict_types=1);

namespace PPC\Parser\ConventionalChangelog;

use DateTime;
use PPC\CharStream;
use function PPC\Combinators\boxed;
use function PPC\Combinators\chain;
use function PPC\Combinators\optional;
use function PPC\Combinators\repeat;
use function PPC\Combinators\until;
use function PPC\Controls\run;
use function PPC\Handlers\extract;
use function PPC\Handlers\flatMap;
use function PPC\Handlers\merge;
use function PPC\Handlers\toString;
use function PPC\Parsers\alpha;
use function PPC\Parsers\eof;
use function PPC\Parsers\eol;
use function PPC\Parsers\in;
use function PPC\Parsers\is;
use function PPC\Parsers\regex;

class ConventionalChangelogParser
{
    private static $parser;

    public static function parse(CharStream $stream)
    {
        if (null === self::$parser) {
            $string = repeat(alpha(), merge(toString()));
            $title = chain(
                [
                    [@TITLE, until(eol(), toString())],
                    eol()
                ],
                extract(@TITLE)
            );
            $body = until(eof(), toString());

            self::$parser = chain(
                [
                    [@TYPE, $string],
                    [@SCOPE, optional(
                        boxed(
                            is('('),
                            $string,
                            is(')')
                        )
                    )],
                    is(':'),
                    [@TITLE, $title],
                    eol(),
                    [@BODY, optional($body)],
                    eof()
                ],
                extract([@TYPE, @SCOPE, @TITLE, @BODY])
            );
        }

        $parser = self::$parser;

        return $parser($stream);
    }
}

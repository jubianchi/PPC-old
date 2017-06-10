<?php

// https://www.reddit.com/r/dailyprogrammer/comments/6eerfk/20170531_challenge_317_intermediate_counting/

declare(strict_types=1);

namespace PPC\Parser\Chemical;

use PPC\CharStream;
use function PPC\Combinators\alternatives;
use function PPC\Combinators\boxed;
use function PPC\Combinators\chain;
use function PPC\Combinators\optional;
use function PPC\Combinators\repeat;
use function PPC\Controls\call;
use function PPC\Controls\run;
use function PPC\Handlers\flatMap;
use function PPC\Handlers\merge;
use function PPC\Handlers\toString;
use function PPC\Parsers\is;
use function PPC\Parsers\regex;
use PPC\Slice;

class ChemicalElement
{
    public $molecule;
    public $quantifier;
}

class ChemicalParser
{
    private static $parser;

    public static function parse(CharStream $stream)
    {
        if (null === self::$parser) {
            $molecule = chain([regex('/[A-Z]/'), optional(regex('/[a-z]/'))], merge(toString()));
            $quantifier = optional(repeat(regex('/\d/'), merge()), function (Slice $result) {
                return (int) (((string) $result) ?: 1);
            });
            $element = chain(
                [
                    $molecule,
                    optional($quantifier)
                ],
                flatMap(function (array $results) {
                    $element = new ChemicalElement();
                    $element->molecule = $results[0];
                    $element->quantifier = $results[1];

                    return $element;
                })
            );
            $group = chain(
                [
                    boxed(is('('), call($formula), is(')'), flatMap()),
                    optional($quantifier)
                ],
                function (array $result) {
                    foreach ($result[0] as $element) {
                        $element->quantifier *= end($result);
                    }

                    return $result[0];
                }
            );
            $formula = repeat(alternatives([$element, $group]), flatMap());

            self::$parser = run($formula, function (array $result) {
                $elements = [];

                foreach ($result as $element) {
                    if (isset($elements[$element->molecule]) === false) {
                        $elements[$element->molecule] = 0;
                    }

                    $elements[$element->molecule] += $element->quantifier;
                }

                ksort($elements);

                return $elements;
            });
        }

        $parser = self::$parser;

        return $parser($stream);
    }
}

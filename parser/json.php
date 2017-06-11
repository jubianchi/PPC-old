<?php

declare(strict_types=1);

namespace PPC\Parser\Json;

use JsonSerializable;
use PPC\CharStream;
use function PPC\Combinators\alternatives;
use function PPC\Combinators\boxed;
use function PPC\Combinators\chain;
use function PPC\Combinators\optional;
use function PPC\Combinators\repeat;
use function PPC\Controls\call;
use function PPC\Controls\run;
use function PPC\Handlers\extract;
use function PPC\Handlers\flatMap;
use function PPC\Handlers\merge;
use function PPC\Handlers\toString;
use function PPC\Parsers\eof;
use function PPC\Parsers\in;
use function PPC\Parsers\is;
use function PPC\Parsers\not;
use function PPC\Parsers\numeric;
use function PPC\Parsers\regex;
use function PPC\Parsers\space;

class JsonObject implements JsonSerializable
{
    public $properties;

    public function __set($property, $value)
    {
        $this->properties[stripslashes($property)] = $value;
    }

    public function jsonSerialize()
    {
        return $this->properties;
    }
}

class JsonArray implements JsonSerializable
{
    public $items = [];

    public function jsonSerialize()
    {
        return $this->items;
    }
}

class JsonNull implements JsonSerializable
{
    public function jsonSerialize()
    {
        return null;
    }
}
class JsonTrue implements JsonSerializable
{
    public function jsonSerialize()
    {
        return true;
    }
}
class JsonFalse implements JsonSerializable
{
    public function jsonSerialize()
    {
        return false;
    }
}
class JsonString implements JsonSerializable
{
    public $value = '';

    public function jsonSerialize()
    {
        return $this->value;
    }
}

class JsonParser
{
    private static $parser;

    public static function parse(CharStream $stream)
    {
        if (null === self::$parser) {
            $spaces = repeat(space());
            $rawString = boxed(
                is('"'),
                repeat(
                    alternatives([
                        is('\\'),
                        is('\"'),
                        not('"')
                    ])
                ),
                is('"'),
                merge(toString())
            );
            $string = run($rawString, function ($result) {
                $string = new JsonString();
                $string->value = $result;

                return $string;
            });
            $integer = chain(
                [
                    [@SIGN, optional(in(['+', '-']))],
                    [@HEAD, regex('/[0-9]/')],
                    [@TAIL, optional(repeat(numeric(), merge()))]
                ],
                function (array $result) {
                    $int = intval($result[@HEAD] . $result[@TAIL]);

                    if ('-' === (string) $result[@SIGN]) {
                        return 0 - $int;
                    }

                    return $int;
                }
            );
            $scalar = alternatives([
                $string,
                is('null', function () {
                    return new JsonNull();
                }),
                is('true', function () {
                    return new JsonTrue();
                }),
                is('false', function () {
                    return new JsonFalse();
                }),
                chain(
                    [
                        [@INTEGER, $integer],
                        [@DECIMAL, optional(
                            chain(
                                [
                                    is('.'),
                                    repeat(numeric(), merge())
                                ],
                                function (array $result) {
                                    return floatval('.'.$result[1]);
                                }
                            )
                        )],
                        [@EXP, optional(
                            chain(
                                [
                                    in(['e', 'E']),
                                    [@INTEGER, $integer],
                                ],
                                extract(@INTEGER)
                            )
                        )],
                    ],
                    function (array $result) {
                        return doubleval(($result[@INTEGER] + (is_numeric($result[@DECIMAL]) ? $result[@DECIMAL] : 0)).(is_numeric($result[@EXP]) ? 'e'.$result[@EXP] : ''));
                    }
                )
            ]);
            $pair = chain(
                [
                    [@KEY, $rawString],
                    optional($spaces),
                    is(':'),
                    optional($spaces),
                    [@VALUE, alternatives([$scalar, call($hash), call($array)])]
                ],
                extract([@KEY, @VALUE])
            );
            $hash = boxed(
                chain([optional($spaces), is('{'), optional($spaces)]),
                optional(
                    chain(
                        [
                            run($pair, function (array $pair) {
                                return [$pair];
                            }),
                            optional(
                                repeat(
                                    chain(
                                        [
                                            optional($spaces),
                                            is(','),
                                            optional($spaces),
                                            [@PAIR, $pair]
                                        ],
                                        extract(@PAIR)
                                    )
                                )
                            )
                        ],
                        flatMap()
                    )
                ),
                chain([optional($spaces), is('}'), optional($spaces)]),
                function ($results) {
                    $object = new JsonObject();

                    foreach ($results as $entry) {
                        $object->{$entry[@KEY]} = $entry[@VALUE];
                    }

                    return $object;
                }
            );
            $array = boxed(
                chain([optional($spaces), is('['), optional($spaces)]),
                optional(
                    chain(
                        [
                            alternatives([$scalar, call($hash), call($array)], function ($result) {
                                return [$result];
                            }),
                            optional(
                                repeat(
                                    chain(
                                        [
                                            optional($spaces),
                                            is(','),
                                            optional($spaces),
                                            [@VALUE, alternatives([$scalar, call($hash), call($array)])],
                                        ],
                                        extract(@VALUE)
                                    )
                                )
                            )
                        ],
                        flatMap()
                    )
                ),
                chain([optional($spaces), is(']'), optional($spaces)]),
                function ($results) {
                    $object = new JsonArray();
                    $object->items = array_filter(
                        $results,
                        function ($result) {
                            return !$result instanceof \PPC\Slice;
                        }
                    );

                    return $object;
                }
            );

            self::$parser = chain([[@JSON, alternatives([$scalar, $array, $hash])], eof()], extract(@JSON));
        }

        $parser = self::$parser;

        return $parser($stream);
    }
}

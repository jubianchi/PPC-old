<?php

declare(strict_types=1);

use PPC\CharStream;
use PPC\Parser\Json\JsonParser;

require_once __DIR__ . '/../vendor/autoload.php';

$start = microtime(true);
$a = JsonParser::parse(new CharStream('{"a":1,"b":null,"c":true,"d":{"x":"a","b":[1,2,3]}}'));
var_dump('PPC: ' . (microtime(true) - $start));
var_dump(json_decode(json_encode($a)) == json_decode('{"a":1,"b":null,"c":true,"d":{"x":"a","b":[1,2,3]}}'));

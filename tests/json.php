<?php

declare(strict_types=1);

use PPC\CharStream;
use PPC\Parser\Json\JsonParser;

require_once __DIR__ . '/../vendor/autoload.php';

$stream = new CharStream(file_get_contents(__DIR__ . '/../composer.json'));

$start = microtime(true);
$a = JsonParser::parse($stream);
var_dump('Time: ' . (microtime(true) - $start));
var_dump(json_decode(json_encode($a)) == json_decode(file_get_contents(__DIR__ . '/../composer.json')));

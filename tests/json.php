<?php

declare(strict_types=1);

use PPC\CharStream;
use PPC\Parser\Json\JsonParser;

require_once __DIR__ . '/../vendor/autoload.php';

$json = <<<JSON
{
    "name": "jubianchi/ppc",
    "license": "MIT",
    "authors": [
        {
            "name": "jubianchi",
            "email": "contact@jubianchi.fr"
        }
    ],
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Hywan/Kitab"
        }
    ],
    "autoload": {
        "files": [
            "src/Combinators.php",
            "src/Controls.php",
            "src/Handlers.php",
            "src/Parsers.php"
        ],
        "classmap": [
            "parser/"
        ]
    }
}
JSON;

$start = microtime(true);
$a = JsonParser::parse(new CharStream($json));
var_dump('PPC: ' . (microtime(true) - $start));
var_dump(json_decode(json_encode($a)) == json_decode($json));

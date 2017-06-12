<?php

declare(strict_types=1);

use PPC\CharStream;
use PPC\Parser\ConventionalChangelog\ConventionalChangelogParser;

require_once __DIR__ . '/../vendor/autoload.php';

$log = <<<LOG
feat(parsers): Add conventional changelog parser

And it handles message body

Even on multiline


LOG;


$stream = new CharStream($log);

$start = microtime(true);
ConventionalChangelogParser::parse($stream);
var_dump('PPC: ' . (microtime(true) - $start));

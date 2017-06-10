<?php

declare(strict_types=1);

use PPC\CharStream;
use PPC\Parser\Chemical\ChemicalParser;

require_once __DIR__ . '/../vendor/autoload.php';

$stream = new CharStream('C6H12O6CCl2F2NaHCO3C4H8(OHPbCl(NH3C6H12O6CCl2F2NaHCO3C4H8(OHPbCl(NH3)2(COOH)2)2)2(COOH)2)2');

var_dump(ChemicalParser::parse($stream));
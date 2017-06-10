<?php

declare(strict_types=1);

use PPC\CharStream;
use PPC\Parser\Log\LogParser;

require_once __DIR__ . '/../vendor/autoload.php';

$log = <<<LOG
|&| 26/04/17 12:56:19 |&| BEGIN |&| /var/pmsipilot/maj/incoming/ca.crt.zip
|&| 26/04/17 12:56:19 |&| ERROR |&| Zip file not protected : /var/pmsipilot/maj/incoming/ca.crt.zip
|&| 26/04/17 13:41:55 |&| BEGIN |&| /var/pmsipilot/maj/incoming/ca.crt.zip
|&| 26/04/17 13:41:55 |&| ERROR |&| Zip file not protected : /var/pmsipilot/maj/incoming/ca.crt.zip
|&| 02/05/17 17:55:33 |&| BEGIN |&| /var/pmsipilot/maj/incoming/update.zip
|&| 02/05/17 17:55:33 |&| LOG |&| Start uncompress
|&| 02/05/17 17:55:33 |&| LOG |&| Check MD5
|&| 02/05/17 17:55:33 |&| LOG |&| Pre-Exec File found
pmsipilot-platform-9.12.0
Welcome to the pre-exec
|&| 02/05/17 17:55:33 |&| LOG |&| No RPM found
|&| 02/05/17 17:55:33 |&| LOG |&| Post-Exec File found
Welcome to the post-exec
|&| 02/05/17 17:55:33 |&| LOG |&| Move update file in /var/pmsipilot/maj/done
|&| 02/05/17 17:55:33 |&| END |&| /var/pmsipilot/maj/incoming/update.zip
|&| 02/05/17 17:57:15 |&| BEGIN |&| /var/pmsipilot/maj/incoming/update.zip
|&| 02/05/17 17:57:15 |&| LOG |&| Start uncompress
|&| 02/05/17 17:57:15 |&| LOG |&| Check MD5
|&| 02/05/17 17:57:15 |&| LOG |&| Pre-Exec File found
pmsipilot-platform-9.12.0
Welcome to the pre-exec
|&| 02/05/17 17:57:15 |&| LOG |&| No RPM found
|&| 02/05/17 17:57:15 |&| LOG |&| Post-Exec File found
Welcome to the post-exec
|&| 02/05/17 17:57:15 |&| LOG |&| Move update file in /var/pmsipilot/maj/done
|&| 02/05/17 17:57:15 |&| END |&| /var/pmsipilot/maj/incoming/update.zip
LOG;

$stream = new CharStream($log);

var_dump(LogParser::parse($stream));

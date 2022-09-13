<?php

namespace Andygrond\Hugonette;

// Hugonette single access point
// Author: Andygrond 2022

require '../../app/.env.php';
require SYS_DIR .'/vendor/autoload.php';

Env::init(SYS_DIR);
Env::set('mode', OP_MODE);
Log::set(new Logger('myblog.log', 10));

\App\Bootstrap::boot();

require SYS_DIR .'/app/routes.php';

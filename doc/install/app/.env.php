<?php

/** Lumen - Hugonette development environment
  * @Author Andygrond 2022
  */

// error_reporting(-1);

// locales
define('SYS_CURRENT_LANG', 'Polish');
setlocale(LC_ALL, 'pl_PL.UTF8');
date_default_timezone_set('Europe/Warsaw');

// app operating mode [development | production | maintenance]
define('OP_MODE', 'development');
define('SYS_DIR', dirname(__DIR__));

// project constants
// caution! all paths should be defined without the ending '/'

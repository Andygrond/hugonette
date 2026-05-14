<?php

/** Hugonette single access point
* @author: Andygrond 2026
*/

// Start autoloader
require '../myblog/vendor/autoload.php';

// Configurator
\App\Bootstrap::boot();

// Router
require SYS_DIR .'/app/routes.php';

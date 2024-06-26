<?php

/** Standard initial environment attributes for Hugonette
 * @author Andygrond 2023
 */

$uriBase = dirname($_SERVER['SCRIPT_NAME']);
($uriBase >1) or $uriBase = ''; // in subfolder or in documentroot

return [
  // App mode: [ development | production | maintenance ]
  'mode' => 'production',
  // Intended view manner [ plain | latte | json | upload | redirect ]
  'view' => 'plain',
  // Namespaces of functionalities
  'namespace' => [
    'presenter' => 'App\\Presenters\\',
    'view' => 'Andygrond\\Hugonette\\Views\\',
    'db' => 'App\\Library\\Db\\',
  ],

  // Base paths
  'base' => [
    // base path for route (subfolder of document root)
    'uri' => $uriBase,
    // base path for static templates (subfolder of static base)
    'template' => $_SERVER['DOCUMENT_ROOT'] .'/static' .$uriBase,
  ],

  // hidden environment attributes
  'hidden' => [
    // File locations
    'file' => [
      // Browser identification table
      'bots' => false,
      // error template
      'error' => '/index.html',
      // Encryption key
      'key' => '/.secure/key.php',
      // DB credentials
      'db' => '/app/config/db.data',
      // SMB credentials
      'smb' => '/app/config/ad.data',
      // SMTP credentials
      'smtp' => '/app/config/ad.data',
    ],
  ],
];

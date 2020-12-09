<?php

/** Standard initial environment attributes for Hugonette
 * @author Andygrond 2020
 */

$uriBase = dirname($_SERVER['SCRIPT_NAME']);

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
      'bots' => __DIR__ .DIRECTORY_SEPARATOR .'bots.ini',
      // Encryption key should be prepended by it's directory before use
      'key' => DIRECTORY_SEPARATOR .'.secure' .DIRECTORY_SEPARATOR .'key.php',
      // DbFacory credentials data path relative to system dir
      'access' => DIRECTORY_SEPARATOR .'app' .DIRECTORY_SEPARATOR .'config' .DIRECTORY_SEPARATOR .'access.data',
    ],
  ],
];

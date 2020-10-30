<?php

namespace Andygrond\Hugonette;

/* Page state attributes for Hugonette
 * @author Andygrond 2020
**/

class Page
{
  // page attributes
  public $attrib = [
    'view' => 'plain',  // view mode [ plain | latte | json | upload | redirect ]
    'presenterNamespace' => 'App\Presenters',
  ];

  private $trace = [];  // trace of matched routes
  private $requestPath; // base for pattern comparison

  // $sysDir - Nette framework folder
  public function __construct(string $sysDir)
  {
    [ $path ] = explode('?', $_SERVER['REQUEST_URI']);
    $this->requestPath = rtrim($path, '/');

    $this->setBase($sysDir);

    $this->setGroupRequest();
  }

  // run presenter instance and exit if truly presented
  public function run(string $presenter)
  {
    // keep trace of matched routes for the request
    $lineNo = debug_backtrace()[1]['line'];
    $this->trace[$lineNo] = $presenter;
    $this->attrib['trace'] = $this->trace;

    // call Presenter
    PresenterFactory::create($presenter, $this->attrib);
  }

  // calculate template file name based on the URL
  // return = file exists
  public function template(): bool
  {
    $this->attrib['base']['template'] = '';
    $template = $this->attrib['base']['request'] .$this->attrib['request'][0];
    $template .= (substr($template, -1) == '/')? 'index.html' : '.html';

    if (is_file($this->attrib['base']['static'] .$template)) {
      $this->attrib['template'] = $template;
      return true;
    }
    return false;
  }

  // set base folders
  // $sysDir - System or Nette framework folder
  protected function setBase(string $sysDir)
  {
    $request = dirname($_SERVER['SCRIPT_NAME']);
    $this->attrib['base'] = [
      'request' => $request,  // base path for route (subfolder of document root)
      'static' => $_SERVER['DOCUMENT_ROOT'] .'/static',  // path to rendered static site (containing template base)
      'template' => $request, // base path for static template (subfolder of static base)
      'system' => $sysDir,    // path to Nette framework
    ];
  }

  // calculate requestBase for a group and request URI variables
  public function setGroupRequest(string $groupBase = '')
  {
    if ($groupBase) {
      $this->attrib['base']['request'] .= $groupBase; // base URL set for current route group
    }

    $path = substr($this->requestPath, strlen($this->attrib['base']['request']));
    $ending = '/';
    if (substr($this->requestPath, -5) == '.html') {
      $path = substr($path, 0, -5);
      $ending = '';
    }
    $this->attrib['request'] = explode('/', $path);
    $this->attrib['request'][0] = $path .$ending;
  }

}

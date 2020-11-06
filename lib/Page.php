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
    'namespace' => [
      'presenter' => 'App\\Presenters\\',
      'view' => 'Andygrond\\Hugonette\\Views\\',
    ],
  ];

  private $trace = [];  // trace of matched routes

  // $sysDir - framework folder (Nette)
  public function __construct(string $sysDir)
  {
    // set base
    $uriBase = dirname($_SERVER['SCRIPT_NAME']);
    $this->attrib['base'] = [
      'uri' => $uriBase,  // base path for route (subfolder of document root)
      'system' => $sysDir,    // path to Nette framework
      'template' => $_SERVER['DOCUMENT_ROOT'] .'/static' .$uriBase, // base path for static template (subfolder of static base)
    ];

    // set request
    [ $path ] = explode('?', $_SERVER['REQUEST_URI']);
    $isHtml = (substr($path, -5) == '.html');
    $path = $isHtml? substr($path, strlen($uriBase), -5) : substr(rtrim($path, '/'), strlen($uriBase)) .'/';
// zrobiłem rozróżnienie: gdy jest to katalog ma slash na końcu - plik nie ma - czy to jest potrzebne?
    $this->attrib['request'] = [
      'group' => '',    // router group base
      'item' => $path,  // request details (path in group)
      'parts' => explode('/', trim($path, '/')),
    ];
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
  public function template(): bool  // todo!!! to nie powinno działac po ostatnich zmianach
  {
    $this->attrib['base']['template'] = '';
    $template = $this->attrib['base']['uri'] .$this->attrib['request']['item'];
    $template .= (substr($template, -1) == '/')? 'index.html' : '.html';

    if (is_file($this->attrib['base']['static'] .$template)) {
      $this->attrib['template'] = $template;
      return true;
    }
    return false;
  }

  // calculate request for a group
  public function setGroupRequest(string $groupBase)
  {
    $this->attrib['request']['group'] .= $groupBase; // base URL for current route group
    $this->attrib['request']['item'] = substr($this->attrib['request']['item'], strlen($groupBase));
  }

}

<?php

namespace Andygrond\Hugonette;

/* Page state calculations for Hugonette
 * @author Andygrond 2020
**/

class Page
{
  private $trace = [];  // trace of matched routes

  // $sysDir - framework folder (Nette)
  public function __construct(string $sysDir)
  {
    // set base
    $uriBase = dirname($_SERVER['SCRIPT_NAME']);
    Env::set('base', [
      'uri' => $uriBase,  // base path for route (subfolder of document root)
      'system' => $sysDir,    // path to Nette framework
      'template' => $_SERVER['DOCUMENT_ROOT'] .'/static' .$uriBase, // base path for static template (subfolder of static base)
    ]);

    // set request
    [ $path ] = explode('?', urldecode($_SERVER['REQUEST_URI']));
    $isHtml = (substr($path, -5) == '.html');
    $path = $isHtml? substr($path, strlen($uriBase), -5) : substr(rtrim($path, '/'), strlen($uriBase)) .'/';
// zrobiłem rozróżnienie: gdy jest to katalog ma slash na końcu - plik nie ma - czy to jest potrzebne?
    Env::set('request', [
      'group' => '',    // router group base
      'item' => $path,  // request details (path in group)
      'parts' => explode('/', trim($path, '/')),
    ]);
  }

  // run presenter instance and exit if truly presented
  public function run(string $presenter)
  {
    // keep trace of matched routes for the request
    $lineNo = debug_backtrace()[1]['line'];
    $this->trace[$lineNo] = $presenter;
    Env::set('trace', $this->trace);

    // call Presenter
    PresenterFactory::create($presenter, Env::get());
  }

  // calculate template file name based on the URL
  // return = file exists
  public function template(): bool  // TODO!!! to nie powinno działac po ostatnich zmianach
  {
//    Env::set('base.template', '');
    $template = Env::get('base.uri') .Env::get('request.item');
    $template .= (substr($template, -1) == '/')? 'index.html' : '.html';

    if (is_file(Env::get('base.static') .$template)) {
      Env::set('template', $template);
      return true;
    }
    return false;
  }

  // calculate request for a group
  public function setGroupRequest(string $groupBase)
  {
    Env::append('request.group', $groupBase); // base URL for current route group
    Env::set('request.item', substr(Env::get('request.item'), strlen($groupBase)));
  }

}

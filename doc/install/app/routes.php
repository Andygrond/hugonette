<?php

namespace Andygrond\Hugonette;

/** Application Routes for Hugonette
  * @Author: Andygrond 2022
  */

$route = new Route();

Log::enable('tracy'); // enable debugging tool
// Log::enable('output');	// Tracy Output Debugger

new Session();

Env::set('view', 'json'); // JSON view
// your JSON API here - for use with AJAX

Env::set('view', 'latte');  // Latte view
// your html requests here
$route->get('/', 'Examples'); // route to Examples presenter
$route->get('/(\w+)/.*', 'MyBlog');


$route->notFound('Error:404'); // catch all

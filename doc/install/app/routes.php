<?php

namespace Andygrond\Hugonette;

/** Application Routes for Hugonette
  * @Author: Andygrond 2022
  */

// Log::enable('output');	// Tracy Output Debugger

$route = new Route();

Log::enable('tracy');
new Session();

Env::set('view', 'json');
// here place your JSON API - for use with AJAX

Env::set('view', 'latte');
// here place your html requests
$route->get('/', 'Homepage');
$route->get('/(\w+)/.*', 'MyBlog');

// catch all
$route->notFound('Error:404');

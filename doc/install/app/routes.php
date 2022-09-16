<?php

namespace Andygrond\Hugonette;

/** Application Routes for Hugonette
* @author: Andygrond 2022
*/

Log::set(new Logger('myblog.log', 3));
Log::enable('tracy'); // enable debugging tool
// Log::enable('output');	// Tracy Output Debugger

$route = new Route();
new Session();

// REST services
Env::set('view', 'json');
$route->get('/api/(\w+)/.*', 'Error:200');

// Latte pages
Env::set('view', 'latte');
$route->get('/', 'Examples'); // route to Examples presenter
$route->get('/(\w+)/.*', 'MyBlog');

// Catch all
$route->notFound('Error:404');

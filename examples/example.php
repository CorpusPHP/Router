<?php

require('vendor/autoload.php');

// $_SERVER => ['REQUEST_METHOD' => [ 'method' => 'POST' ]]
$router = new \Corpus\Router\HttpRouter('\\Corpus\\Controllers', $_SERVER);


$route = $router->match('test/controller:action');

// $route =
//	[
//		'controller' => '\\Corpus\\Controllers\\test\\controller',
//		'action'     => 'action',
//		'options'    => [],
//		'request'    => [ 'method' => 'POST' ],
//	]


# ----------------


$route = $router->match('test/controller?query=whatwhat');

// $route =
//	[
//		'controller' => '\\Corpus\\Controllers\\test\\controller',
//		'action'     => NULL,
//		'options'    => [ 'query'  => 'whatwhat' ],
//		'request'    => [ 'method' => 'POST' ],
//	]


# ----------------


$route = $router->match($_SERVER['REQUEST_URI']);

// $route = Current Request


# ----------------


$url = $router->generate('myNamespace\\admin', 'index');

// $url = '/myNamespace/admin:index'


# ----------------


$url = $router->generate('\\Corpus\\Controllers\\myNamespace\\admin', 'index');

// $url = '/myNamespace/admin:index'


# ----------------


try {
	$url = $router->generate('\\Invalid\\Absolute\\Controller', 'index');
}catch (\Corpus\Router\Exceptions\NonRoutableException $e) {
	$url = 'fail';
}

// $url = 'fail'


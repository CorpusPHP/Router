# Corpus Router

[![Latest Stable Version](https://poser.pugx.org/corpus/router/v/stable.png)](https://packagist.org/packages/corpus/router)
[![License](https://poser.pugx.org/corpus/router/license.png)](https://packagist.org/packages/corpus/router)
[![Build Status](https://travis-ci.org/CorpusPHP/Router.svg?branch=master)](https://travis-ci.org/CorpusPHP/Router)

A Simple Collection of Routers

## Requirements

- PHP 5.3.0+

## Installing

Corpus Router is available through Packagist via Composer.

```json
{
	"require": {
		"corpus/router": "dev-master",
	}
}

```

## Usage

### HttpRouter

```php
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


```

## Documentation

### Class: HttpRouter \[ `\Corpus\Router` \]

#### Method: `HttpRouter->__construct($root_namespace [, $server = array()])`

##### Parameters:

- ***string*** `$root_namespace`
- ***array*** `$server` - The $_SERVER array - optional



---

#### Method: `HttpRouter->match($path)`

##### Parameters:

- ***string*** `$path`


##### Returns:

- ***array***


---

#### Method: `HttpRouter->generate($controller [, $action = null [, $options = array()]])`

##### Parameters:

- ***string*** | ***object*** `$controller` - Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'
- ***string*** | ***null*** `$action`
- ***array*** | ***null*** `$options`


##### Returns:

- ***string***


---

#### Method: `HttpRouter->getNamespace()`

Return the canonicalized namespace prefix  
  


##### Returns:

- ***String***


### Class: CliRouter \[ `\Corpus\Router` \]

#### Method: `CliRouter->__construct($root_namespace [, $arguments = array()])`

##### Parameters:

- ***string*** `$root_namespace` - The namespace prefix the controllers will be under



---

#### Method: `CliRouter->match($path)`

##### Parameters:

- ***string*** `$path`


##### Returns:

- ***array***


---

#### Method: `CliRouter->getNamespace()`

Return the canonicalized namespace prefix  
  


##### Returns:

- ***String***


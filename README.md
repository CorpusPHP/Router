# Corpus Router

[![Latest Stable Version](https://poser.pugx.org/corpus/router/version)](https://packagist.org/packages/corpus/router)
[![License](https://poser.pugx.org/corpus/router/license)](https://packagist.org/packages/corpus/router)
[![Build Status](https://travis-ci.org/CorpusPHP/Router.svg?branch=master)](https://travis-ci.org/CorpusPHP/Router)


A Simple Collection of Routers

## Requirements

- **php**: >=5.3

## Installing

Install the latest version with:

```bash
composer require 'corpus/router'
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

### Class: \Corpus\Router\HttpRouter

```php
<?php
namespace Corpus\Router;

class HttpRouter {
	const ACTION = 'action';
	const CONTROLLER = 'controller';
	const OPTIONS = 'options';
}
```

#### Method: HttpRouter->__construct

```php
function __construct($root_namespace [, $server = array()])
```

##### Parameters:

- ***string*** `$root_namespace`
- ***array*** `$server` - The $_SERVER array - optional

---

#### Method: HttpRouter->match

```php
function match($path)
```

##### Parameters:

- ***string*** `$path`

##### Returns:

- ***array*** | ***false***

---

#### Method: HttpRouter->generate

```php
function generate($controller [, $action = null [, $options = array()]])
```

##### Parameters:

- ***string*** | ***object*** `$controller` - Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'
- ***string*** | ***null*** `$action`
- ***array*** `$options`

##### Returns:

- ***string***

---

#### Method: HttpRouter->getNamespace

```php
function getNamespace()
```

Return the canonicalized namespace prefix

##### Returns:

- ***String***

### Class: \Corpus\Router\CliRouter

```php
<?php
namespace Corpus\Router;

class CliRouter {
	const ARGUMENTS = 'arguments';
	const ACTION = 'action';
	const CONTROLLER = 'controller';
	const OPTIONS = 'options';
}
```

#### Method: CliRouter->__construct

```php
function __construct($root_namespace [, $arguments = array()])
```

##### Parameters:

- ***string*** `$root_namespace` - The namespace prefix the controllers will be under

---

#### Method: CliRouter->match

```php
function match($path)
```

##### Parameters:

- ***string*** `$path`

##### Returns:

- ***array*** | ***false***

---

#### Method: CliRouter->getNamespace

```php
function getNamespace()
```

Return the canonicalized namespace prefix

##### Returns:

- ***String***
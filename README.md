# Corpus Router

[![Latest Stable Version](https://poser.pugx.org/corpus/router/version)](https://packagist.org/packages/corpus/router)
[![License](https://poser.pugx.org/corpus/router/license)](https://packagist.org/packages/corpus/router)
[![CI](https://github.com/CorpusPHP/Router/workflows/CI/badge.svg?)](https://github.com/CorpusPHP/Router/actions?query=workflow%3ACI)


A Simple Collection of Routers

## Requirements

- **php**: >=7.1

## Installing

Install the latest version with:

```bash
composer require 'corpus/router'
```

## Usage

### HttpRouter

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

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

// ----------------

$route = $router->match('test/controller?query=whatwhat');

// $route =
//	[
//		'controller' => '\\Corpus\\Controllers\\test\\controller',
//		'action'     => NULL,
//		'options'    => [ 'query'  => 'whatwhat' ],
//		'request'    => [ 'method' => 'POST' ],
//	]

// ----------------

$route = $router->match($_SERVER['REQUEST_URI']);

// $route = Current Request

// ----------------

$url = $router->generate('myNamespace\\admin', 'index');

// $url = '/myNamespace/admin:index'

// ----------------

$url = $router->generate('\\Corpus\\Controllers\\myNamespace\\admin', 'index');

// $url = '/myNamespace/admin:index'

// ----------------

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
	public const ACTION = 'action';
	public const CONTROLLER = 'controller';
	public const OPTIONS = 'options';
}
```

#### Method: HttpRouter->__construct

```php
function __construct(string $rootNamespace [, array $server = []])
```

##### Parameters:

- ***array*** `$server` - The $_SERVER array - optional

---

#### Method: HttpRouter->match

```php
function match(string $path) : ?array
```

Match given path to a route array.  
  
A non-null route is not guaranteed to _exist_ - just to be well formed.  
It is up the implementations dispatch mechanism to decide it the route exists  
  
The returned route array the the a shape of  
  
```php  
[  
    // The controller action. Definition varies by router.  
    RouterInterface:ACTION     => 'action',  
  
    // An expected class name based on given rules. Not guaranteed to exist.  
    RouterInterface:CONTROLLER => '\Controller\www\index',  
  
    // Router specific but akin to $_GET - may contain additional options  
    RouterInterface:OPTIONS    => ['key' => 'value'],  
]  
```

Match given path to a route array.  
  
A non-null route is not guaranteed to _exist_ - just to be well formed.  
It is up the implementations dispatch mechanism to decide it the route exists  
  
The returned route array the the a shape of  
  
```php  
[  
    // The controller action. Definition varies by router.  
    RouterInterface:ACTION     => 'action',  
  
    // An expected class name based on given rules. Not guaranteed to exist.  
    RouterInterface:CONTROLLER => '\Controller\www\index',  
  
    // Router specific but akin to $_GET - may contain additional options  
    RouterInterface:OPTIONS    => ['key' => 'value'],  
]  
```

##### Parameters:

- ***string*** `$path` - The path to match against including query string ala `foo/bar.html?param=woo`

##### Returns:

- ***array*** | ***null*** - route array or null on failure to route

---

#### Method: HttpRouter->generate

```php
function generate($controller [, ?string $action = null [, array $options = []]]) : string
```

Generate a URL for the given controller, action and options

##### Parameters:

- ***object*** | ***string*** `$controller` - Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'

---

#### Method: HttpRouter->getNamespace

```php
function getNamespace() : string
```

##### Returns:

- ***string*** - The canonical namespace prefix

### Class: \Corpus\Router\CliRouter

```php
<?php
namespace Corpus\Router;

class CliRouter {
	public const ARGUMENTS = 'arguments';
	public const ACTION = 'action';
	public const CONTROLLER = 'controller';
	public const OPTIONS = 'options';
}
```

#### Method: CliRouter->__construct

```php
function __construct($rootNamespace [, array $arguments = []])
```

##### Parameters:

- ***string*** `$rootNamespace` - The namespace prefix the controllers will be under

---

#### Method: CliRouter->match

```php
function match(string $path) : ?array
```

Match given path to a route array.  
  
A non-null route is not guaranteed to _exist_ - just to be well formed.  
It is up the implementations dispatch mechanism to decide it the route exists  
  
The returned route array the the a shape of  
  
```php  
[  
    // The controller action. Definition varies by router.  
    RouterInterface:ACTION     => 'action',  
  
    // An expected class name based on given rules. Not guaranteed to exist.  
    RouterInterface:CONTROLLER => '\Controller\www\index',  
  
    // Router specific but akin to $_GET - may contain additional options  
    RouterInterface:OPTIONS    => ['key' => 'value'],  
]  
```

##### Parameters:

- ***string*** `$path` - The path to match against including query string ala `foo/bar.html?param=woo`

##### Returns:

- ***array*** | ***null*** - route array or null on failure to route

---

#### Method: CliRouter->getNamespace

```php
function getNamespace() : string
```

##### Returns:

- ***string*** - The canonical namespace prefix
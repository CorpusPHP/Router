<?php

namespace Corpus\Router;

use Corpus\Router\Exceptions\NonRoutableException;
use Corpus\Router\Interfaces\ReversibleRouterInterface;

class HttpRouter extends AbstractRouter implements ReversibleRouterInterface {

	protected $server;

	/**
	 * @param string $root_namespace
	 * @param array  $server The $_SERVER array - optional
	 */
	function __construct( $root_namespace, $server = array() ) {
		$this->server = $server;
		parent::__construct($root_namespace);
	}

	/**
	 * @param string $path
	 * @return array|false
	 */
	public function match( $path ) {
		$parts = parse_url($path);

		$path = empty($parts['path'])  ? '' : $parts['path'];
		$args = empty($parts['query']) ? array() : $this->parseStr( $parts['query'] );

		if( substr($path, -1) == '/' ) {
			$path .= 'index';
		}

		$path = $this->trimSlashes($path);

		if( preg_match(
			'%^
				# offical namespace/class_name regex
				(?P<namespace>(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/)*)
				(?P<class_name>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)
				(?::(?P<action>[a-zA-Z]+))?
			$%sx',
			$path, $regs)
		) {
			$parts = explode('/', $regs['namespace'] . $regs['class_name']);
			array_unshift($parts, $this->namespace);
			$class_name = $this->classNameC14N(implode('\\', $parts));
			if( $class_name === false ) {
				throw new \RuntimeException; // this should never happen
			}

			$return = array(
				self::CONTROLLER => $class_name,
				self::ACTION     => null,
				self::OPTIONS    => $args,
			);

			if( !empty($regs[self::ACTION]) && ctype_alpha($regs[self::ACTION]) ) {
				$return[self::ACTION] = $regs[self::ACTION];
			}

			if( !empty($this->server['REQUEST_METHOD']) ) {
				$return['request']['method'] = strtoupper($this->server['REQUEST_METHOD']);
			}

			return $return;
		}

		return false;
	}

	/**
	 * @param string|object $controller Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'
	 * @param string|null   $action
	 * @param array         $options
	 * @return string
	 * @throws Exceptions\NonRoutableException
	 */
	public function generate( $controller, $action = null, array $options = array() ) {
		$class_name = $this->classNameC14N($controller);

		if( !$class_name ) {
			throw new NonRoutableException("Controller '{$controller}' should be a valid controller class/classname and of namespace {$this->namespace}");
		}

		$parts = explode('\\', $this->trimSlashes($class_name));
		$parts = array_slice($parts, substr_count($this->namespace, '\\') + 1);
		$path  = '/' . implode('/', $parts);

		if( !empty($action) ) {
			$path .= ":{$action}";
		}

		$options = array_filter($options);
		if( count($options) > 0 ) {
			$path .= '?' . http_build_query($options);
		}

		return $path;
	}
}

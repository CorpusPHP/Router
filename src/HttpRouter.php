<?php

namespace Corpus\Router;

use Corpus\Router\Exceptions\NonRoutableException;
use Corpus\Router\Interfaces\ReversibleRouterInterface;

class HttpRouter extends AbstractRouter implements ReversibleRouterInterface {

	protected $server;

	/**
	 * @param array $server The $_SERVER array - optional
	 */
	public function __construct( string $rootNamespace, array $server = [] ) {
		$this->server = $server;

		parent::__construct($rootNamespace);
	}

	public function match( string $path ) : ?array {
		$parts = parse_url($path);

		$path = empty($parts['path']) ? '' : $parts['path'];
		$args = empty($parts['query']) ? [] : $this->parseStr($parts['query']);

		if( substr($path, -1) === '/' ) {
			$path .= 'index';
		}

		$path = $this->trimSlashes($path);

		if( preg_match(
			<<<'REGEX'
%^
	# offical namespace/class_name regex
	(?P<namespace>(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/)*)
	(?P<class_name>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)
	(?::(?P<action>[a-zA-Z]+))?
$%x
REGEX
			,
			$path, $regs)
		) {
			$parts = explode('/', $regs['namespace'] . $regs['class_name']);
			array_unshift($parts, $this->namespace);

			$className = $this->classNameC14N(implode('\\', $parts));
			if( $className === null ) {
				throw new \RuntimeException; // this should never happen
			}

			$return = [
				self::CONTROLLER => $className,
				self::ACTION     => null,
				self::OPTIONS    => $args,
			];

			if( !empty($regs[self::ACTION]) && ctype_alpha($regs[self::ACTION]) ) {
				$return[self::ACTION] = $regs[self::ACTION];
			}

			if( !empty($this->server['REQUEST_METHOD']) ) {
				$return['request']['method'] = strtoupper($this->server['REQUEST_METHOD']);
			}

			return $return;
		}

		return null;
	}

	/**
	 * @param object|string $controller Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'
	 * @throws \Corpus\Router\Exceptions\NonRoutableException
	 */
	public function generate( $controller, ?string $action = null, array $options = [] ) : string {
		$className = $this->classNameC14N($controller);

		if( !$className ) {
			throw new NonRoutableException("Controller '{$controller}' should be a valid controller class/classname and of namespace {$this->namespace}");
		}

		$parts = explode('\\', $this->trimSlashes($className));
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

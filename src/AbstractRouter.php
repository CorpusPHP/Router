<?php

namespace Corpus\Router;

use Corpus\Router\Interfaces\RouterInterface;

abstract class AbstractRouter implements RouterInterface {

	/** @var string */
	protected $namespace;

	/**
	 * @param string $root_namespace The namespace prefix the controllers will be under
	 */
	public function __construct( $root_namespace ) {
		$this->namespace = $this->trimSlashes($root_namespace);
	}

	/**
	 * @param $path
	 * @return string
	 */
	final protected function trimSlashes( $path ) {
		return trim($path, ' /\\');
	}

	/**
	 * Return the canonicalized namespace prefix
	 *
	 * @return string
	 */
	public function getNamespace() {
		return $this->namespace;
	}

	/**
	 * @param string $class_name
	 * @return bool
	 */
	protected function isOfNamespace( $class_name ) {
		return stripos($this->trimSlashes($class_name), $this->namespace . '\\') === 0;
	}

	/**
	 * @param string $query_str
	 * @return array
	 */
	protected function parseStr( $query_str ) {
		parse_str($query_str, $opts);

		return $opts;
	}

	/**
	 * @param $controller
	 * @return bool|string
	 */
	protected function classNameC14N( $controller ) {
		$class_name = false;
		if( is_object($controller) && is_callable($controller) && $this->isOfNamespace($class_name = get_class($controller)) ) {
		} elseif( is_string($controller) && $controller ) {
			if( $this->isOfNamespace($controller) ) {
				$class_name = $this->trimSlashes($controller);
			} elseif( $controller[0] != '\\' ) {
				$class_name = $this->namespace . '\\' . $this->trimSlashes($controller);
			}
		}

		if( $class_name !== false ) {
			$class_name = '\\' . ltrim($class_name, '\\');
		}

		return $class_name;
	}

}

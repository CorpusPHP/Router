<?php

namespace Corpus\Router;

use Corpus\Router\Interfaces\RouterInterface;

abstract class AbstractRouter implements RouterInterface {

	/**
	 * @var String
	 */
	protected $namespace;

	/**
	 * @param string $root_namespace The namespace prefix the controllers will be under
	 */
	function __construct( $root_namespace ) {
		$this->namespace = $this->trimSlashes($root_namespace);
	}

	/**
	 * @param $path
	 * @return string
	 */
	protected final function trimSlashes( $path ) {
		return trim($path, ' /\\');
	}

	/**
	 * Return the canonicalized namespace prefix
	 *
	 * @return String
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
		parse_str( $query_str, $opts );
		return $opts;
	}

}

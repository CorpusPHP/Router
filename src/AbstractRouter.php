<?php

namespace Corpus\Router;

use Corpus\Router\Interfaces\RouterInterface;

abstract class AbstractRouter implements RouterInterface {

	/** @var string */
	protected $namespace;

	/**
	 * @param string $rootNamespace The namespace prefix the controllers will be under
	 */
	public function __construct( string $rootNamespace ) {
		$this->namespace = $this->trimSlashes($rootNamespace);
	}

	final protected function trimSlashes( string $path ) : string {
		return trim($path, ' /\\');
	}

	/**
	 * Return the canonical namespace prefix
	 */
	public function getNamespace() : string {
		return $this->namespace;
	}

	protected function isOfNamespace( string $className ) : bool {
		return stripos($this->trimSlashes($className), $this->namespace . '\\') === 0;
	}

	protected function parseStr( string $queryStr ) : array {
		parse_str($queryStr, $opts);

		return $opts;
	}

	/**
	 * @param object|string $controller
	 */
	protected function classNameC14N( $controller ) : ?string {
		$className = null;
		if( is_object($controller) && is_callable($controller) && $this->isOfNamespace($className = get_class($controller)) ) {
		} elseif( is_string($controller) && $controller ) {
			if( $this->isOfNamespace($controller) ) {
				$className = $this->trimSlashes($controller);
			} elseif( $controller[0] != '\\' ) {
				$className = $this->namespace . '\\' . $this->trimSlashes($controller);
			}
		}

		if( $className !== null ) {
			$className = '\\' . ltrim($className, '\\');
		}

		return $className;
	}

}

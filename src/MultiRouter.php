<?php

namespace Corpus\Router;

use Corpus\Router\Interfaces\RouterInterface;

/**
 * MultiRouter
 *
 * @package Corpus\Router
 */
class MultiRouter implements RouterInterface {

	/**
	 * @var RouterInterface[]
	 */
	protected $routers = array();

	/**
	 * @param ... RouterInterface
	 */
	public function __construct() {
		foreach( func_get_args() as $arg ) {
			$this->addRouter($arg);
		}
	}

	/**
	 * Add a router to the queue
	 *
	 * @param \Corpus\Router\Interfaces\RouterInterface $router
	 */
	public function addRouter( RouterInterface $router ) {
		$this->routers[] = $router;
	}

	/**
	 * @return Interfaces\RouterInterface[]
	 */
	public function getRouters() {
		return $this->routers;
	}

	/**
	 * Loops over routers in the order they were added until a match is found.
	 *
	 * @param string $path
	 * @return array|false
	 */
	public function match( $path ) {
		foreach( $this->routers as $router ) {
			$match = $router->match($path);
			
			if( $match !== false ) {
				return $match;
			}
		}

		return false;
	}
}

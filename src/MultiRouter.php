<?php

namespace Corpus\Router;

use Corpus\Router\Interfaces\RouterInterface;

/**
 * MultiRouter
 */
class MultiRouter implements RouterInterface {

	/** @var RouterInterface[] */
	protected $routers = [];

	public function __construct( RouterInterface ...$routers ) {
		$this->routers = $routers;
	}

	/**
	 * Add a router to the queue
	 */
	public function addRouter( RouterInterface $router ) : void {
		$this->routers[] = $router;
	}

	/**
	 * @return Interfaces\RouterInterface[]
	 */
	public function getRouters() : array {
		return $this->routers;
	}

	/**
	 * Loops over routers in the order they were added until a match is found.
	 */
	public function match( string $path ) : ?array {
		foreach( $this->routers as $router ) {
			$match = $router->match($path);

			if( $match !== null ) {
				return $match;
			}
		}

		return null;
	}

}
